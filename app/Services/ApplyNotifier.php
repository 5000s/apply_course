<?php

namespace App\Services;

use App\Models\Apply;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Location;
use App\Models\Member;
use Illuminate\Support\Facades\Log;

/**
 * อีเมล / SMS ถึงผู้สมัครคอร์ส
 * ทุกเมธอดไม่ throw: ส่งไม่สำเร็จจะบันทึก log แล้วคืนค่า false เพื่อไม่ให้ขั้นตอนหลักล้ม
 */
class ApplyNotifier
{
    public function __construct(private SmsSender $sms)
    {
    }

    // ภาษาในการแจ้งผู้สมัคร: สัญชาติไทย (หรือไม่ระบุ) = th นอกนั้น = en
    public function langFor(?Member $member): string
    {
        return in_array($member->nationality ?? null, [null, '', 'ไทย'], true) ? 'th' : 'en';
    }

    // อีเมลผลการสมัคร: ยืนยันแล้ว (complete_ana) หรือ รอเจ้าหน้าที่ยืนยัน (complete_non_confirm)
    public function sendApplyEmail(Apply $apply, bool $isConfirmed, ?string $lang = null): bool
    {
        $lang = $this->resolveLang($apply, $lang);

        $subject = $isConfirmed
            ? ($lang === 'th' ? 'ยืนยันสมัครคอร์สเรียบร้อย' : 'Application Confirmed')
            : ($lang === 'th' ? 'ได้รับใบสมัครของท่านแล้ว' : 'Application Received');

        $view = $isConfirmed ? 'apply.complete_ana' : 'apply.complete_non_confirm';

        return $this->sendCourseEmail($apply, $view, $subject, $lang, [], true);
    }

    /**
     * แจ้งผู้สมัครว่ายืนยันการสมัครแล้ว ทั้งอีเมลและ SMS (ใช้ทั้งตอนยืนยันทันทีและตอนตัดรอบ 7 วัน)
     * SMS แจ้งเวลาที่ควรมาถึง และเบอร์กรณีฉุกเฉินของสถานที่ (ไม่มี = เบอร์มูลนิธิฯ)
     *
     * @return array{email: bool, sms: bool}
     */
    public function sendConfirmedNotice(Apply $apply, ?string $lang = null): array
    {
        $lang = $this->resolveLang($apply, $lang);
        $course = $apply->course;

        $time = $course ? $course->getCourseStartTime($lang) : null;
        $location = $course ? Location::find($course->location_id) : null;
        $phone = trim((string) ($location->phone ?? '')) ?: SmsSender::CONTACT_PHONE;

        $sms = [
            'th' => 'ยืนยันการสมัคร :course :date ที่ :location เรียบร้อยแล้ว:arrive หากมีเหตุไม่สามารถมาได้ กรุณาแจ้ง โทร. :phone',
            'en' => 'Confirmed: :course, :date at :location.:arrive If you cannot attend, please call :phone',
        ];
        $arrive = $time ? ($lang === 'th' ? " กรุณามาถึงก่อน {$time}" : ' Please arrive before ' . rtrim($time, '.') . '.') : '';
        if ($lang === 'en') {
            // SMS ภาษาอังกฤษ: ตัดชื่อผู้ติดต่อภาษาไทยในวงเล็บออก เช่น "091-416-6008 (คุณเจษฏา)"
            $phone = trim(preg_replace('/\s*\([^)]*\p{Thai}[^)]*\)/u', '', $phone)) ?: SmsSender::CONTACT_PHONE;
        }

        return [
            'email' => $this->sendApplyEmail($apply, true, $lang),
            'sms' => $this->sendCourseSms($apply, $lang, $sms[$lang], [':arrive' => $arrive, ':phone' => $phone]),
        ];
    }

