<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request){
        $member = Member::first();

        return $member;
    }


    public function create()
    {
        return view('members.create'); // Assumes you have a view at resources/views/members/create.blade.php
    }

    /**
     * Store a newly created member in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'surname' => 'required|max:255',
            // Add other fields as necessary
        ]);

        $validatedData['email'] = Auth::user()->email; // Link member to user by email

        Member::create($validatedData);

        return redirect()->route('profile')->with('success', 'Member created successfully.');
    }

    /**
     * Show the form for editing the specified member.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $member = Member::findOrFail($id); // Ensure the member exists

        return view('members.edit', compact('member')); // Assumes you have a view at resources/views/members/edit.blade.php
    }

    /**
     * Update the specified member in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'surname' => 'required|max:255',
            // Add other fields as necessary
        ]);

        $member = Member::findOrFail($id);
        $member->update($validatedData);

        return redirect()->route('profile')->with('success', 'Member updated successfully.');
    }
}
