<?php

namespace App\Http\Controllers;

use App\Member;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index(Request $request){
        return view('login');
    }

    public function login(Request $request){

        $name = $request->name;
        $lastname = $request->lastname;
        $phone = $request->phone;
        $phone = self::getPhoneSlug($phone);

        $member = Member::where("name",$name)->where("surname",$lastname)->where("phone_slug",$phone)->first();

        if ($member){
            dd($member);
        }else{
            dd($name,$lastname,$phone);
        }

        return view('login');
    }

    public function updatePhone(){

        $members = Member::get();

        foreach ($members as $member){
            $phone = $member->phone;
            $member->phone_slug = self::getPhoneSlug($phone);
            $member->save();
        }
    }

    public static function getPhoneSlug($phone){

        $phone = str_replace('|',",",$phone);
        $phoneList = explode("," , $phone);
        $phone = $phoneList[0];

        $divider = '';
        $text = '';

        try {


            // replace non letter or digits by divider
            $text = preg_replace('~[^\pL\d]+~u', $divider, $phone);


            // remove unwanted characters
            $text = preg_replace('~[^-\w]+~', '', $text);

            // trim
            $text = trim($text, $divider);

            // remove duplicate divider
            $text = preg_replace('~-+~', $divider, $text);

            // lowercase
            $text = strtolower($text);


        }catch (\Exception $exception){

        }

        if (strlen($text) > 30){
            dd($text);
        }

        if (empty($text)) {
            return '-';
        }

        return $text;
    }
}
