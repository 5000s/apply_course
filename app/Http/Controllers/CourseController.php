<?php

namespace App\Http\Controllers;

use App\Models\Apply;
use App\Models\Course;
use App\Models\Location;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class CourseController extends Controller
{
    public function courseList(Request $request)
    {

        //TODO: LET HONG FINISH
        // $courses = Course::whereYear("date_start", ">=", "2024")->orderBy("id", "desc")->get();
        // $data = [];
        // $data['courses'] = $courses;
        $data = [];
        $locationChoose = "";

        if ($request->isMethod('post')) {
            // Logic specific to POST requests
            $locationChoose = $request->input('location');
            error_log($locationChoose);
        }

        //initial
        $location_id = 1; //1,3
        $category_id = 6;
        $year = '2020';

        $courses = DB::table('courses as c')
            ->select(
                'c.location',
                'c.id',
                'c.category',
                'c.date_start',
                'c.date_end',
                DB::raw('COUNT(*) AS apply_count'),
                DB::raw('COUNT(*) as confirm_count')
            )
            ->join('applies as a', 'c.id', '=', 'a.course_id')
            ->where('c.location_id', $location_id)
            ->where('c.category_id', $category_id)
            ->where(function ($query) use ($year) {
                $query->whereYear('c.date_start', $year)
                    ->orWhereYear('c.date_end', $year);
            })
            ->groupBy('c.location', 'c.id', 'c.category', 'c.date_start', 'c.date_end')
            ->get();

        foreach ($courses as $course) {
            $course->pass_count = $this->countPassedApplies($course->id);
        }

        $data['courses'] = $courses;

        return view('admin.course_list', $data);
    }

    private function countPassedApplies($course_id)
    {
        return DB::table('applies as a')
            ->join('courses as c', 'c.id', '=', 'a.course_id')
            ->where('c.id', $course_id)
            ->where('a.state', 'ผ่านการอบรม')
            ->count();
    }

    public function courseApplyList()
    {
        //TODO: LET HONG FINISH

        // $apply = DB::table('Apply as a') // Use 'databaseName as a' to alias the table name
        //     ->where('a.course_id', '=', '501') // Apply the condition where b equals '2023'
        //     ->orderBy("created_at", "asc")
        //     ->with('member')
        //     ->get();

        $course_id = '149'; // Example value, replace with the actual course_id you want to query


        $members = DB::table('members as m')
            ->select(
                DB::raw("DATE_FORMAT(a.created_at, '%Y-%m-%d %H:%i:%s') as apply_date"),
                'm.name',
                'm.surname',
                'm.phone',
                'm.email',
                'm.age',
                'm.gender',
                'm.buddhism',
                'a.state',
                'm.updated_by'
            )
            ->join('applies as a', 'a.member_id', '=', 'm.id')
            ->join('courses as c', 'c.id', '=', 'a.course_id')
            ->where('a.course_id', $course_id)
            ->orderByRaw("DATE_FORMAT(a.created_at, '%Y-%m-%d %H:%i:%s')")
            ->get();

        $data = [];
        $data['members'] = $members;

        return view('admin.courser_apply', $data);
    }

    public function adminSearchMember(Request $request)
    {
        //        if (!Auth::check()){
//            return redirect("admin/login");
//        }

        $members = Member::select("id as i", "name as n", "surname as s", "phone_slug as p")->get();
        return view('admin.courser_admin_member', ['members' => $members]);
    }

    public function adminMemberCourse(Request $request)
    {

        //        if (!Auth::check()){
//            return redirect("admin/login");
//        }

        $member = $request->member;

        $courses = Course::orderBy("id", "desc")->get();
        $data = [];
        $data['courses'] = $courses;

        return view('admin.courser_admin_member_select', $data);
    }


    public function locationCourse(Request $request, $location)
    {

        $location = Location::where("id", $location)->first();
        $now = Carbon::now();
        $year = $now->year;
        $courses = $location->course()
            ->where("date_start", ">=", $now)
            ->orderBy("date_start", "ASC")->get();
        $data = [];
        $data['location'] = $location;
        $data['courses'] = $courses;

        return view('location', $data);
    }

    public function courseRegister(Request $request, $course)
    {

        $now = Carbon::now();
        $course = Course::where("id", $course)->where("date_start", ">=", $now)->first();
        $data = [];
        $data['course'] = $course;
        return view('courser_register', $data);
    }

    public function memberSearch(Request $request)
    {

        $input = $request->input();
        $course = $input['course'];
        $year = $input['year'];
        $month = $input['month'];
        $day = $input['day'];
        $name = $input['name'];
        $lastname = $input['lastname'];

        $date = $day . '-' . $month . '-' . $year;
        $birthDate = Carbon::parse($date);


        $now = Carbon::now();
        $course = Course::where("id", $course)->where("date_start", ">=", $now)->first();
        $member = Member::where('name', $name)->where('surname', $lastname)->where('birthdate', $birthDate)->first();


        if ($request->has('cancel')) {
            if ($member && $member->create_complete == 99) {
                $member->delete();
            }

            $data = [];
            $data['course'] = $course;
            return view('courser_register', $data);
        }


        if (!$member) {
            $member = new Member();
            $member->name = $name;
            $member->surname = $lastname;
            $member->birthdate = $birthDate;
            $member->create_complete = 99;
            $member->created_by = "ผู้สมัครสร้างเองจากหน้า web";
            $member->updated_by = "ผู้สมัครสร้างเองจากหน้า web";
            $member->save();
        }

        if ($member->create_complete == 99 && !$request->has('confirm')) {
            $data = [];
            $data['course'] = $course;
            $data['member'] = $member;
            return view('courser_user_new', $data);
        }

        $applies = Apply::where("member_id", $member->id)->get();
        $old_student = false;
        if (count($applies) > 0) {
            $old_student = true;
        }

        $isApplied = false;

        foreach ($applies as $apply) {
            $apply->course = $apply->course()->first();
            $apply->member = $apply->member()->first();

            if ($course->id == $apply->course->id) {
                $isApplied = true;
            }
        }

        $data = [];
        $data['member'] = $member;
        $data['course'] = $course;
        $data['applies'] = $applies;
        $data['is_applied'] = $isApplied;
        $data['old_student'] = $old_student;

        return view('courser_confirm', $data);
    }

    //คอร์สเตโชวิปัสสนา
    //คอร์สฤาษี (๑๔ วัน)
    //คอร์สเตโชฯ (ศิษย์เก่า)
    //คอร์สวิถีอาสวะ
    //คอร์สอานาปานสติ
    //คอร์สศิษย์เก่า (๓ วัน)
    //ธรรมะแคมป์
    //คอร์สอานาปานสติ ๑ วัน

    //buddhism
    //ฆราวาส
    //แม่ชี
    //ภิกษุ
    //สามเณร

    public static function checkQualificationForCourse($member_id, $course_id)
    {

        $course = Course::where("id", $course_id)->first();
        $member = Member::where("id", $member_id)->first();

        $pass = true;
        $message = [];

        if ($member) {

            $apply_all = Apply::where("member_id", $member_id)->where("role", "ผู้เข้าอบรม")->leftJoin('courses', function ($join) {
                $join->on('applies.course_id', '=', 'courses.id');
            })->orderBy('courses.date_end', "desc")->get();


            $apply_finish = Apply::where("member_id", $member->id)->where("role", "ผู้เข้าอบรม")->where("state", "ผ่านการอบรม")->get();
            $apply_register = Apply::where("member_id", $member->id)->where("role", "ผู้เข้าอบรม")->where("state", "ยื่นใบสมัคร")->get();


            $anapa_course = Course::where("category", "คอร์สอานาปานสติ")->get()->pluck('id')->toArray();
            $techo_course = Course::where("category", "คอร์สเตโชวิปัสสนา")->get()->pluck('id')->toArray();
            $pass_anapa = false;
            $pass_anapa_count = 0;
            $pass_techo = false;
            $pass_techo_count = 0;

            //// ผ่านอานา 3 วันแล้ว
            if (count($apply_finish) > 0) {
                foreach ($apply_finish as $apply) {
                    $course_id = $apply->course_id;
                    if (in_array($course_id, $anapa_course)) {
                        $pass_anapa = true;
                        $pass_anapa_count++;
                    }
                }
            }

            //  ผ่าน เตโช
            if (count($apply_finish) > 0) {
                foreach ($apply_finish as $apply) {
                    $course_id = $apply->course_id;
                    if (in_array($course_id, $techo_course)) {
                        $pass_techo = true;
                        $pass_techo_count++;
                    }
                }
            }



            if ($course->category == "คอร์สอานาปานสติ ๑ วัน") {
                if ($member->buddhism == "ภิกษุ" || $member->buddhism == "สามเณร") {
                    $pass = false;

                    $message[] = "คอร์สอานาปานสติ ๑ วัน นั้นไม่ได้จัดที่ไว้เพื่อ ภิกษุ และ สามเณร โปรดเลือกสมัครคอร์ส คอร์สอานาปานสติ";
                }
                if ($pass_anapa || $pass_techo) {
                    $pass = false;

                    $message[] = "คอร์สอานาปานสติ ๑ วัน นั้นไม่ได้จัดที่ไว้เพื่อ ศิษย์เก่าทั้ง อานาปานสติ และ เตโชวิปัสสนา โปรดเลือกสมัครคอร์ส อานาปานสติ หรือ เตโชวิปัสสนา";
                }

            } else if ($course->category == "คอร์สอานาปานสติ") {

                if ($pass_anapa_count >= 2) {
                    $pass = false; // ให้สมัครเตโชแทน

                    $message[] = "ท่านเข้าคอร์สอานาปานสติครบ 2 ครั้งแล้ว โปรดเลือกสมัครคอร์ส เตโชวิปัสสนา";
                }

            } else if ($course->category == "คอร์สศิษย์เก่า (๓ วัน)") {

                if (!$pass_anapa && !$pass_techo) {
                    $pass = false;
                    $message[] = "คอร์สศิษย์เก่า (๓ วัน) เป็นคอร์สสำหรับศิษย์ที่ผ่าน เตโชวิปัสสนา หรือ คอร์สอานาปานสติ มาแล้วเท่านั้น";
                }

            } else if ($course->category == "ธรรมะแคมป์") {

            } else if ($course->category == "คอร์สเตโชวิปัสสนา") {


                //จํานวนคอร์สศิษย์เตโชท่ีสมัครล่วงหน้า  ได้ 1 คอร์ส



                if (!$pass_anapa && !$pass_techo) {
                    $pass = false;
                    $message[] = "$course->category  เป็นคอร์สสำหรับศิษย์ที่ผ่าน อานาปานสติ หรือ เตโชวิปัสสนา มาแล้วเท่านั้น";
                }

                if ($member->age < 17 || $member->age > 70) {
                    $pass = false;
                    $message[] = "อายุของท่านยังไม่ถึงเกณฑ์ที่กำหนดในการเข้าคอร์สเตโชวิปัสสนา ให้ต่อสอบถามเพิ่มเติมจากเจ้าหน้าที่";
                }

                // Course ที่ลง ห่างไม่เกิน 4
                foreach ($apply_all as $apply) {
                    if ($apply->category == "คอร์สเตโชวิปัสสนา") {
                        if ($apply->course_id != $course->id) {


                            $index_course = array_search($course->id, $techo_course);
                            $index_apply_course = array_search($apply->course_id, $techo_course);
                            $dif = abs($index_course - $index_apply_course);

                            if ($dif < 4) {

                                $pass = false;
                                $message[] = "ท่านสมัครคอร์สเตโชวิปัสสนาห่างจากรอบที่เคยสมัครแล้วไม่เกิน 4 คอร์ส โปรดเลือกคอร์สอื่นที่ห่างจากที่เคยสมัครไว้ 4 คอร์ส";
                            }
                        }
                    }
                }


                // Check Course > 2
                $now = Carbon::now();
                $year = $now->year;
                $course_year = 0;
                foreach ($apply_all as $apply) {

                    $date_end = Carbon::parse($apply->date_end);

                    if ($date_end->year == $year) {
                        if ($apply->category == "คอร์สเตโชวิปัสสนา") {
                            $course_year++;
                        }
                    }
                }

                if ($course_year >= 2) {
                    $pass = false;  // สมัครเกินจำนวนครั้งต่อปี ให้สมัคร คอร์สศิษย์เก่า (๓ วัน)
                    $bh_year = $year + 543;
                    $message[] = "ท่านสมัครคอร์สเตโชวิปัสสนาในปีพ.ศ. $bh_year เกิน 2 ครั้ง โปรดสมัครคอร์สศิษย์เก่า (๓ วัน) ";
                }


            } else if ("คอร์สเตโชฯ (ศิษย์เก่า)") {

                if (!$pass_techo) {
                    $pass = false;
                    $message[] = "$course->category  เป็นคอร์สสำหรับศิษย์ที่ผ่าน เตโชวิปัสสนา มาแล้วเท่านั้น";
                }
            } else if ($course->category == "คอร์สฤาษี (๑๔ วัน)" || $course->category == "คอร์สวิถีอาสวะ") {

            }
        }

        $data = [];
        $data['pass'] = $pass;
        $data['message'] = $message;

        return $data;
    }





    public function courseApply(Request $request)
    {

        $input = $request->all();

        $member_id = $input['member_id'];
        $course_id = $input['course_id'];

        $check = self::checkQualificationForCourse($member_id, $course_id);
        $pass = $check['pass'];
        $pass_message = $check['message'];

        $member = Member::where('id', $member_id)->first();
        $course = Course::where("id", $course_id)->first();
        $applies = Apply::where("member_id", $member->id)->get();

        $my_apply = Apply::where("member_id", $member->id)->where("course_id", $course_id)->first();

        if (!$my_apply) {
            $my_apply = new Apply();
            $my_apply->member_id = $member_id;
            $my_apply->course_id = $course_id;
            $my_apply->role = "ผู้เข้าอบรม";
            $my_apply->shelter = "ทั้วไป";
            $my_apply->confirmed = "no";
            $my_apply->van = "no";

            $my_apply->firsttime = "yes";
            if (count($applies) > 0) {
                $my_apply->firsttime = "no";
            }
        }

        foreach ($applies as $apply) {
            $apply->course = $apply->course()->first();
            $apply->member = $apply->member()->first();
        }
        //        if(!$apply){
//            $apply = new Apply();
//            $my_apply->member_id = $member_id;
//            $my_apply->course_id = $course_id;
//            $my_apply->role = "ผู้เข้าอบรม";
//            $my_apply->shelter = "ทั้วไป";
//            $my_apply->confirmed = "no";
//            $my_apply->van = "no";
//        }

        $member->update($input);

        $data = [];
        $data['member'] = $member;
        $data['course'] = $course;
        //        $data['apply'] = $apply;
        $data['my_apply'] = $my_apply;
        $data['pass'] = $pass;
        $data['message'] = $pass_message;
        $data['applies'] = $applies;

        if ($pass == false) {
            return view('courser_reject', $data);
        }

        return view('courser_apply', $data);
    }

    public function courseConfirm(Request $request)
    {

        $input = $request->all();
        $member_id = $input['member_id'];
        $course_id = $input['course_id'];


        $path = self::saveApplication($request, $member_id);

        $member = Member::where('id', $member_id)->first();
        $course = Course::where("id", $course_id)->first();
        $my_apply = Apply::where("member_id", $member->id)->where("course_id", $course_id)->first();
        $applies = Apply::where("member_id", $member->id)->get();

        if (!$my_apply) {
            $my_apply = new Apply();
            $my_apply->member_id = $member_id;
            $my_apply->course_id = $course_id;
            $my_apply->confirmed = "no";
            $my_apply->van = "no";

            $my_apply->firsttime = "yes";
            if (count($applies) > 0) {
                $my_apply->firsttime = "no";
            }
        }


        $my_apply->role = $input['role'];
        $my_apply->remark = $input['remark'];
        $my_apply->application = $path;
        try {
            $my_apply->shelter = $input['shelter'];
        } catch (\Exception $exception) {

        }

        $my_apply->save();


        $applies = Apply::where("member_id", $member->id)->get();


        $data = [];
        $data['member'] = $member;
        $data['course'] = $course;
        $data['apply'] = $applies;
        $data['my_apply'] = $my_apply;

        return view('courser_apply_finish', $data);
    }

    public static function saveApplication(Request $request, $member_id)
    {
        if ($request->hasFile('application')) {
            $application = $request->file('application');
            $time = time();
            $fileName = $application->getClientOriginalName();
            $path = "userform/$time/$member_id/";
            $application->storeAs($path, $fileName);
            return $path . $fileName;
        }
        return "";
    }

    public function courseApplyFinish(Request $request)
    {
        $input = $request->all();
        $member_id = $input['member_id'];
        $course_id = $input['course_id'];
        $apply_id = $input['apply_id'];

        $member = Member::where('id', $member_id)->first();
        $course = Course::where("id", $course_id)->first();
        $my_apply = Apply::where("id", $apply_id)->where("member_id", $member->id)->where("course_id", $course_id)->first();

        $my_apply->is_user_confirm = 1;
        $my_apply->save();

        session()->put('member_id', $member->id);

        return redirect("/profile/course");
    }

    public function userCourse(Request $request)
    {

        if ($request->session()->has('member_id')) {

            $member_id = session('member_id');

            $member = Member::where('id', $member_id)->first();
            $applies = Apply::where('member_id', $member_id)->get();

            $data = [];
            $data['member'] = $member;
            $data['applies'] = $applies;

            return view('courser_user', $data);
        }

        return redirect("/");
    }

    public function getApplication(Request $request, $time, $member_id, $filename)
    {

        return response()->download(storage_path("app/userform/$time/$member_id/$filename"));

    }
}
