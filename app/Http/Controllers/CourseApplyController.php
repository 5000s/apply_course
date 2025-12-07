<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\Apply;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Location;
use App\Models\Member;
use App\Models\OtpCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use function Symfony\Component\String\b;


class CourseApplyController extends Controller
{

    public function courseTableWordpress(Request $request, $type, $lang)
    {
        $month_backward = 5;
        if ($request->input('m')) {
            $month_backward = $request->input('m');
        }

        $id = "area_" . rand(10000, 99999);
        if ($request->input('id')) {
            $id = $request->input('id');
        }

        $location = 1;
        if ($request->input('location')) {
            $location = $request->input('location');
        }

        $regis = false;
        if ($request->input('regis') && $request->input('regis') == 1) {
            $regis = true;
        }

        if ($type == 1) {
            $courses = Course::whereIn('category_id', [1, 3])
                ->where('location_id', $location)
                ->where('date_start', '>=', now()->subMonths($month_backward))
                ->where("state", "เปิดรับสมัคร")
                ->orderBy('date_start', 'asc')
                ->get();
        } else {
            $courses = Course::where('category_id', $type)
                ->where('location_id', $location)
                ->where('date_start', '>=', now()->subMonths($month_backward))
                ->where("state", "เปิดรับสมัคร")
                ->orderBy('date_start', 'asc')
                ->get();
        }


        $category = CourseCategory::where('id', $type)->first();



        return view('apply.course_table', compact('courses', 'category', 'lang', 'id', 'regis'));
    }

    public function directApply(Request $request)
    {
        $courseId = $request->input('course_id');
        abort_unless($courseId, 404, 'course_id is required');

        $th = function ($d) {
            if (!$d) return null;
            $c = Carbon::parse($d)->locale('th');
            return $c->translatedFormat('j F') . ' ' . ($c->year + 543);
        };


        $course = Course::findOrFail($courseId);
        $courseCat = CourseCategory::findOrFail($course->category_id);

        $course->date_start_txt = $th($course->date_start);
        $course->date_end_txt   = $th($course->date_end);

        // -------------------------------
        // ตรวจ "เปิดรับสมัคร" = เริ่มมากกว่า 14 วันจากตอนนี้
        // -------------------------------
        $now            = now(); // ตาม timezone ใน config/app.php
        $startCarbon    = $course->date_start ? Carbon::parse($course->date_start) : null;
        $daysUntilStart = $startCarbon ? $now->diffInDays($startCarbon, false) : null; // ติดลบถ้าเลยแล้ว

        $isOpen = false;
        $state  = 'ไม่ระบุ';

        if (!$startCarbon) {
            $state = 'ยังไม่กำหนดวันเริ่ม';
        } elseif ($daysUntilStart >= 14) {
            $isOpen = true;
            $state  = 'เปิดรับสมัคร';
        } elseif ($daysUntilStart >= 0) {
            $state = 'ใกล้เริ่มแล้ว';
        } else {
            $state = 'สิ้นสุดการรับสมัคร';
        }


        // Query #2: หาสถานที่
        $location = null;

        if (!empty($course->location_id)) {
            // ถ้ามี FK ก็ใช้ ID ตรงๆ
            $location = Location::find($course->location_id);
        }


        // 2) รูปสถานที่ (อยู่ public/images)
        $rawImg = $location->image ?? null;
        if ($rawImg) {
            $imageUrl = Str::startsWith($rawImg, ['http://', 'https://', '/'])
                ? $rawImg
                : asset('images/' . $rawImg);    // ชี้ไป public/images/<ไฟล์>
        } else {
            $imageUrl = 'https://placehold.co/800x450?text=Course+Image';
        }

        $placeName = $location?->show_name
            ?? null;

        // 3) วันที่ format
        $startText = $course->start_date
            ? Carbon::parse($course->start_date)->format('d M Y')
            : null;

        $endText = $course->end_date
            ? Carbon::parse($course->end_date)->format('d M Y')
            : null;

        // 4) ส่ง view model ให้ Blade
        return view('apply.direct', [
            'course'     => $course,
            'course_cat' => $courseCat,
            'vm' => [
                'place_name' => $placeName,
                'image_url'  => $imageUrl,
                'start_text' => $startText,
                'end_text'   => $endText,
                'alt'        => $placeName ?: $course->title,
                'is_open'         => $isOpen,
                'state'           => $state,
                'days_until_start' => $daysUntilStart,
            ],
        ]);
    }

