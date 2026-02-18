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
}
