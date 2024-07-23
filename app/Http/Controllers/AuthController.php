<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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

    public function preregister(Request $request)
    {
        return view('members.preregister');
    }

    public function formPreregister(Request $request)
    {
        return view('members.request-access');
    }

    public function checkEmail(Request $request) {
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $birthYear = $request->birth_year;
        $gender = $request->gender;

        $members = Member::whereYear("birthdate", $birthYear)
            ->where("gender", $gender)
            ->get();

        $threshold = 80; // Similarity threshold in percentage
        $matched_members = [];

        foreach ($members as $member) {
            similar_text($first_name, $member->name, $percent_firstname);
            similar_text($last_name, $member->surname, $percent_lastname);

            $average_similarity = ($percent_firstname + $percent_lastname) / 2;

            if ($average_similarity >= $threshold) {
                $masked_email = $this->maskEmail($member->email);
                $matched_members[] = [
                    'id' => $member->id,
                    'email' => $masked_email,
                    'similarity' => $average_similarity
                ];
            }
        }

        // Sort matched members by similarity in descending order
        usort($matched_members, function($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        // Get the most matched member
        $most_matched_member = $matched_members[0] ?? null;

        if ($most_matched_member) {
            return view('members.display-emails', ['most_matched_member' => $most_matched_member]);
        } else {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลที่ตรงกับการค้นหาของคุณ กรุณาตรวจสอบให้ถูกต้อง');
        }
    }

    private function maskEmail($email) {
        $email_parts = explode("@", $email);
        $local = substr($email_parts[0], 0, 2) . str_repeat("*", strlen($email_parts[0]) - 2);
        return $local . "@" . $email_parts[1];
    }


    public function requestPasswordReset(Request $request) {
        $member = Member::find($request->member_id);

        if ($member) {

            $user = User::where('email', $member->email)->first();

            if (!$user) {
                // สร้างผู้ใช้ใหม่ถ้าไม่พบผู้ใช้ที่มีอยู่
                $user = User::create([
                    'name' => $member->name,
                    'surname' => $member->surname,
                    'email' => $member->email,
                    'role' => 'student',
                    'password' => Hash::make(Str::random(8)), // กำหนดรหัสผ่านเริ่มต้นแบบสุ่ม
                ]);
            }
            // ส่งอีเมลเพื่อสร้างรหัสผ่านใหม่
            $status = Password::sendResetLink(['email' => $user->email]);

            if ($status === Password::RESET_LINK_SENT) {
                $maskedEmail = $this->maskEmail($user->email);
                return view('members.password-reset-sent', ['maskedEmail' => $maskedEmail]);
            } else {
                return back()->withErrors(['email' => __($status)]);
            }
        }

        return back()->with('error', 'ไม่พบข้อมูลสมาชิก');
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