    /**
     * แจ้งผู้สมัครว่าอยู่ในรายชื่อสำรองลำดับที่ N (จะแจ้งยืนยันสถานะอีกครั้งตอนตัดรอบ 7 วันก่อนเริ่มคอร์ส)
     *
     * @return array{email: bool, sms: bool}
     */
    public function sendWaitingListNotice(Apply $apply, int $position, ?string $lang = null): array
    {
        $lang = $this->resolveLang($apply, $lang);
        $subject = $lang === 'th' ? "ใบสมัครของท่านอยู่ในรายชื่อสำรองลำดับที่ {$position}" : "Your application is on the waiting list (No. {$position})";

        $days = Course::APPLY_CUTOFF_DAYS;
        $sms = [
            'th' => "มูลนิธิฯ ได้รับใบสมัคร :course :date ที่ :location แล้ว ท่านอยู่ในรายชื่อสำรองลำดับที่ {$position} จะแจ้งยืนยันสถานะอีกครั้ง {$days} วันก่อนวันเริ่มคอร์ส โทร. :phone",
            'en' => "Application received: :course, :date at :location. You are No. {$position} on the waiting list. We will confirm your status {$days} days before the course starts. Tel. :phone",
        ];

        return [
            'email' => $this->sendCourseEmail($apply, 'apply.complete_non_confirm', $subject, $lang, ['waiting_position' => $position], true),
            'sms' => $this->sendCourseSms($apply, $lang, $sms[$lang]),
        ];
    }

    // อีเมลแจ้งเตือนก่อนเริ่มคอร์ส (รายละเอียดเดียวกับอีเมลยืนยัน + ข้อความแจ้งเตือนด้านบน)
    public function sendReminderEmail(Apply $apply, int $daysBefore, ?string $lang = null): bool
    {
        $lang = $this->resolveLang($apply, $lang);
        $subject = $lang === 'th' ? 'แจ้งเตือนการเข้าคอร์ส' : 'Course Reminder';

        return $this->sendCourseEmail($apply, 'apply.complete_ana', $subject, $lang, [
            'is_reminder' => true,
            'reminder_days' => $daysBefore,
        ], true);
    }

    /**
     * แจ้งผู้สมัครที่ถูกยกเลิกเพราะที่นั่งเต็ม (ตอนตัดรอบ 7 วัน) ทั้งอีเมลและ SMS
     *
     * @return array{email: bool, sms: bool}
     */
    public function notifyCancelledFull(Apply $apply): array
    {
        $lang = $this->resolveLang($apply, null);
        $subject = $lang === 'th' ? 'ขออภัย ที่นั่งเต็มแล้ว' : 'Sorry, the course is fully booked';

        $sms = [
            'th' => 'ขออภัย :course :date ที่ :location ที่นั่งเต็มแล้ว ใบสมัครของท่านจึงถูกยกเลิก สอบถาม โทร. :phone',
            'en' => 'Sorry, :course, :date at :location is fully booked, so your application has been cancelled. Tel. :phone',
        ];

        return [
            'email' => $this->sendCourseEmail($apply, 'apply.cancelled_full', $subject, $lang, [], false),
            'sms' => $this->sendCourseSms($apply, $lang, $sms[$lang]),
        ];
    }

    /**
     * ยืนยันกับผู้สมัครว่ายกเลิกคอร์สเรียบร้อยแล้ว (ผู้สมัครกดยกเลิกเอง) ทั้งอีเมลและ SMS
     *
     * @return array{email: bool, sms: bool}
     */
    public function notifyCancelledByUser(Apply $apply, ?string $lang = null): array
    {
        $lang = $this->resolveLang($apply, $lang);
        $subject = $lang === 'th' ? 'ยกเลิกการสมัครเรียบร้อย' : 'Registration Cancelled';

        $sms = [
            'th' => 'มูลนิธิฯ ได้รับคำขอยกเลิก :course :date ที่ :location เรียบร้อยแล้ว สมัครคอร์สอื่นได้ที่ bodhidhammayan.org โทร. :phone',
            'en' => 'Your cancellation of :course, :date at :location has been received. Other courses: bodhidhammayan.org Tel. :phone',
        ];

        return [
            'email' => $this->sendCourseEmail($apply, 'apply.cancelled_by_user', $subject, $lang, [], false),
            'sms' => $this->sendCourseSms($apply, $lang, $sms[$lang]),
        ];
    }

