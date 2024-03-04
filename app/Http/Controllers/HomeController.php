<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }


    public function profile()
    {
        $user = Auth::user();

        // Retrieve all members related to this user by email
        $members = Member::where('email', $user->email )->select('id', 'name', 'surname', 'email')->get();

        // Pass the member data to the view
        return view('profile', ['members' => $members]);
    }
}