    public function applyCourse(Request $request)
    {
        // 1) validate ฟอร์มหน้าแรก
        $data = $request->validate([
            'course_id'  => ['required', 'integer', 'exists:courses,id'],
            'gender'     => ['required', 'in:หญิง,ชาย'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date'],
            'phone'      => ['required', 'string', 'max:30'],
            'email'      => ['nullable', 'email', 'max:190'],
            'g-recaptcha-response' => app()->environment('production')
                ? ['required']
                : ['nullable'],   // local ข้ามได้
        ]);

        // 2) ตรวจ reCAPTCHA เฉพาะ production
        if (app()->environment('production')) {
            $secret = config('services.recaptcha.secret_key');

            $response = Http::asForm()->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'secret'   => $secret,
                    'response' => $request->input('g-recaptcha-response'),
                    'remoteip' => $request->ip(),
                ]
            );

            $captcha = $response->json();
            if (empty($captcha['success'])) {
                return back()
                    ->withErrors(['g-recaptcha-response' => 'กรุณายืนยัน Captcha ให้ถูกต้อง'])
                    ->withInput();
            }
        }

        $firstname = $data['first_name'];
        $lastname = $data['last_name'];
        $gender = $data['gender'];
        $phone = $data['phone'];
        $birth_date = $data['birth_date'];
        $code = $this->getApplyCode();
        $email = "";


        $member = Member::findCandidate($gender, $firstname, $lastname, $birth_date);

        if (!$member) {
            $member = $this->createMember($gender, $firstname, $lastname, $birth_date, $phone, $email, $code);
        } else {
            // update เบา ๆ เผื่อข้อมูลใหม่กว่า
            $member->phone     = $data['phone'];
            if (!empty($data['email'])) {
                $member->email = $data['email'];
            }
            $member->updated_by = 'web-direct';
        }

        $member->save();

        $course  = Course::findOrFail($data['course_id']);

        $apply = $this->newApply($member->id, $course->id, 0, "ทั่วไป");



        // 4) Prepare View Model (Same as directApply)
        $courseCat = CourseCategory::findOrFail($course->category_id);

        // Logic to construct $vm
        $th = function ($d) {
            if (!$d) return null;
            $c = Carbon::parse($d)->locale('th');
            return $c->translatedFormat('j F') . ' ' . ($c->year + 543);
        };

        $course->date_start_txt = $th($course->date_start);
        $course->date_end_txt   = $th($course->date_end);

        $now            = now();
        $startCarbon    = $course->date_start ? Carbon::parse($course->date_start) : null;
        $daysUntilStart = $startCarbon ? $now->diffInDays($startCarbon, false) : null;

        $isOpen = false;
        $state  = 'ไม่ระบุ';

        if (!$startCarbon) {
            $state = 'ยังไม่กำหนดวันเริ่ม';
        } elseif ($daysUntilStart >= 14) {
            $isOpen = true;
            $state  = 'เปิดรับสมัคร';
        } elseif ($daysUntilStart >= 0) {
            $state = 'ใกล้เริ่มแล้ว';
        } else {
            $state = 'สิ้นสุดการรับสมัคร';
        }

        $location = null;
        if (!empty($course->location_id)) {
            $location = Location::find($course->location_id);
        }

