<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMemberController extends Controller
{
    public function profile()
    {


        // Retrieve all members related to this user by email
        $members = Member::where("surname", "!=", "")->get();

        // Pass the member data to the view
        return view('admin.profile_list', ['members' => $members]);
    }
}
