<?php

namespace App\Services;

use App\Models\Apply;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseLocationLimit;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * ระบบ Auto Confirm ใบสมัครคอร์ส
 *
 * ก่อนตัดรอบ (สมัครก่อนเริ่มคอร์สมากกว่า 7 วัน) — ตัดสินตอนกดสมัคร:
 *   - คอร์สวิปัสสนา (> 6 วัน): ศิษย์วิปัสสนาแล้ว + ที่นั่งที่ยืนยันแล้วยังไม่ถึง 50% → ยืนยันทันที
 *                              ยังไม่เคยเข้าวิปัสสนา → รอเจ้าหน้าที่ยืนยัน
 *   - คอร์สสั้น (อานาฯ / การเดินทางสู่จิตใจ): ที่นั่งที่ยืนยันแล้วยังไม่ถึง 50% → ยืนยันทันที
 *
 * วันตัดรอบ (ก่อนเริ่มคอร์ส 7 วันพอดี) — runCutoff() รันทุกวันจาก scheduler:
 *   1. ปิดรับสมัครคอร์ส
 *   2. ยืนยันใบสมัครที่ค้าง เรียงตามเวลาที่สมัคร จนที่นั่งเต็ม 100% (ศิษย์ใหม่ของคอร์สวิปัสสนายังรอเจ้าหน้าที่)
 *   3. ใบสมัครที่ยังค้างและที่นั่งของเพศนั้นเต็มแล้ว → ยกเลิกสมัคร + แจ้งทางอีเมล/SMS
 *
 * ที่นั่งนับเฉพาะผู้เข้าอบรมที่ "ยืนยันแล้ว" เทียบกับจำนวนรับในหน้า "ตั้งค่าจำนวนรับ" (course_limit)
 */
class CourseAutoConfirmService
{
    const STATE_PENDING = 'ยื่นใบสมัคร';
    const STATE_CONFIRMED = 'ยืนยันแล้ว';
    const STATE_CANCELLED = 'ยกเลิกสมัคร';
    const STATE_PASSED = 'ผ่านการอบรม';
    const ROLE_PARTICIPANT = 'ผู้เข้าอบรม';

    const COURSE_OPEN = 'เปิดรับสมัคร';
    const COURSE_CLOSED = 'ปิดรับสมัคร';
    const COURSE_CANCELLED = 'ยกเลิกคอร์ส';

    const VIPASSANA_STUDENT_STATUS = 'ศิษย์เตโชวิปัสสนา';

    // ยังไม่ได้ตั้งค่าจำนวนรับในหน้า "ตั้งค่าจำนวนรับ"
    const DEFAULT_LIMIT = 99;

    const SYSTEM_USER = 'ระบบ';
    const REMARK_CONFIRM_ON_APPLY = 'ระบบยืนยันการสมัคร';
    const REMARK_CONFIRM_CUTOFF = 'ระบบยืนยันการสมัคร (ตัดรอบ 7 วันก่อนเริ่มคอร์ส)';
    const REMARK_CANCEL_FULL = 'ระบบยกเลิก: ที่นั่งเต็ม (ตัดรอบ 7 วันก่อนเริ่มคอร์ส)';

    public function __construct(private ApplyNotifier $notifier)
    {
    }

    /**
     * จำนวนรับของประเภทคอร์ส + สถานที่ (ไม่มีการตั้งค่า = 99 ทุกช่อง)
     * ถ้ามีข้อมูลซ้ำคู่เดียวกัน ใช้แถวแรก (id น้อยสุด)
     */
    public function courseLimit($categoryId, $locationId): array
    {
        $row = CourseLocationLimit::where('course_category_id', $categoryId)
            ->where('location_id', $locationId)
            ->orderBy('id')
            ->first();

        if (!$row) {
            return ['male_limit' => self::DEFAULT_LIMIT, 'female_limit' => self::DEFAULT_LIMIT, 'max_limit' => self::DEFAULT_LIMIT];
        }

        return [
            'male_limit' => (int) $row->male_limit,
            'female_limit' => (int) $row->female_limit,
            'max_limit' => (int) $row->max_limit,
        ];
    }

    // จำนวนผู้เข้าอบรมที่ยืนยันแล้ว แยกชาย/หญิง
    public function confirmedCounts(int $courseId): array
    {
        $genders = Apply::query()
            ->join('members', 'members.id', '=', 'applies.member_id')
            ->where('applies.course_id', $courseId)
            ->where('applies.role', self::ROLE_PARTICIPANT)
            ->where('applies.state', self::STATE_CONFIRMED)
            ->where(fn($q) => $q->whereNull('applies.cancel')->orWhere('applies.cancel', '!=', 1))
            ->pluck('members.gender');

        return [
            'male' => $genders->filter(fn($g) => CourseSeatPolicy::genderKey($g) === 'male')->count(),
            'female' => $genders->filter(fn($g) => CourseSeatPolicy::genderKey($g) === 'female')->count(),
            'total' => $genders->count(),
        ];
    }

