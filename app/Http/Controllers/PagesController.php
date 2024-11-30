<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PagesController extends Controller
{
    public function index(Request $request)
    {
        if(Auth::check()){
            return redirect()->route('profile');
        }else{
            return redirect()->route('login');
        }

        return view('home');
    }

    public function about()
    {
        return view('about');
    }


}
