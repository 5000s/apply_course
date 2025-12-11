<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminMemberController extends Controller
{
    public function profile()
    {
        ini_set('memory_limit', '256M');


        // Retrieve all members related to this user by email
        $members = Member::where("surname", "!=", "")->get();

        // Pass the member data to the view
        return view('admin.profile_list', ['members' => $members, 'title' => 'รายการสมาชิก']);
    }


    public function getMemberType()
    {
        $data = [];

        $ana_rows = DB::table('applies as a')
            ->join('members as m', 'm.id', '=', 'a.member_id')
            ->join('courses as c', 'c.id', '=', 'a.course_id')
            ->join('course_categories as cc', 'cc.id', '=', 'c.category_id')
            ->select([
                'a.member_id',
                DB::raw("CONCAT_WS(' ', m.name, m.surname) as member_name"),
                'm.email',
                DB::raw("GROUP_CONCAT(DISTINCT c.location ORDER BY c.location SEPARATOR ', ') as locations_attended"),
                DB::raw("MIN(c.date_start) as first_course_date"),
                DB::raw("MAX(c.date_start) as last_course_date"),
            ])
            ->where('a.state', 'ผ่านการอบรม')
            ->whereIn('cc.id', [9, 11])
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('applies as a2')
                    ->join('courses as c2', 'a2.course_id', '=', 'c2.id')
                    ->join('course_categories as cc2', 'c2.category_id', '=', 'cc2.id')
                    ->whereColumn('a2.member_id', 'a.member_id')
                    ->where('a2.state', 'ผ่านการอบรม')
                    ->where(function ($qq) {
                        $qq->where('cc2.show_name', 'like', '%วิปัส%')
                            ->orWhereIn('cc2.id', [5, 13, 14]); // <- fixed: cc2.id
                    });
            })
            ->groupBy('a.member_id', 'm.name', 'm.surname', 'm.email')
            ->orderBy('member_name')
            ->get();


        $ana_3d_rows = DB::table('applies as a')
            ->join('members as m', 'm.id', '=', 'a.member_id')
            ->join('courses as c', 'c.id', '=', 'a.course_id')
            ->join('course_categories as cc', 'cc.id', '=', 'c.category_id')
            ->select([
                'a.member_id',
                DB::raw("CONCAT_WS(' ', m.name, m.surname) as member_name"),
                'm.email',
                DB::raw("GROUP_CONCAT(DISTINCT c.location ORDER BY c.location SEPARATOR ', ') as locations_attended"),
                DB::raw("MIN(c.date_start) as first_course_date"),
                DB::raw("MAX(c.date_start) as last_course_date"),
            ])
            ->where('a.state', 'ผ่านการอบรม')
            ->whereIn('cc.id', [5, 13, 14])
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('applies as a2')
                    ->join('courses as c2', 'a2.course_id', '=', 'c2.id')
                    ->join('course_categories as cc2', 'c2.category_id', '=', 'cc2.id')
                    ->whereColumn('a2.member_id', 'a.member_id')
                    ->where('a2.state', 'ผ่านการอบรม')
                    ->where('cc2.show_name', 'like', '%วิปัส%');
            })
            ->groupBy('a.member_id', 'm.name', 'm.surname', 'm.email')
            ->orderBy('member_name')
            ->get();


        $techo_rows = DB::table('applies as a')
            ->join('members as m', 'm.id', '=', 'a.member_id')
            ->join('courses as c', 'c.id', '=', 'a.course_id')
            ->join('course_categories as cc', 'cc.id', '=', 'c.category_id')
            ->select([
                'a.member_id',
                DB::raw("CONCAT_WS(' ', m.name, m.surname) as member_name"),
                'm.email',
                DB::raw("GROUP_CONCAT(DISTINCT c.location ORDER BY c.location SEPARATOR ', ') as locations_attended"),
                DB::raw("MIN(c.date_start) as first_course_date"),
                DB::raw("MAX(c.date_start) as last_course_date"),
            ])
            ->where('a.state', 'ผ่านการอบรม')
            ->where('cc.show_name', 'like', '%วิปัส%')
            ->groupBy('a.member_id', 'm.name', 'm.surname', 'm.email')
            ->orderBy('member_name')
            ->get();

        $data['ana'] = $ana_rows;
        $data['ana_3d'] = $ana_3d_rows;
        $data['techo'] = $techo_rows;


        return view('admin.member_type',  compact('data'));
    }
    public function senior()
    {
        ini_set('memory_limit', '256M');
        $members = Member::where("surname", "!=", "")->where('current_level', '!=', '0')->get();
        return view('admin.senior_member_list', ['members' => $members, 'title' => 'ตารางศิษย์อาวุโส (senior level)']);
    }

    public function editSenior($id)
    {
        $member = Member::findOrFail($id);
        return view('admin.senior_member_edit', compact('member'));
    }

    public function updateSenior(Request $request, $id)
    {
        $request->validate([
            'leave_date' => 'nullable|date',
            'death_date' => 'nullable|date',
            'leave_description' => 'nullable|string',
            'promote_level' => 'nullable|integer|min:1|max:4',
            'promote_date' => 'required_with:promote_level|nullable|date',
        ]);

        $member = Member::findOrFail($id);
        
        $updateData = [
            'leave_date' => $request->leave_date,
            'death_date' => $request->death_date,
            'leave_description' => $request->leave_description,
        ];

        // If promote_level is set, update current_level and the specific level date
        if ($request->filled('promote_level')) {
            $newLevel = (int) $request->promote_level;
            
            // 1. Always update the date for the selected level (allows backfilling)
            if ($request->filled('promote_date')) {
                $updateData["level_{$newLevel}_date"] = $request->promote_date;
            }

            // 2. Only update current_level if the new level is higher than the current one
            $currentLevelInt = (int) $member->current_level;
            
            \Illuminate\Support\Facades\Log::info("Updating Senior ID: {$id}", [
                'promote_level_req' => $request->promote_level,
                'newLevel' => $newLevel,
                'currentLevelRaw' => $member->current_level,
                'currentLevelInt' => $currentLevelInt,
                'check' => ($newLevel > $currentLevelInt)
            ]);

            if ($newLevel > $currentLevelInt) {
                 // Cast to string to prevent MySQL interpreting int as ENUM index
                 $updateData['current_level'] = (string) $newLevel;
            }
        }
        
        \Illuminate\Support\Facades\Log::info("Update Data:", $updateData);

        $member->update($updateData);

        return redirect()->route('admin.members.senior')->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }
}
