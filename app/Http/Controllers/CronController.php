<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CronController extends Controller
{
    //
    public function checkAndCancelPastApplications()
    {
        // 1. Get the date 2 weeks ago
        $twoWeeksAgo = \Carbon\Carbon::now()->subWeeks(2);

        // 2. Find courses that ended more than 2 weeks ago
        $pastCoursesIds = \App\Models\Course::where('date_end', '<=', $twoWeeksAgo)->pluck('id');

        if ($pastCoursesIds->isEmpty()) {
            return response()->json([
                'message' => 'No past courses found to process.',
                'date_threshold' => $twoWeeksAgo->toDateTimeString()
            ]);
        }

        // 3. Update applications for these courses
        // Query all apply which state is not "ผ่านการอบรม"
        // Also excluding "ยกเลิกสมัคร" to prevent re-updating already cancelled ones (optional but good for performance)
        $affectedRows = \App\Models\Apply::whereIn('course_id', $pastCoursesIds)
            ->where('state', '!=', 'ผ่านการอบรม')
            ->where('state', '!=', 'ยุติกลางคัน')
            ->where('state', '!=', 'ยกเลิกสมัคร') // Add this to avoid updating already cancelled records
            ->update(['state' => 'ยกเลิกสมัคร']);

        return response()->json([
            'message' => "Updated $affectedRows applications to 'ยกเลิกสมัคร'",
            'course_count' => $pastCoursesIds->count(),
            'date_threshold' => $twoWeeksAgo->toDateTimeString()
        ]);
    }
    /**
     * ตัดรอบ 7 วันก่อนเริ่มคอร์ส: ปิดรับสมัคร, ยืนยันอัตโนมัติจนเต็ม, ยกเลิกส่วนที่เกิน + แจ้งผู้สมัคร
     *
     * GET /admin/cron/applyCutoff        ทำจริง
     * GET /admin/cron/applyCutoff?dry=1  แสดงผลที่จะเกิดขึ้น โดยไม่แก้ข้อมูลและไม่ส่งแจ้งเตือน
     */
    public function applyCutoff(Request $request)
    {
        return response()->json(
            app(\App\Services\CourseAutoConfirmService::class)->runCutoff(null, $request->boolean('dry'))
        );
    }

    public function closeCourseAuto()
    {
        $today = \Carbon\Carbon::now()->toDateString();

        $affectedRows = \App\Models\Course::where('state', 'เปิดรับสมัคร')
            ->whereDate('date_start', '<=', $today)
            ->update(['state' => 'ปิดรับสมัคร']);

        return response()->json([
            'message' => "Updated $affectedRows courses to 'ปิดรับสมัคร'",
            'date_checked' => $today
        ]);
    }

    public function updateMemberStatus()
    {
        $categoryIds = \App\Models\CourseCategory::VIPASSANA_PASSED_CATEGORY_IDS;

        // Find members who are not "ศิษย์เตโชวิปัสสนา" 
        // and have at least one apply with state "ผ่านการอบรม" 
        // for a course in the specified categories.
        $affectedRows = \App\Models\Member::where(function ($query) {
            // Ensure we only update those who are not already "ศิษย์เตโชวิปัสสนา"
            $query->where('status', '!=', 'ศิษย์เตโชวิปัสสนา')
                ->orWhereNull('status');
        })
            ->whereExists(function ($query) use ($categoryIds) {
                $query->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('applies')
                    ->join('courses', 'applies.course_id', '=', 'courses.id')
                    ->whereColumn('applies.member_id', 'members.id')
                    ->where('applies.state', 'ผ่านการอบรม')
                    ->whereIn('courses.category_id', $categoryIds);
            })
            ->update(['status' => 'ศิษย์เตโชวิปัสสนา']);

        // Update ผู้สมัครใหม่ to  ศิษย์อานาฯ ๑ วัน 
        $categoryIdsAna = [9, 11, 18];

        $affectedRowsAna = \App\Models\Member::where(function ($query) {
            $query->where('status', 'ผู้สมัครใหม่')
                ->orWhereNull('status');
        })
            ->whereExists(function ($query) use ($categoryIdsAna) {
                $query->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('applies')
                    ->join('courses', 'applies.course_id', '=', 'courses.id')
                    ->whereColumn('applies.member_id', 'members.id')
                    ->where('applies.state', 'ผ่านการอบรม')
                    ->whereIn('courses.category_id', $categoryIdsAna);
            })
            ->update(['status' => 'ศิษย์อานาฯ ๑ วัน']);


        // Update ผู้สมัครใหม่ or  ศิษย์อานาฯ ๑ วัน to ศิษย์อานาปานสติ
        $categoryIdsAna2 = [5, 10, 13, 14, 17];

        $affectedRowsAna2 = \App\Models\Member::where(function ($query) {
            $query->where('status', 'ผู้สมัครใหม่')
                ->orWhere('status', 'ศิษย์อานาฯ ๑ วัน');
        })
            ->whereExists(function ($query) use ($categoryIdsAna2) {
                $query->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('applies')
                    ->join('courses', 'applies.course_id', '=', 'courses.id')
                    ->whereColumn('applies.member_id', 'members.id')
                    ->where('applies.state', 'ผ่านการอบรม')
                    ->whereIn('courses.category_id', $categoryIdsAna2);
            })
            ->update(['status' => 'ศิษย์อานาปานสติ']);


        return response()->json([
            'message' => "Updated $affectedRows members to 'ศิษย์เตโชวิปัสสนา' and $affectedRowsAna members to 'ศิษย์อานาฯ ๑ วัน' and $affectedRowsAna2 members to 'ศิษย์อานาปานสติ'"
        ]);
    }
}
