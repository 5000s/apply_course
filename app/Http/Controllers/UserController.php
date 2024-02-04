<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(Request $request){

        $username = $request->username;
        $password = $request->password;

        $data = [];
        $data['message'] = "กรุณากรอก Username, Password เพื่อ Login";

        if (!$username == ""){
            $user = User::where("username",$username)->where("password",$password)->first();

            if ($user){
                Auth::loginUsingId($user->id);

                return redirect('/admin/member');
            }else{
                $data['message'] = "Username, Password ไม่พบในฐานข้อมูล โปรดติดต่อทีมงาน";
            }
        }

        return view("admin.login", $data);
    }

    public function logout(Request $request){

        Auth::logout();

        return redirect('/');

    }
}