    // ศิษย์วิปัสสนาแล้ว: สถานะสมาชิก หรือ เคยผ่านการอบรมคอร์สหมวดวิปัสสนา (กรณี cron ยังไม่ได้ปรับสถานะ)
    public function isVipassanaStudent(Member $member): bool
    {
        if ($member->status === self::VIPASSANA_STUDENT_STATUS) {
            return true;
        }

        return Apply::where('member_id', $member->id)
            ->where('state', self::STATE_PASSED)
            ->whereHas('course', fn($q) => $q->whereIn('category_id', CourseCategory::VIPASSANA_PASSED_CATEGORY_IDS))
            ->exists();
    }

    // คอร์สวิปัสสนา + ยังไม่เคยเข้าวิปัสสนา → ต้องให้เจ้าหน้าที่ยืนยันเสมอ
    public function needsStaffConfirm(Course $course, Member $member): bool
    {
        return $course->isVipassana() && !$this->isVipassanaStudent($member);
    }

    /**
     * ตอนกดสมัคร: ยืนยันอัตโนมัติได้หรือไม่ (ใบสมัครใหม่ยังเป็น "ยื่นใบสมัคร" จึงไม่ถูกนับในที่นั่ง)
     * ปกติหลังตัดรอบหน้าเว็บจะปิดรับสมัครแล้ว ถ้ายังมีใบสมัครเข้ามาให้ใช้เกณฑ์ 100%
     */
    public function shouldAutoConfirmOnApply(Course $course, Member $member): bool
    {
        if ($this->needsStaffConfirm($course, $member)) {
            return false;
        }

        $percent = $course->isPastApplyCutoff()
            ? CourseSeatPolicy::FULL_PERCENT
            : CourseSeatPolicy::EARLY_CONFIRM_PERCENT;

        return CourseSeatPolicy::hasSeat(
            $this->courseLimit($course->category_id, $course->location_id),
            $this->confirmedCounts($course->id),
            $member->gender,
            $percent
        );
    }

    /**
     * ลำดับในรายชื่อสำรอง (เริ่มที่ 1) ของใบสมัครที่ยังไม่ได้ยืนยัน = ลำดับที่จะถูกพิจารณายืนยันตอนตัดรอบ
     * นับเฉพาะใบสมัครที่ระบบยืนยันให้ได้ตอนตัดรอบ เรียงตามเวลาที่สมัคร
     * คืน null ถ้าไม่ใช่รายชื่อสำรอง (ศิษย์ใหม่ของคอร์สวิปัสสนา หรือ จำนวนรับเป็น 0 = เจ้าหน้าที่ยืนยันเอง)
     */
    public function waitingListPosition(Course $course, Apply $apply): ?int
    {
        $member = $apply->member;
        $limits = $this->courseLimit($course->category_id, $course->location_id);

        if (!$member || $this->needsStaffConfirm($course, $member) || CourseSeatPolicy::isStaffManaged($limits, $member->gender)) {
            return null;
        }

        $queue = $this->pendingApplies($course->id)
            ->filter(fn(Apply $pending) => $pending->member && !$this->needsStaffConfirm($course, $pending->member))
            ->values();

        $index = $queue->search(fn(Apply $pending) => $pending->id === $apply->id);

        return $index === false ? null : $index + 1;
    }

    /**
     * ตัดรอบคอร์สที่เริ่มในอีก 7 วันพอดี (รันวันละครั้ง; รันซ้ำวันเดียวกันได้ ไม่ยืนยัน/แจ้งซ้ำ)
     * + เก็บตกคอร์สที่เลยวันตัดรอบแล้วแต่ยัง "เปิดรับสมัคร" อยู่ (cron ไม่ได้รัน / เลื่อนวัน / สร้างคอร์สกระชั้นชิด)
     *   คอร์สที่ตัดรอบแล้วจะถูกปิดรับสมัคร จึงไม่ถูกประมวลผลซ้ำในวันถัดไป
     */
    public function runCutoff(?Carbon $today = null, bool $dryRun = false): array
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();
        $startDate = $today->copy()->addDays(Course::APPLY_CUTOFF_DAYS)->toDateString();
        $tomorrow = $today->copy()->addDay()->toDateString();

        $courses = Course::where('state', '!=', self::COURSE_CANCELLED)
            ->where(function ($q) use ($startDate, $tomorrow) {
                $q->whereDate('date_start', $startDate)
                    ->orWhere(fn($late) => $late->where('state', self::COURSE_OPEN)
                        ->whereDate('date_start', '>=', $tomorrow)
                        ->whereDate('date_start', '<', $startDate));
            })
            ->orderBy('date_start')
            ->orderBy('id')
            ->get();

        $result = ['date_start' => $startDate, 'dry_run' => $dryRun, 'courses' => []];

