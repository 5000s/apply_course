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
use App\Models\ReportCase;
use App\Models\CourseLocationLimit;

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
        } elseif ($daysUntilStart >= 4) {
            $isOpen = true;
            $state  = 'เปิดรับสมัคร';
        } elseif ($daysUntilStart >= 1) {
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

        $placeNameEn = $location?->show_name_en
            ?? null;

        // 3) วันที่ format
        $startText = $course->start_date
            ? Carbon::parse($course->start_date)->format('d M Y')
            : null;

        $endText = $course->end_date
            ? Carbon::parse($course->end_date)->format('d M Y')
            : null;

        // 4) ส่ง view model ให้ Blade
        $lang = $request->input('lang', 'th');

        return view('apply.direct', [
            'course'     => $course,
            'course_cat' => $courseCat,
            'lang'       => $lang,
            'vm' => [
                'place_name' => $placeName,
                'place_name_en' => $placeNameEn,
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
            'phone'      => ['nullable', 'string', 'max:30'],
            'email'      => ['nullable', 'email', 'max:190'],
            'lang'       => ['nullable', 'string', 'max:10'],
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

        $member_id = $request->input('member_id');

        $firstname = $data['first_name'];
        $lastname = $data['last_name'];
        $gender = $data['gender'];

        $birth_date = $data['birth_date'];
        $course_id = $data['course_id'];

        try {
            $phone = $data['phone'];
        } catch (\Exception $e) {
            $phone = "";
        }


        try {
            $lang = $data['lang'];
        } catch (\Exception $e) {
            $lang = "th";
        }


        return $this->applyConfirm($member_id, $course_id, $firstname, $lastname, $gender, $phone, $birth_date, $lang);
    }

    public function applyConfirm($member_id, $course_id, $firstname, $lastname, $gender, $phone, $birth_date, $lang)
    {

        $code = $this->getApplyCode();
        $email = "";
        $member_new = false;

        $course  = Course::findOrFail($course_id);

        if ($member_id) {
            $member = Member::findOrFail($member_id);
        } else {
            // $member = Member::findCandidate($gender, $firstname, $lastname, $birth_date);
            $member = null;
        }

        if (!$member) {
            $member = $this->createMember($gender, $firstname, $lastname, $birth_date, $phone, $email, $code);
            $member->save();
            $member_new = true;
        } else {
            // update เบา ๆ เผื่อข้อมูลใหม่กว่า
            if (strlen($phone) > 80) {
                $phone = substr($phone, 0, 80);
            }

            $member->phone_new = $phone;
            $member->updated_by = 'web-direct';
            $member->save();
            $member_new = false;
        }

        $apply = $this->newApply($member->id, $course->id, 0, "ทั่วไป");

        // 4) Prepare View Model (Same as directApply)
        $courseCat = CourseCategory::findOrFail($course->category_id);

        $need_check_history_apply = false;
        if (Str::contains($courseCat->show_name ?? '', 'วิปัสสนา')) {
            $need_check_history_apply = true;
        }


        if ($need_check_history_apply) {

            $not_pass = false;

            if ($member_new) {
                $not_pass = true;
            }

            $passCourseCategoryID = [1, 2, 3, 4, 5, 6, 8, 10, 12, 13, 14, 15, 16, 17];

            $applyHistory = Apply::where('member_id', $member->id)
                ->whereHas('course', function ($q) use ($passCourseCategoryID) {
                    $q->whereIn('category_id', $passCourseCategoryID);
                })
                ->with('course')
                ->get();

            if (count($applyHistory) == 0) {
                $not_pass = true;
            }

            if ($not_pass) {
                $message_eng =  "New applicants must have attended at least one Anapanasati meditation course (3-4 days course) at Bodhidhammayan before registering for Vipassana meditation.";
                $message_th = "ผู้สมัครศิษย์ใหม่ที่จะเข้าอบรมวิปัสสนากรรมฐานได้ “ต้องผ่านการอบรมสมาธิอานาปานสติ (3-4 วัน) ของโพธิธรรมญาณ ก่อนอย่างน้อย 1 ครั้ง";

                return back()
                    ->withErrors(['course_id' => $lang == 'en' ? $message_eng : $message_th])  // หรือข้อความอื่น
                    ->withInput();
            }
        }


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
        } elseif ($daysUntilStart >= 4) {
            $isOpen = true;
            $state  = 'เปิดรับสมัคร';
        } elseif ($daysUntilStart >= 1) {
            $state = 'ใกล้เริ่มแล้ว';
        } else {
            $state = 'สิ้นสุดการรับสมัคร';
        }

        if (!$isOpen) {
            $request = new Request();
            $request->merge([
                'course_id' => $course->id,
                'member_id' => $member->id,
            ]);

            return $this->directApply($request);
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
        $placeNameEn = $location?->show_name_en ?? null;
        $startText = $course->start_date ? Carbon::parse($course->start_date)->format('d M Y') : null;
        $endText = $course->end_date ? Carbon::parse($course->end_date)->format('d M Y') : null;

        $vm = [
            'place_name' => $placeName,
            'place_name_en' => $placeNameEn,
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
        $data['member_new'] = $member_new;
        $data['apply'] = $apply;
        $data['course_cat'] = $courseCat;
        $data['vm'] = $vm;
        $data['provinces'] = $provinceArray;
        $data['nations'] = MemberController::$nationals;

        $data['lang']    = $lang;


        $need_more_info = false;
        if (
            Str::contains($courseCat->show_name ?? '', 'วิปัสสนา') &&
            Str::contains($vm['place_name'] ?? '', 'แก่งคอย')
        ) {
            $need_more_info = true;
        }

        if ($member_new) {
            $need_more_info = true;
        }

        if ($need_more_info) {
            // 5) redirect ไปหน้าแบบฟอร์มเต็ม (Step 2)
            return  view('apply.full_form', $data);
        } else {

            $request = new Request();
            $request->merge([
                'course_id' => $course->id,
                'member_id' => $member->id,
                'lang' => $lang,
            ]);


            return $this->directConfirm($request, $course_id, $member_id);
        }
    }

    public function newApply($member_id, $course_id, $van, $shelter)
    {
        $apply = Apply::where('member_id', $member_id)
            ->where('course_id', $course_id)
            ->where('cancel', "!=", 1)
            ->first();

        if ($apply) {
            return $apply;
        }

        $apply = new Apply();
        $apply->member_id = $member_id;
        $apply->course_id = $course_id;
        $apply->van = $van;
        $apply->shelter = $shelter;
        $apply->cancel = null;
        $apply->state = "ยื่นใบสมัคร";
        $apply->created_by = "USER";

        return $apply;
    }


    // Send OTP to phone OR email
    // public function directRequestOtp(Request $request)
    // {
    //     $data = $request->validate([
    //         'course_id'  => ['required', Rule::exists('courses', 'id')],
    //         'gender' => ['required', 'string', 'max:100'],
    //         'first_name' => ['required', 'string', 'max:100'],
    //         'last_name'  => ['required', 'string', 'max:100'],
    //         'channel'    => ['required', Rule::in(['phone', 'email'])],
    //         'phone'      => ['nullable', 'string', 'max:30', 'required_if:channel,phone'],
    //         'email'      => ['nullable', 'email', 'max:190', 'required_if:channel,email'],
    //         'birth_date' => ['required', 'date', 'before_or_equal:today'],
    //         'member_id'  => ['nullable', 'integer'],
    //     ], [
    //         'phone.required_if' => 'กรุณากรอกเบอร์โทรศัพท์',
    //         'email.required_if' => 'กรุณากรอกอีเมล',
    //     ]);

    //     // --- normalize recipient
    //     $recipient = $data['channel'] === 'phone'
    //         ? $this->normalizePhone($data['phone'])
    //         : strtolower(trim($data['email']));

    //     $firstname = $data['first_name'];
    //     $lastname = $data['last_name'];
    //     $gender = $data['gender'];
    //     $phone = "";
    //     $email = "";
    //     if ($data['channel'] === 'phone') {
    //         $phone = $this->normalizePhone($data['phone']);
    //     } else {
    //         $email = strtolower(trim($data['email']));
    //     }

    //     if ($data['channel'] === 'phone' && !$recipient) {
    //         return response()->json(['ok' => false, 'message' => 'รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง'], 422);
    //     }

    //     //        $key = "otp:{$data['channel']}:{$recipient}";
    //     //        if (! RateLimiter::attempt($key, $perMinute = 1, fn() => true, $decay = 60)) {
    //     //            return response()->json(['ok'=>false,'message'=>'โปรดรอสักครู่ก่อนขอรหัสใหม่'], 429);
    //     //        }


    //     $birthDate = $data['birth_date'];
    //     $code = null;

    //     $memberSearch = Member::findCandidate($gender, $firstname, $lastname, $birthDate);
    //     if ($memberSearch) {
    //         $existing = OtpCode::where('member_id', $memberSearch->id)->first();
    //         if (!$existing) {
    //             $code = $this->getApplyCode();
    //         }
    //         $memberSearch->applycode = $code;
    //         $memberSearch->save();
    //     } else {

    //         $code = $this->getApplyCode();
    //         $expiresAt = now()->addYear(30);

    //         $memberSearch = $this->createMember($gender, $firstname, $lastname, $birthDate, $phone, $email, $code);

    //         OtpCode::create([
    //             'member_id'  => $memberSearch->id,
    //             'channel'    => $data['channel'],
    //             'recipient'  => $recipient,
    //             'code'       => $code,
    //             'expires_at' => $expiresAt,
    //             'attempts'   => 0,
    //         ]);
    //     }



    //     // --- send
    //     try {
    //         if ($data['channel'] === 'email') {
    //             // Mail::to($recipient)->send(new OtpMail($code));
    //             Log::info("OTP Email to {$recipient}: {$code}");
    //         } else {
    //             // SmsService::send($recipient, "รหัส OTP: {$code} (หมดอายุใน 5 นาที)");

    //             $this->sendSMS($recipient, $code, $firstname, $lastname);
    //             Log::info("OTP SMS to {$recipient}: {$code}");
    //         }
    //     } catch (\Throwable $e) {
    //         Log::error($e);
    //         return response()->json(['ok' => false, 'message' => 'ส่งรหัสไม่สำเร็จ โปรดลองอีกครั้ง'], 500);
    //     }

    //     return response()->json([
    //         'ok'        => true,
    //         'message'   => 'ส่งรหัส OTP แล้ว (มีอายุ 5 นาที)',
    //         'channel'   => $data['channel'],
    //         'recipient' => $this->maskRecipient($data['channel'], $recipient),
    //         // ถ้าหน้าคุณต้องใช้ค่าตัวเต็มในการยืนยัน ให้ส่ง recipient ตัวเต็ม (ไม่ mask) ด้วยอีกฟิลด์
    //         // แต่อย่า render ออกหน้าจอ
    //         'recipient_raw' => $recipient,
    //     ]);
    // }
    public function getApplyCode()
    {
        $code = str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
        return $code;
    }

    public function createMember($gender, $firstname, $lastname, $birthDate, $phone, $email, $applyCode)
    {
        $member = Member::findCandidate($gender, $firstname, $lastname, $birthDate);

        if ($member) {
            if (
                $member->name == $firstname
                && $member->surname == $lastname
                && $member->gender == $gender
                && $member->birthdate->format('Y-m-d') == $birthDate
            ) {
                return $member;
            }
        }

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

        $full_form = false;
        $no_update = false;

        if ($request->input('full_form') == 1 || $request->input('full_form') == "1") {
            $full_form = true;
        }

        if ($request->input('no_update') == 1 || $request->input('no_update') == "1") {
            $no_update = true;
        }



        if ($full_form && !$no_update) {

            $member = Member::find($member_id);
            $member->gender = $request->input('gender');
            $member->name = $request->input('name');
            $member->surname = $request->input('surname');
            $member->birthdate = $request->input('birthdate');
            $member->email = $request->input('email');
            $member->phone = $request->input('phone');
            $member->phone_new = $request->input('phone_2');
            $member->line = $request->input('line');
            $member->nationality = $request->input('nationality');
            $member->province = $request->input('province');
            $member->country = $request->input('country');
            $member->medical_condition = $request->input('disease');
            $member->degree = $request->input('degree');
            $member->organization = $request->input('organization');
            $member->career = $request->input('career');
            $member->expertise = $request->input('expertise');
            $member->name_emergency = $request->input('name_emergency');
            $member->surname_emergency = $request->input('surname_emergency');
            $member->phone_emergency = $request->input('phone_emergency');
            $member->relation_emergency = $request->input('relation_emergency');
            $member->save();
        }

        $van = $request->input('van', "no");
        $shelter = "ทั่วไป";
        $course = Course::find($course_id);
        $location = Location::find($course->location_id);


        $courseCategory = CourseCategory::find($course->category_id);


        $apply = Apply::where('course_id', $course_id)
            ->where('member_id', $member_id)
            ->where(function ($query) {
                $query->where('cancel', '!=', 1)
                    ->orWhereNull('cancel');
            })
            ->first();

        $member = Member::find($member_id);

        if (!$member) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบข้อมูลสมาชิก'], 404);
        }

        if (!$apply) {
            $apply = new Apply();
            $apply->member_id = $member_id;
            $apply->course_id = $course_id;
            $apply->created_by = "USER";
            $apply->updated_by = "USER";
        }

        $apply->van = $van;
        $apply->shelter = $shelter;
        $apply->cancel = null;
        $apply->state = "ยื่นใบสมัคร";
        $apply->save();



        $lang = $request->input('lang', 'th');

        $isNeedConfirm = false;

        if (str_contains($course->category, "วิปัสสนา")) {
            $isNeedConfirm = true;
        } else {
            $courseLimit = $this->getCourseLimit($course->category_id, $location->id);
            $applyCount = $this->getApplyCount($course_id);

            if ($member->gender == "ชาย") {
                $applyCount->male = $applyCount->male + 1;
            } else {
                $applyCount->female = $applyCount->female + 1;
            }

            $totalCount = $applyCount->male + $applyCount->female;

            if ($applyCount->male > $courseLimit['male_limit'] || $applyCount->female > $courseLimit['female_limit'] || $totalCount > $courseLimit['max_limit']) {
                $isNeedConfirm = true;
            }
        }

        if ($isNeedConfirm) {
            return view('apply.complete_non_confirm', ['apply' => $apply, 'course' => $course, 'location' => $location, 'courseCategory' => $courseCategory, 'lang' => $lang]);
        }

        $apply->state = "ยืนยันแล้ว";
        $apply->remark = "ระบบยืนยันการสมัคร";
        $apply->save();

        return view('apply.complete_ana', ['apply' => $apply, 'course' => $course, 'location' => $location, 'courseCategory' => $courseCategory, 'lang' => $lang]);
    }

    public function getCourseLimit($course_category_id, $location_id)
    {
        $courseLimit = CourseLocationLimit::where('course_category_id', $course_category_id)
            ->where('location_id', $location_id)
            ->first();

        if ($courseLimit) {
            $male_limit = $courseLimit->male_limit;
            $female_limit = $courseLimit->female_limit;
            $max_limit = $courseLimit->max_limit;
        } else {
            $male_limit = 0;
            $female_limit = 0;
            $max_limit = 0;
        }

        return [
            'male_limit' => $male_limit,
            'female_limit' => $female_limit,
            'max_limit' => $max_limit,
        ];
    }

    public function getApplyCount($course_id)
    {
        $applyList = Apply::where('course_id', $course_id)
            ->where('cancel', "!=", 1)
            ->with('member')
            ->get();

        $male_count = $applyList->where('member.gender', 'ชาย')->count();
        $female_count = $applyList->where('member.gender', 'หญิง')->count();
        $total_count = $applyList->count();

        return (object) [
            'male' => $male_count,
            'female' => $female_count,
            'total' => $total_count
        ];
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


    public function searchMember(Request $request)
    {
        // รับค่าจาก ajax
        $gender = $request->input('gender');
        $firstname = $request->input('first_name'); // form name="first_name"
        $lastname = $request->input('last_name');   // form name="last_name"
        $birthDate = $request->input('birth_date'); // form name="birth_date" (Y-m-d)

        // ถ้าส่งมาเป็น camelCase ก็เผื่อไว้
        if (!$firstname) $firstname = $request->input('firstname');
        if (!$lastname) $lastname = $request->input('lastname');
        if (!$birthDate) $birthDate = $request->input('birthDate');

        // ค้นหา
        $members = Member::findMatchingMember($gender, $firstname, $lastname, $birthDate);

        // Calculate age for display
        $members->transform(function ($member) {
            $member->age_years = $member->birthdate ? \Carbon\Carbon::parse($member->birthdate)->age : '-';
            return $member;
        });


        return response()->json([
            'count' => $members->count(),
            'members' => $members
        ]);
    }

    public function reportMemberNotFound(Request $request)
    {
        // รับค่าจาก ajax
        $gender = $request->input('gender');
        $firstname = $request->input('first_name'); // form name="first_name"
        $lastname = $request->input('last_name');   // form name="last_name"
        $birthDate = $request->input('birth_date'); // form name="birth_date" (Y-m-d)
        $phone = $request->input('phone'); // form name="phone" (0898889999)
        $email = $request->input('email'); // form name="email" (0898889999)


        $ip = $request->ip();
        $ipv4 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ip : null;
        $ipv6 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ip : null;

        $reportCase = ReportCase::create([
            'gender' => $gender,
            'name' => $firstname,
            'surname' => $lastname,
            'birthdate' => $birthDate,
            'phone' => $phone,
            'email' => $email,
            'ipv4' => $ipv4,
            'ipv6' => $ipv6,
            'city' => $request->input('city'),
            'province' => $request->input('province'),
            'country' => $request->input('country'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Report case created successfully.',
            'report_case_id' => $reportCase->id
        ]);
    }
}