        $rawImg = $location->image ?? null;
        if ($rawImg) {
            $imageUrl = Str::startsWith($rawImg, ['http://', 'https://', '/'])
                ? $rawImg
                : asset('images/' . $rawImg);
        } else {
            $imageUrl = 'https://placehold.co/800x450?text=Course+Image';
        }

        $placeName = $location?->show_name ?? null;
        $startText = $course->start_date ? Carbon::parse($course->start_date)->format('d M Y') : null;
        $endText = $course->end_date ? Carbon::parse($course->end_date)->format('d M Y') : null;

        $vm = [
            'place_name' => $placeName,
            'image_url'  => $imageUrl,
            'start_text' => $startText,
            'end_text'   => $endText,
            'alt'        => $placeName ?: $course->title,
            'is_open'         => $isOpen,
            'state'           => $state,
            'days_until_start' => $daysUntilStart,
        ];

        // Fetch and sort provinces
        $provinces = \PA\ProvinceTh\Factory::province();
        $provinceArray = $provinces->toArray();
        usort($provinceArray, function ($a, $b) {
            return strcmp($a['name_th'], $b['name_th']);
        });

        $data  = [];
        $data['course'] = $course;
        $data['member'] = $member;
        $data['apply'] = $apply;
        $data['course_cat'] = $courseCat;
        $data['vm'] = $vm;
        $data['provinces'] = $provinceArray;
        $data['nations'] = MemberController::$nationals;