        foreach ($courses as $course) {
            try {
                $result['courses'][] = $this->cutoffCourse($course, $dryRun);
            } catch (\Throwable $e) {
                Log::error("Apply cutoff failed for course {$course->id}: " . $e->getMessage());
                $result['courses'][] = ['course_id' => $course->id, 'error' => $e->getMessage()];
            }
        }

        Log::info('Apply cutoff', $result);

        return $result;
    }

    private function cutoffCourse(Course $course, bool $dryRun): array
    {
        $summary = [
            'course_id' => $course->id,
            'course' => $course->category,
            'location' => $course->location,
            'closed' => false,
            'limits' => $this->courseLimit($course->category_id, $course->location_id),
            'confirmed' => [],
            'cancelled' => [],
            'waiting_staff' => [],
            'notify_failed' => [], // สถานะเปลี่ยนแล้วแต่แจ้งผู้สมัครไม่สำเร็จ → เจ้าหน้าที่ต้องติดต่อเอง
        ];

        if ($course->state === self::COURSE_OPEN) {
            $summary['closed'] = true;
            if (!$dryRun) {
                $course->state = self::COURSE_CLOSED;
                $course->save();
            }
        }

        $counts = $this->confirmedCounts($course->id);
        $summary['confirmed_before'] = $counts;

        // รอบที่ 1: ยืนยันตามลำดับเวลาที่สมัคร จนเต็ม 100%
        $remaining = [];
        foreach ($this->pendingApplies($course->id) as $apply) {
            $member = $apply->member;
            $canAutoConfirm = $member && !$this->needsStaffConfirm($course, $member)
                && CourseSeatPolicy::hasSeat($summary['limits'], $counts, $member->gender, CourseSeatPolicy::FULL_PERCENT);

            if (!$canAutoConfirm) {
                $remaining[] = $apply;
                continue;
            }

            $counts = CourseSeatPolicy::withConfirmed($counts, $member->gender);
            $summary['confirmed'][] = $this->applyRow($apply);
            if (!$dryRun) {
                $sent = $this->confirm($apply);
                if (!$sent['email'] || !$sent['sms']) {
                    $summary['notify_failed'][] = $this->applyRow($apply) + ['action' => 'confirmed'] + $sent;
                }
            }
        }

        // รอบที่ 2: ที่ยังค้างอยู่ ถ้าที่นั่งของเพศนั้นเต็มแล้ว → ยกเลิก นอกนั้นรอเจ้าหน้าที่ (ศิษย์ใหม่วิปัสสนา / จำนวนรับเป็น 0)
        foreach ($remaining as $apply) {
            if (CourseSeatPolicy::isFull($summary['limits'], $counts, $apply->member->gender ?? null)) {
                $summary['cancelled'][] = $this->applyRow($apply);
                if (!$dryRun) {
                    $sent = $this->cancelFull($apply);
                    if (!$sent['email'] || !$sent['sms']) {
                        $summary['notify_failed'][] = $this->applyRow($apply) + ['action' => 'cancelled'] + $sent;
                    }
                }
            } else {
                $summary['waiting_staff'][] = $this->applyRow($apply);
            }
        }

        $summary['confirmed_after'] = $counts;

        if ($summary['notify_failed']) {
            Log::warning("Apply cutoff: notification failed for course {$course->id}", $summary['notify_failed']);
        }

        return $summary;
    }

    private function pendingApplies(int $courseId)
    {
        return Apply::with(['member', 'course'])
            ->where('course_id', $courseId)
            ->where('role', self::ROLE_PARTICIPANT)
            ->where('state', self::STATE_PENDING)
            ->where(fn($q) => $q->whereNull('cancel')->orWhere('cancel', '!=', 1))
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
    }

    /** @return array{email: bool, sms: bool} ผลการแจ้งยืนยันทางอีเมล/SMS */
    private function confirm(Apply $apply): array
    {
        $apply->state = self::STATE_CONFIRMED;
        $apply->remark = $this->appendRemark($apply->remark, self::REMARK_CONFIRM_CUTOFF);
        $apply->updated_by = self::SYSTEM_USER;
        $apply->save();

        return $this->notifier->sendConfirmedNotice($apply);
    }

    /** @return array{email: bool, sms: bool} */
    private function cancelFull(Apply $apply): array
    {
        $apply->state = self::STATE_CANCELLED;
        $apply->cancel = 1;
        $apply->cancel_at = now();
        $apply->remark = $this->appendRemark($apply->remark, self::REMARK_CANCEL_FULL);
        $apply->updated_by = self::SYSTEM_USER;
        $apply->save();

        return $this->notifier->notifyCancelledFull($apply);
    }

    private function appendRemark(?string $remark, string $note): string
    {
        $remark = trim((string) $remark);

        return $remark === '' ? $note : "{$remark} | {$note}";
    }

    private function applyRow(Apply $apply): array
    {
        $member = $apply->member;

        return [
            'apply_id' => $apply->id,
            'name' => $member ? trim("{$member->name} {$member->surname}") : null,
            'gender' => $member->gender ?? null,
            'applied_at' => (string) $apply->created_at,
        ];
    }
}
