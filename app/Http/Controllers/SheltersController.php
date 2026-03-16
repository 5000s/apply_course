<?php

namespace App\Http\Controllers;

use App\Models\Shelter;
use App\Models\Member;
use Illuminate\Http\Request;

class SheltersController extends Controller
{
    public function index()
    {
        // View to load the datatable similar to admin.senior_member_list

        $this->autoSetMemberID();

        $shelters = Shelter::with('member')->get();



        return view('admin.shelters.index', [
            'shelters' => $shelters,
            'title' => 'ตาราง Shelters',
        ]);
    }

    public function autoSetMemberID()
    {
        $shelters = Shelter::whereNull("member_id")->get();

        $updatedCount = 0;

        foreach ($shelters as $shelter) {
            // Only search if name and surname are provided
            if (!empty($shelter->name) && !empty($shelter->surname)) {
                $member = Member::where('name', $shelter->name)
                    ->where('surname', $shelter->surname)
                    ->where(function ($q) {
                        $q->whereNull('is_delete')->orWhere('is_delete', 0);
                    })
                    ->first();

                if ($member) {
                    $shelter->update(['member_id' => $member->id]);
                    $updatedCount++;
                }
            }
        }
    }

    public function create()
    {
        return view('admin.shelters.form', ['title' => 'เพิ่ม Shelter']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'nullable|string',
            'index' => 'nullable|integer',
            'name' => 'nullable|string',
            'surname' => 'nullable|string',
            'note' => 'nullable|string',
            'note_master' => 'nullable|string',
            'extra_user' => 'nullable|string',
            'member_id' => 'nullable|integer|exists:members,id',
        ]);

        Shelter::create($request->all());

        return redirect()->route('shelters.index')->with('success', 'เพิ่มข้อมูลสำเร็จ');
    }

    public function edit($id)
    {
        $shelter = Shelter::findOrFail($id);
        return view('admin.shelters.form', [
            'shelter' => $shelter,
            'title' => 'แก้ไข Shelter',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'number' => 'nullable|string',
            'index' => 'nullable|integer',
            'name' => 'nullable|string',
            'surname' => 'nullable|string',
            'note' => 'nullable|string',
            'note_master' => 'nullable|string',
            'extra_user' => 'nullable|string',
            'member_id' => 'nullable|integer|exists:members,id',
        ]);

        $shelter = Shelter::findOrFail($id);
        $shelter->update($request->all());

        return redirect()->route('shelters.index')->with('success', 'แก้ไขข้อมูลสำเร็จ');
    }

    public function destroy($id)
    {
        $shelter = Shelter::findOrFail($id);
        $shelter->delete();

        return redirect()->route('shelters.index')->with('success', 'ลบข้อมูลสำเร็จ');
    }

    // Ajax route for DataTables format if needed just like AdminMemberController@getMembersAjax
}