        // 5) redirect ไปหน้าแบบฟอร์มเต็ม (Step 2)
        return  view('apply.full_form', $data);
    }

    public function newApply($member_id, $course_id, $van, $shelter)
    {
        $apply = new Apply();
        $apply->member_id = $member_id;
        $apply->course_id = $course_id;
        $apply->van = $van;
        $apply->shelter = $shelter;
        $apply->cancel = 0;
        $apply->state = "ยื่นใบสมัคร";
        $apply->created_by = "USER";
        return $apply;
    }


    // Send OTP to phone OR email
    public function directRequestOtp(Request $request)
    {
        $data = $request->validate([
            'course_id'  => ['required', Rule::exists('courses', 'id')],
            'gender' => ['required', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'channel'    => ['required', Rule::in(['phone', 'email'])],
            'phone'      => ['nullable', 'string', 'max:30', 'required_if:channel,phone'],
            'email'      => ['nullable', 'email', 'max:190', 'required_if:channel,email'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'member_id'  => ['nullable', 'integer'],
        ], [
            'phone.required_if' => 'กรุณากรอกเบอร์โทรศัพท์',
            'email.required_if' => 'กรุณากรอกอีเมล',
        ]);

        // --- normalize recipient
        $recipient = $data['channel'] === 'phone'
            ? $this->normalizePhone($data['phone'])
            : strtolower(trim($data['email']));

        $firstname = $data['first_name'];
        $lastname = $data['last_name'];
        $gender = $data['gender'];
        $phone = "";
        $email = "";
        if ($data['channel'] === 'phone') {
            $phone = $this->normalizePhone($data['phone']);
        } else {
            $email = strtolower(trim($data['email']));
        }

        if ($data['channel'] === 'phone' && !$recipient) {
            return response()->json(['ok' => false, 'message' => 'รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง'], 422);
        }

        //        $key = "otp:{$data['channel']}:{$recipient}";
        //        if (! RateLimiter::attempt($key, $perMinute = 1, fn() => true, $decay = 60)) {
        //            return response()->json(['ok'=>false,'message'=>'โปรดรอสักครู่ก่อนขอรหัสใหม่'], 429);
        //        }


        $birthDate = $data['birth_date'];
        $code = null;

        $memberSearch = Member::findCandidate($gender, $firstname, $lastname, $birthDate);
        if ($memberSearch) {
            $existing = OtpCode::where('member_id', $memberSearch->id)->first();
            if (!$existing) {
                $code = $this->getApplyCode();
            }
            $memberSearch->applycode = $code;
            $memberSearch->save();
        } else {

            $code = $this->getApplyCode();
            $expiresAt = now()->addYear(30);

            $memberSearch = $this->createMember($gender, $firstname, $lastname, $birthDate, $phone, $email, $code);

            OtpCode::create([
                'member_id'  => $memberSearch->id,
                'channel'    => $data['channel'],
                'recipient'  => $recipient,
                'code'       => $code,
                'expires_at' => $expiresAt,
                'attempts'   => 0,
            ]);
        }



        // --- send
        try {
            if ($data['channel'] === 'email') {
                // Mail::to($recipient)->send(new OtpMail($code));
                Log::info("OTP Email to {$recipient}: {$code}");
            } else {
                // SmsService::send($recipient, "รหัส OTP: {$code} (หมดอายุใน 5 นาที)");

                $this->sendSMS($recipient, $code, $firstname, $lastname);
                Log::info("OTP SMS to {$recipient}: {$code}");
            }
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(['ok' => false, 'message' => 'ส่งรหัสไม่สำเร็จ โปรดลองอีกครั้ง'], 500);
        }

        return response()->json([
            'ok'        => true,
            'message'   => 'ส่งรหัส OTP แล้ว (มีอายุ 5 นาที)',
            'channel'   => $data['channel'],
            'recipient' => $this->maskRecipient($data['channel'], $recipient),
            // ถ้าหน้าคุณต้องใช้ค่าตัวเต็มในการยืนยัน ให้ส่ง recipient ตัวเต็ม (ไม่ mask) ด้วยอีกฟิลด์
            // แต่อย่า render ออกหน้าจอ
            'recipient_raw' => $recipient,
        ]);
    }

    public function getApplyCode()
    {
        $code = str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
        return $code;
    }

    public function createMember($gender, $firstname, $lastname, $birthDate, $phone, $email, $applyCode)
    {
        $member = new Member();
        $member->gender = $gender;
        $member->name = $firstname;
        $member->surname = $lastname;
        $member->birthdate = $birthDate;
        $member->phone = $phone;
        $member->email = $email;
        $member->buddhism = "ฆราวาส";
        $member->status = "ผู้สมัครใหม่";
        $member->applycode = "$applyCode";
        $member->created_by = "DIRECT-APPLY";
        $member->updated_by = "DIRECT-APPLY";

        $member->save();

        return $member;
    }


    // public function directConfirm(Request $request, $course_id, $member_id)
    public function directConfirm(Request $request, $course_id, $member_id)
    {
        $van = $request->input('van', "no");
        $shelter = "ทั่วไป";
        $course = Course::find($course_id);
        $location = Location::find($course->location_id);


        $courseCategory = CourseCategory::find($course->category_id);

        $apply = Apply::where('course_id', $course_id)
            ->where('member_id', $member_id)
            ->first();

        if (!$apply) {
            $apply = new Apply();
            $apply->member_id = $member_id;
            $apply->course_id = $course_id;
            $apply->created_by = "USER";
        }

        $apply->van = $van;
        $apply->shelter = $shelter;
        $apply->cancel = 0;
        $apply->state = "ยื่นใบสมัคร";
        $apply->save();

        return view('apply.complete', ['apply' => $apply, 'course' => $course, 'location' => $location, 'courseCategory' => $courseCategory]);
    }

    //     $data = $request->validate([
    //         'course_id'  => ['required', Rule::exists('courses','id')],
    //         'first_name' => ['required','string','max:100'],
    //         'last_name'  => ['required','string','max:100'],
    //         'channel'    => ['required', Rule::in(['phone','email'])],
    //         'recipient'  => ['required','string','max:190'], // phone or email
    //         'code'       => ['required','string','max:10'],
    //         'member_id'  => ['nullable','integer'],
    //     ]);

    //     $otp = OtpCode::where('channel',$data['channel'])
    //         ->where('recipient',$data['recipient'])
    //         ->latest()->first();

    //     if (!$otp) return response()->json(['ok'=>false,'message'=>'OTP not found.'], 422);
    //     if ($otp->expires_at->isPast()) return response()->json(['ok'=>false,'message'=>'OTP expired.'], 422);
    //     if ($otp->attempts >= 5) return response()->json(['ok'=>false,'message'=>'Too many attempts. Request a new OTP.'], 429);

    //     $otp->increment('attempts');

    //     if ($otp->code !== $data['code']) {
    //         return response()->json(['ok'=>false,'message'=>'Invalid OTP.'], 422);
    //     }

    //     // Mark complete by creating an apply row (adjust field names to your real table)
    //     $apply = Apply::create([
    //         'course_id'   => $data['course_id'],
    //         'member_id'   => $data['member_id'] ?? null,
    //         'first_name'  => $data['first_name'],
    //         'last_name'   => $data['last_name'],
    //         'phone'       => $data['channel']==='phone' ? $data['recipient'] : null,
    //         'email'       => $data['channel']==='email' ? $data['recipient'] : null,
    //         'confirmed_at'=> Carbon::now(),
    //     ]);

    //     // optional cleanup
    //     OtpCode::where('channel',$data['channel'])
    //         ->where('recipient',$data['recipient'])
    //         ->delete();

    //     return response()->json([
    //         'ok'=>true,
    //         'message'=>'Complete registration.',
    //         'apply_id'=>$apply->id
    //     ]);
    // }




    // --- helpers ---
    protected function normalizePhone(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        // 1) เอาเฉพาะตัวเลข
        $digits = preg_replace('/\D+/', '', $phone);

        if ($digits === '') {
            return null;
        }

        // 2) แปลงรูปแบบ +66 / 66xxxxxxxxx -> 0xxxxxxxxx
        //    (เบอร์มือถือไทยจะมี 9 หลักตามหลัง 66 = รวม 11 หลัก)
        if (Str::startsWith($digits, '66') && strlen($digits) === 11) {
            $digits = '0' . substr($digits, 2);  // 66xxxxxxxxx -> 0xxxxxxxxx
        }

        // 3) ตรวจรูปแบบสุดท้าย: 0XXXXXXXXX (10 หลัก) และต้องขึ้นต้น 06/08/09
        if (!preg_match('/^0[689]\d{8}$/', $digits)) {
            // ไม่ใช่เบอร์มือถือไทย
            return null;
        }

        return $digits; // คืนค่าแบบ normalize แล้ว เช่น 0812345678
    }

    private function maskRecipient(string $channel, string $value): string
    {
        if ($channel === 'email') {
            [$u, $d] = explode('@', $value, 2);
            return mb_substr($u, 0, 2) . '***@' . $d;
        }
        // phone: keep last 4
        return preg_replace('/.(?=.{4})/u', '•', $value);
    }


    private function sendSMS(string $phone, string $code, $firstname, $lastname): bool
    {
        $url    = config('services.smskub.url', 'https://console.sms-kub.com/api/messages');
        $apiKey = config('services.smskub.key');
        $from   = config('services.smskub.sender', 'KBO staff');

        if (! $url || ! $apiKey) {
            Log::warning("SMSKUB not configured. Simulate send to {$phone} (OTP {$code})");
            return false;
        }

        $payload = [
            'to'      => [$phone],
            'from'    => $from,
            'message' => "รหัส สำหรับสมัครของคุณ $firstname $lastname คือ: {$code}",
        ];

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'key'          => $apiKey,
        ])
            ->timeout(10)
            ->send('POST', $url, [
                'body' => $json,   // เหมือน curl --data ' {...}'
            ]);




        if (! $response->successful()) {
            Log::error('SMSKUB HTTP error', [
                'phone'  => $phone,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return false;
        }

        Log::info('SMSKUB OTP sent', [
            'phone'  => $phone,
            'code'   => $code,
            'result' => $response->json() ?? $response->body(),
        ]);

        return true;
    }
}
