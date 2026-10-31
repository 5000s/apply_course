<?php

// app/Http/Controllers/SmsController.php
namespace App\Http\Controllers;

use App\Models\Apply;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Location;
use App\Services\SmsKubClient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SmsController extends Controller
{
    // ส่ง SMS เตือนการเข้าคอร์ส ล่วงหน้ากี่วันก่อนวันเริ่มคอร์ส
    private const REMINDER_DAYS_BEFORE = 2;

    // แจ้งเตือนแอดมินเมื่อมีผู้สมัครใหม่: 1 SMS ไม่เกินกี่คน
    private const ADMIN_NOTIFY_MAX_PER_SMS = 3;
    // ช่วงเวลาที่อนุญาตให้ส่ง SMS แจ้งแอดมิน (จ-ศ เวลา 08:00-17:00)
    private const ADMIN_NOTIFY_HOUR_START = 8;
    private const ADMIN_NOTIFY_HOUR_END   = 17;

    /**
     * SMS เตือนการเข้าคอร์ส ก่อนวันเริ่มคอร์ส 2 วัน
     * ส่งให้ทุกคนที่ได้รับอีเมลยืนยันแล้ว (state = ยืนยันแล้ว)
     *
     * GET /admin/cron/smsCourseReminder        ส่งจริง
     * GET /admin/cron/smsCourseReminder?dry=1  แสดงรายการที่จะส่ง โดยไม่ส่งจริง
     */
    public function remindUpcomingCourses(Request $request)
    {
        return response()->json($this->runCourseReminder($request->boolean('dry')));
    }

    public function runCourseReminder(bool $dryRun = false): array
    {
        $targetDate = Carbon::today()->addDays(self::REMINDER_DAYS_BEFORE)->toDateString();

        $courses = Course::whereDate('date_start', $targetDate)
            ->where('state', '!=', 'ยกเลิกคอร์ส')
            ->get();

        $locationsEn = Location::pluck('show_name_en', 'id');
        $categoriesEn = CourseCategory::pluck('show_name_en', 'id');

        $result = ['date_start' => $targetDate, 'dry_run' => $dryRun, 'courses' => []];

        foreach ($courses as $course) {
            $applies = Apply::with('member')
                ->where('course_id', $course->id)
                ->where('state', 'ยืนยันแล้ว')
                ->where(fn($q) => $q->whereNull('cancel')->orWhere('cancel', '!=', 1))
                ->get();

            $summary = ['course_id' => $course->id, 'course' => $course->category, 'sent' => 0, 'skipped' => [], 'failed' => []];

            foreach ($applies as $apply) {
                $member = $apply->member;
                $phone = $member
                    ? ($this->normalizeThaiMobile($member->phone) ?? $this->normalizeThaiMobile($member->phone_new))
                    : null;

                if (!$phone) {
                    $summary['skipped'][] = ['apply_id' => $apply->id, 'reason' => 'no Thai mobile number'];
                    continue;
                }

                $isThai = in_array($member->nationality, [null, '', 'ไทย'], true);
                $message = $isThai
                    ? $this->reminderMessageTh($course)
                    : $this->reminderMessageEn($course, $categoriesEn[$course->category_id] ?? null, $locationsEn[$course->location_id] ?? null);

                if ($dryRun) {
                    $summary['skipped'][] = ['apply_id' => $apply->id, 'phone' => $phone, 'message' => $message, 'reason' => 'dry run'];
                    continue;
                }

                // กันส่งซ้ำ ถ้ารัน cron ซ้ำในช่วงเดียวกัน
                $cacheKey = "sms_course_reminder_{$apply->id}";
                if (!Cache::add($cacheKey, true, now()->addDays(self::REMINDER_DAYS_BEFORE + 1))) {
                    $summary['skipped'][] = ['apply_id' => $apply->id, 'reason' => 'already sent'];
                    continue;
                }

                if ($this->sendSmsKub($phone, $message)) {
                    $summary['sent']++;
                } else {
                    Cache::forget($cacheKey); // ให้ส่งใหม่ได้รอบหน้า
                    $summary['failed'][] = ['apply_id' => $apply->id, 'phone' => $phone];
                }
            }

            $result['courses'][] = $summary;
        }

        Log::info('SMS course reminder', $result);

        return $result;
    }

    /**
     * แจ้งเตือนแอดมินเมื่อมีผู้สมัครใหม่ (ส่งแบบรวมกลุ่ม)
     * - ส่งเฉพาะ จ-ศ เวลา 08:00-17:00 (คุมที่ Kernel ด้วย; มีด่านตรวจซ้ำในนี้เพื่อความปลอดภัย)
     * - ผู้สมัครที่ค้างช่วงกลางคืน/นอกเวลา จะถูกสะสมไว้แล้วรวมส่งรอบถัดไป
     * - 1 SMS ไม่เกิน ADMIN_NOTIFY_MAX_PER_SMS คน
     * - เบอร์แอดมินดึงจาก ENV (SMSKUB_ADMIN_PHONES)
     *
     * GET /admin/cron/smsNewApplyNotify         ส่งจริง (ตามกรอบเวลา)
     * GET /admin/cron/smsNewApplyNotify?dry=1    แสดงรายการที่จะส่ง โดยไม่ส่งจริง
     * GET /admin/cron/smsNewApplyNotify?force=1  บังคับส่งทันที ไม่สนกรอบเวลา (ใช้ทดสอบ)
     */
    public function notifyAdminNewApplies(Request $request)
    {
        return response()->json(
            $this->runAdminNewApplyNotify($request->boolean('dry'), $request->boolean('force'))
        );
    }

    public function runAdminNewApplyNotify(bool $dryRun = false, bool $force = false): array
    {
        $now = Carbon::now();

        $result = [
            'run_at'   => $now->toDateTimeString(),
            'dry_run'  => $dryRun,
            'forced'   => $force,
            'sent_sms' => 0,
            'notified_applies' => 0,
            'batches'  => [],
        ];

        // ด่านตรวจกรอบเวลา (ข้ามได้เมื่อ dry/force) ผู้สมัครที่ค้างจะถูกส่งรอบถัดไปที่อยู่ในกรอบเวลา
        if (!$dryRun && !$force && !$this->isWithinAdminNotifyWindow($now)) {
            $result['skipped'] = 'นอกกรอบเวลาส่ง (จ-ศ 08:00-17:00) งานค้างจะถูกส่งรอบถัดไป';
            return $result;
        }

        $adminPhones = $this->adminSmsPhones();
        if (empty($adminPhones)) {
            $result['skipped'] = 'ยังไม่ตั้งค่าเบอร์แอดมิน (SMSKUB_ADMIN_PHONES)';
            Log::warning('Admin new-apply notify: no admin phone configured');
            return $result;
        }
        $result['admin_phones'] = $adminPhones;

        // ใบสมัครใหม่ที่ยังไม่ได้แจ้งแอดมิน และไม่ถูกยกเลิก (ปกติ=ของเมื่อวาน, วันจันทร์=รวม ศ-ส-อา)
        // เรียงตามคอร์สก่อน เพื่อจัดกลุ่มผู้สมัครใต้หัวข้อคอร์สเดียวกัน
        $pending = Apply::with(['member', 'course'])
            ->whereNull('admin_notified_at')
            ->where(fn($q) => $q->whereNull('cancel')->orWhere('cancel', '!=', 1))
            ->orderBy('course_id')
            ->orderBy('id')
            ->get();

        if ($pending->isEmpty()) {
            $result['skipped'] = 'ไม่มีผู้สมัครใหม่';
            return $result;
        }

        foreach ($pending->chunk(self::ADMIN_NOTIFY_MAX_PER_SMS) as $chunk) {
            $message = $this->adminNewApplyMessage($chunk);

            $batch = [
                'apply_ids' => $chunk->pluck('id')->all(),
                'count'     => $chunk->count(),
                'message'   => $message,
            ];

            if ($dryRun) {
                $result['batches'][] = $batch + ['result' => 'dry run'];
                continue;
            }

            // ส่งให้แอดมินทุกเบอร์ ถือว่าสำเร็จถ้าส่งได้อย่างน้อย 1 เบอร์
            $anySent = false;
            foreach ($adminPhones as $phone) {
                if ($this->sendSmsKub($phone, $message)) {
                    $anySent = true;
                    $result['sent_sms']++;
                }
            }

            if ($anySent) {
                Apply::whereIn('id', $chunk->pluck('id'))->update(['admin_notified_at' => now()]);
                $result['notified_applies'] += $chunk->count();
                $batch['result'] = 'sent';
            } else {
                $batch['result'] = 'failed';
            }

            $result['batches'][] = $batch;
        }

        Log::info('Admin new-apply notify', $result);

        return $result;
    }

    // จ-ศ และอยู่ในช่วง 08:00-17:00
    private function isWithinAdminNotifyWindow(Carbon $now): bool
    {
        if ($now->isWeekend()) {
            return false;
        }

        return $now->hour >= self::ADMIN_NOTIFY_HOUR_START
            && $now->hour < self::ADMIN_NOTIFY_HOUR_END;
    }

    // อ่านเบอร์แอดมินจาก ENV แปลงเป็นเบอร์มือถือไทย กรองเบอร์ซ้ำ/ไม่ถูกต้องออก
    private function adminSmsPhones(): array
    {
        $raw = (string) config('services.smskub.admin_phones', '');

        $phones = [];
        foreach (preg_split('/[,\s]+/', $raw, -1, PREG_SPLIT_NO_EMPTY) as $item) {
            $phone = $this->normalizeThaiMobile($item);
            if ($phone) {
                $phones[$phone] = true;
            }
        }

        return array_keys($phones);
    }

    // สร้างข้อความแจ้งเตือนผู้สมัครใหม่ จัดกลุ่มตามคอร์ส (หัวข้อ: ชื่อคอร์ส+สถานที่+วันที่)
    // แต่ละคอร์สลิสต์ผู้สมัคร: ลำดับ. ชื่อ อายุ เบอร์
    private function adminNewApplyMessage($applies): string
    {
        $lines = [];
        $lastCourseId = null;
        $n = 0;

        foreach ($applies as $apply) {
            // ขึ้นหัวข้อใหม่เมื่อเปลี่ยนคอร์ส (เลขลำดับผู้สมัครเริ่มใหม่ในแต่ละคอร์ส)
            if ($lines === [] || $apply->course_id !== $lastCourseId) {
                if ($lines !== []) {
                    $lines[] = '';
                }
                $lines[] = $this->courseHeaderTxt($apply->course);
                $lastCourseId = $apply->course_id;
                $n = 0;
            }

            $n++;
            $member = $apply->member;

            $name = $member ? trim(($member->name ?? '') . ' ' . ($member->surname ?? '')) : '-';
            $name = $name !== '' ? $name : '-';
            $age = $member && $member->age ? (string) $member->age : '';
            $phone = $member ? trim((string) ($member->phone ?: ($member->phone_new ?? ''))) : '';

            $parts = array_filter([$name, $age, $phone], fn($v) => $v !== '');
            $lines[] = "{$n}. " . implode(' ', $parts);
        }

        return implode("\n", $lines);
    }

    // หัวข้อคอร์ส: ชื่อคอร์ส + สถานที่ + วันที่
    private function courseHeaderTxt(?Course $course): string
    {
        if (!$course) {
            return '(ไม่พบข้อมูลคอร์ส)';
        }

        $date = $course->date_start ? Carbon::parse($course->date_start)->format('j/n') : '';

        return trim(
            ($course->category ?? '') . ' '
            . ($course->location ?? '')
            . ($date ? " {$date}" : '')
        );
    }

    private function reminderMessageTh(Course $course): string
    {
        $time = $course->getCourseStartTime('th');

        return "แจ้งเตือนเข้าคอร์ส: {$course->category} "
            . $course->getCourseLongDateTxtAttribute(true)
            . " ที่ {$course->location}"
            . ($time ? " กรุณามาถึงก่อน {$time}" : '')
            . " - มูลนิธิโรงเรียนแห่งชีวิต";
    }

    private function reminderMessageEn(Course $course, ?string $categoryEn, ?string $locationEn): string
    {
        $time = $course->getCourseStartTime('en');

        return "Course reminder: " . ($categoryEn ?: $course->category) . ", "
            . $course->getCourseLongDateTxtEnAttribute(true)
            . " at " . ($locationEn ?: $course->location) . "."
            . ($time ? " Please arrive before {$time}." : '')
            . " - Bodhidhammayan";
    }

    // รับเฉพาะเบอร์มือถือไทย 0[689]XXXXXXXX (แปลง +66/66 ให้)
    private function normalizeThaiMobile(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (str_starts_with($digits, '66') && strlen($digits) === 11) {
            $digits = '0' . substr($digits, 2);
        }

        return preg_match('/^0[689]\d{8}$/', $digits) ? $digits : null;
    }

    // ส่ง SMS ผ่าน SMS-KUB (รูปแบบเดียวกับ CourseApplyController::sendSMS ที่ใช้ส่ง OTP)
    private function sendSmsKub(string $phone, string $message): bool
    {
        $url    = config('services.smskub.url');
        $apiKey = config('services.smskub.key');
        $from   = config('services.smskub.sender', 'KBO staff');

        if (!$url || !$apiKey) {
            Log::warning("SMSKUB not configured. Skip SMS to {$phone}");
            return false;
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'key'          => $apiKey,
        ])
            ->timeout(10)
            ->send('POST', $url, [
                'body' => json_encode(['to' => [$phone], 'from' => $from, 'message' => $message], JSON_UNESCAPED_UNICODE),
            ]);

        if (!$response->successful()) {
            Log::error('SMSKUB course reminder error', ['phone' => $phone, 'status' => $response->status(), 'body' => $response->body()]);
            return false;
        }

        return true;
    }

    public function form()
    {
        return view('sms.send');
    }

    public function send(Request $request, SmsKubClient $client)
    {
        $data = $request->validate([
            'to'      => ['required','string','max:20'],   // ปรับ rule ได้ตามเบอร์ E.164
            'message' => ['required','string','max:1000'],
            'sender'  => ['nullable','string','max:20'],
        ]);

        $result = $client->send($data['to'], $data['message'], $data['sender'] ?? null);

        return back()->with([
            'sms_result' => $result,
            'old_input'  => $data,
        ]);
    }


    public function sendSms(Request $request)
    {
        // 1. Validate the incoming request data
        try {
            $validatedData = $request->validate([
                'to' => 'required|string|regex:/^[0-9]{10,12}$/', // Basic validation for a phone number
                'message' => 'required|string|max:1000', // Max length for SMS
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation Failed', 'messages' => $e->errors()], 422);
        }

        // 2. Get API Key and Sender ID from .env file
        //    IMPORTANT: Add these to your .env file!
        //    TWOFACTOR_API_KEY=your_api_key_here
        //    TWOFACTOR_SENDER_ID=your_sender_id_here (e.g., "market")
        $apiKey = env('SMSKUB_API_KEY');
        $senderId = "env('KBO staff')";

        // Check if config is missing
        if (!$apiKey || !$senderId) {
            return response()->json(['error' => 'SMS configuration is missing. Please set TWOFACTOR_API_KEY and TWOFACTOR_SENDER_ID in your .env file.'], 500);
        }

        // 3. Construct the API URL
        $url = "https://2factor.in/API/V1/{$apiKey}/ADDON_SERVICES/SEND/TSMS";

        // 4. Prepare the form data as per the documentation
        $formData = [
            'From' => $senderId,
            'To' => $validatedData['to'],
            'Msg' => $validatedData['message'],
        ];

        // 5. Make the POST request using Laravel's Http facade
        //    We use asForm() because the API expects 'form-data'
        $response = Http::asForm()->post($url, $formData);

        // 6. Handle the response from the API
        if ($response->successful()) {
            // API call was successful (e.g., 2xx status code)
            $apiResult = $response->json(); // Get the JSON response body

            if (isset($apiResult['Status']) && $apiResult['Status'] === 'Success') {
                return response()->json([
                    'success' => true,
                    'message' => 'SMS sent successfully.',
                    'details' => $apiResult['Details'] ?? null
                ], 200);
            } else {
                // The API returned a "Success" status but with an error message
                return response()->json([
                    'success' => false,
                    'message' => 'API returned an error.',
                    'api_response' => $apiResult
                ], 400);
            }

        } else {
            // API call failed (e.g., 4xx or 5xx status code)
            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS due to an API error.',
                'status_code' => $response->status(),
                'api_response' => $response->body() // Get raw body for debugging
            ], $response->status());
        }
    }
}