    /**
     * รูปกฎระเบียบที่ฝังท้ายอีเมลสมัครคอร์ส (ไฟล์อยู่ที่ public/images/rules, ฝังด้วย Content-ID)
     * วิปัสสนา (> 6 วัน) = กฎวิปัสสนา, คอร์ส 1 วัน = กฎทั่วไป + การแต่งกายคอร์ส 1 วัน, นอกนั้น = กฎทั่วไป
     *
     * @return array<int, array{cid: string, path: string, src: string, alt: string}>
     */
    public function ruleImages(Course $course): array
    {
        $image = function (string $file, string $alt) {
            $cid = pathinfo($file, PATHINFO_FILENAME) . '@bodhidhammayan';

            return [
                'cid' => $cid,
                'path' => public_path("images/rules/{$file}"),
                'src' => "cid:{$cid}",
                'alt' => $alt,
            ];
        };

        if ($course->isVipassana()) {
            return [$image('rules_vipassana.jpg', 'กฎระเบียบและข้อปฏิบัติในการเข้ารับการอบรมวิปัสสนากรรมฐาน')];
        }

        $rules = [$image('rules_general.jpg', 'กฎระเบียบและข้อปฏิบัติในการเข้าร่วมอบรม')];

        $isOneDay = $course->date_start && $course->date_end && $course->date_start->isSameDay($course->date_end);
        if ($isOneDay) {
            $rules[] = $image('dress_one_day.jpg', 'การแต่งกายที่เหมาะสม สำหรับคอร์ส 1 วัน');
        }

        return $rules;
    }

    private function resolveLang(Apply $apply, ?string $lang): string
    {
        if (in_array($lang, ['th', 'en'], true)) {
            return $lang;
        }

        return $this->langFor($apply->member);
    }

    private function sendCourseEmail(Apply $apply, string $view, string $subject, string $lang, array $extra, bool $withRuleImages): bool
    {
        $member = $apply->member;
        $course = $apply->course;

        if (!$member || trim((string) $member->email) === '' || !$course) {
            Log::info("Apply email skipped ({$view}): apply {$apply->id} has no member email or course");
            return false;
        }

        try {
            $location = Location::find($course->location_id);
            $courseCategory = CourseCategory::find($course->category_id);

            $courseTitle = $lang === 'th'
                ? ($courseCategory->show_name ?? $course->category)
                : ($courseCategory->show_name_en ?? $courseCategory->show_name ?? $course->category);

            $ruleImages = $withRuleImages ? $this->ruleImages($course) : [];

            (new GmailApiService())->sendEmail($member->email, "{$subject} - {$courseTitle}", $view, array_merge([
                'apply' => $apply,
                'course' => $course,
                'location' => $location,
                'courseCategory' => $courseCategory,
                'lang' => $lang,
                'is_email' => true,
                'rule_images' => $ruleImages,
            ], $extra), null, array_column($ruleImages, 'path', 'cid'));

            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send apply email ({$view}) for apply {$apply->id}: " . $e->getMessage());
            return false;
        }
    }

    // SMS เกี่ยวกับคอร์ส: แทนค่า :course :date :location :phone (+ ค่าเพิ่มเติมใน $extra) ในข้อความตามภาษา
    private function sendCourseSms(Apply $apply, string $lang, string $template, array $extra = []): bool
    {
        $phone = $this->sms->memberMobile($apply->member);
        $course = $apply->course;

        if (!$phone || !$course) {
            return false;
        }

        if ($lang === 'th') {
            $parts = [$course->category, $course->getCourseLongDateTxtAttribute(true), $course->location];
        } else {
            $category = CourseCategory::find($course->category_id);
            $location = Location::find($course->location_id);
            $parts = [
                $category->show_name_en ?? $course->category,
                $course->getCourseLongDateTxtEnAttribute(true),
                $location->show_name_en ?? $course->location,
            ];
        }

        $message = strtr($template, array_merge([
            ':course' => $parts[0],
            ':date' => $parts[1],
            ':location' => $parts[2],
            ':phone' => SmsSender::CONTACT_PHONE,
        ], $extra));

        return $this->sms->send($phone, $message);
    }
}
