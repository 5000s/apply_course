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
        // Return view without loading all members
        return view('admin.profile_list', ['title' => 'รายการสมาชิก']);
    }

    public function getMembersAjax(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'surname',
            3 => 'nickname',
            4 => 'gender',
            5 => 'birthdate', // age
            6 => 'techo_year',
            7 => 'phone',
            8 => 'email',
            // ... hidden columns or actions
        ];

        // Base Query
        $query = Member::query()->where("surname", "!=", "")->where("name", "!=", "");

        // Total Records (before filtering)
        $totalRecords = $query->count();

        // Filtering
        if ($search = $request->input('search.value')) {
            $keywords = explode(' ', $search);
            $countKey = count($keywords);

            if ($countKey == 1) {
                $query->where(function ($mainQuery) use ($keywords) {
                    foreach ($keywords as $word) {
                        if (!empty($word)) {
                            $mainQuery->orWhere(function ($q) use ($word) {
                                $q->where('name', 'LIKE', "%{$word}%")
                                    ->orWhere('surname', 'LIKE', "%{$word}%")
                                    ->orWhere('nickname', 'LIKE', "%{$word}%")
                                    ->orWhere('id', 'LIKE', "%{$word}%")
                                    ->orWhere('email', 'LIKE', "%{$word}%")
                                    ->orWhereRaw("REPLACE(phone, ' ', '') LIKE ?", ["%{$word}%"]);
                            });
                        }
                    }
                });
            } else if ($countKey > 1) {
                $name = $keywords[0];
                $surname = $keywords[1];
                $query->where('name', 'like', "%$name%")->where('surname', 'like', "%$surname%");
            }
        }

        $filteredRecords = $query->count();

        // Ordering
        if ($request->has('order')) {
            $orderColumnIndex = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir');
            $orderColumn = $columns[$orderColumnIndex] ?? 'id';

            // Handle special sort for age (birthdate)
            if ($orderColumn == 'birthdate') {
                $query->orderBy('birthdate', $orderDirection == 'asc' ? 'desc' : 'asc');
            } else {
                $query->orderBy($orderColumn, $orderDirection);
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $query->skip($start)->take($length);

        $members = $query->get();

        // Transform Data
        $data = [];
        foreach ($members as $member) {
            $editUrl = route('member.edit', $member->id);
            $registerUrl = route('courses.index', $member->id);
            $historyUrl = route('courses.history', $member->id);

            // Age Calculation
            $age = $member->birthdate ? $member->birthdate->age : 'ไม่ทราบ';

            $data[] = [
                $member->id,
                $member->name,
                $member->surname,
                $member->nickname,
                $member->gender,
                $age,
                $member->techo_year,
                $member->phone,
                $member->email,
                // Hidden phone column
                str_replace(' ', '', $member->phone),
                // Actions
                '<div style="text-align: center"><a target="_blank" href="' . $editUrl . '" class="btn btn-secondary">' . __('messages.edit') . '</a></div>',
                '<div style="text-align: center"><a target="_blank" href="' . $registerUrl . '" class="btn btn-secondary">' . __('messages.register') . '</a></div>',
                '<div style="text-align: center"><a target="_blank" href="' . $historyUrl . '" class="btn btn-secondary">' . __('messages.history') . '</a></div>',
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
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
            'current_level' => 'required|integer|min:0|max:4',
            'level_1_date' => 'nullable|date',
            'level_2_date' => 'nullable|date',
            'level_3_date' => 'nullable|date',
            'level_4_date' => 'nullable|date',
            'leave_description' => 'nullable|string',
        ]);

        $member = Member::findOrFail($id);

        $updateData = [
            'current_level' => (string)$request->current_level,
            'level_1_date' => $request->level_1_date,
            'level_2_date' => $request->level_2_date,
            'level_3_date' => $request->level_3_date,
            'level_4_date' => $request->level_4_date,
            // 'leave_description' => $request->leave_description, // Optional: Keep or remove as per user? User said "Remove... I will put elsewhere" for death/leave date. But maybe description too? 
            // The user said "วันที่เสียชีวิต, วันที่ออกจากสายธรรม หมายเหตุ เอาออก (Death date, Leave date, Remark remove)".
            // So I will NOT update them here.
        ];

        // I will actually remove leave_description from the update array if the user explicitly said "Note remove".
        // Let's re-read: "วันที่เสียชีวิต, วันที่ออกจากสายธรรม หมายเหตุ เอาออก"
        // Yes, remove 'leave_description' from update.

        $member->update([
            'current_level' => (string)$request->current_level,
            'level_1_date' => $request->level_1_date,
            'level_2_date' => $request->level_2_date,
            'level_3_date' => $request->level_3_date,
            'level_4_date' => $request->level_4_date,
        ]);

        return redirect()->route('admin.members.senior')->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }
}
