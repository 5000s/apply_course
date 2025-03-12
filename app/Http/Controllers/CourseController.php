<?php

namespace App\Http\Controllers;

use App\Exports\CourseApplyExport;
use App\Models\Apply;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Location;
use App\Models\Member;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PA\ProvinceTh\Factory;
use Madnest\Madzipper\Facades\Madzipper;
use Illuminate\Support\Facades\Storage;



class CourseController extends Controller
{

    public function courseCreate(Request $request)
    {
        $locations = Location::all();
        $categories = CourseCategory::all();
        return view('admin.course_create', compact('locations', 'categories'));
    }

    public function courseEdit(Request $request,$course_id)
    {

        $course = Course::where("id",$course_id)->first();
        $locations = Location::all();
        $categories = CourseCategory::all();

        return view('admin.course_create', compact('course', 'locations', 'categories'));
    }


    /**
     * Store a newly created course in storage.
     */
    public function courseSave(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'date_start'  => 'required|date',
            'date_end'    => 'required|date',
            'location_id' => 'required|integer|exists:locations,id',
            'category_id' => 'required|integer|exists:course_categories,id',
            'state'       => 'required|string',
        ]);

        // Create a new course instance
        $course = new Course();
        $course->date_start   = $request->input('date_start');
        $course->date_end     = $request->input('date_end');
        $course->location_id  = $request->input('location_id');
        $course->category_id  = $request->input('category_id');
        $course->state        = $request->input('state');
        // Add any other relevant fields here if needed

        $course->save();

        return redirect()->back()->with('success', 'Course saved successfully!');
    }

    /**
     * Update the specified course in storage.
     */
    public function courseUpdate(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'date_start'  => 'required|date',
            'date_end'    => 'required|date',
            'location_id' => 'required|integer|exists:locations,id',
            'category_id' => 'required|integer|exists:course_categories,id',
            'state'       => 'required|string',
        ]);

        // Fetch the existing course, or throw a 404 if not found
        $course = Course::findOrFail($id);

        // Update course fields
        $course->date_start   = $request->input('date_start');
        $course->date_end     = $request->input('date_end');
        $course->location_id  = $request->input('location_id');
        $course->category_id  = $request->input('category_id');
        $course->state        = $request->input('state');
        // Update any other relevant fields here if needed

        $course->save();

        return redirect()->back()->with('success', 'Course updated successfully!');
    }



        public function courseList(Request $request)
    {
        $data = [];
        $locationChoose = "";

        // Initial
        $location_id = $request->input('location', 1);
        $category_id = $request->input('category', 6);
        $now_year = Carbon::now()->year;
        $year = $request->input('year', $now_year);

        // Query builder with Eloquent
        $courses = DB::table('courses as c')
            ->join('course_categories as cc', 'c.category_id', '=', 'cc.id') // Join course categories

            ->select(
                'c.location',
                'c.id',
                'c.state',
                'cc.show_name as name', // Course category name
                'c.category',
                'c.date_start',
                'c.date_end',
                DB::raw('COUNT(DISTINCT CASE WHEN a.cancel IS NULL OR a.cancel = 0 THEN a.member_id END) as apply_all_count'),
                DB::raw('SUM(CASE WHEN a.cancel = 0 THEN 1 ELSE 0 END) as apply_count'),
                DB::raw('SUM(CASE WHEN a.confirmed = "yes" THEN 1 ELSE 0 END) as confirm_count'),
                DB::raw('SUM(CASE WHEN a.state = "ผ่านการอบรม" THEN 1 ELSE 0 END) as pass_count')
            )
            ->leftJoin('applies as a', 'c.id', '=', 'a.course_id');

        // Apply filters
        if ($location_id != "0") {
            $courses = $courses->where('c.location_id', $location_id);
        }

        if ($category_id != "0") {
            $courses = $courses->where('c.category_id', $category_id);
        }

        // Year filter
        $courses = $courses->where(function ($query) use ($year) {
            $query->whereYear('c.date_start', $year)
                ->orWhereYear('c.date_end', $year);
        });

        // Group by and get results
        $courses = $courses->groupBy( 'c.state', 'c.location', 'c.id', 'c.category', 'c.date_start', 'c.date_end', 'cc.show_name')
            ->get()->map(function ($course) {
                // Parse the start and end dates
                $startDate = Carbon::parse($course->date_start)->locale('th');
                $endDate = Carbon::parse($course->date_end)->locale('th');

                // Add 543 years to convert Gregorian year to Thai Buddhist year
                $thaiYear = $endDate->year + 543;

                // Format the date range with Thai Buddhist year
                if ($startDate->isSameDay($endDate)) {
                    $course->date_range = "{$startDate->translatedFormat('j F')} $thaiYear";
                } else {
                    $course->date_range = "{$startDate->translatedFormat('j')}–{$endDate->translatedFormat('j F')} $thaiYear";
                }

                $course->month_year = "{$endDate->translatedFormat('F')} $thaiYear";

                return $course;
            });

        // Pass data to view
        $data['courses'] = $courses;

        return view('admin.course_list', $data);
    }


    public function courseApplyList(Request $request, $course_id)
    {

        $members = DB::table('members as m')
            ->select(
                DB::raw("DATE_FORMAT(a.created_at, '%Y-%m-%d %H:%i:%s') as apply_date"),
                'a.id as apply_id',
                'c.id as course_id',
                'm.name',
                'm.surname',
                'm.phone',
                'm.email',
                'm.age',
                'm.gender',
                'm.buddhism',
                'a.state',
                'a.updated_by'
            )
            ->join('applies as a', 'a.member_id', '=', 'm.id')
            ->join('courses as c', 'c.id', '=', 'a.course_id')
            ->where('a.course_id', $course_id)
            ->where(function ($query) {
                $query->where('a.cancel', 0)
                    ->orWhereNull('a.cancel');
            })
            ->orderByRaw("DATE_FORMAT(a.created_at, '%Y-%m-%d %H:%i:%s')")
            ->get();

        $data = [];
        $data['members'] = $members;

        $course = Course::where("id", $course_id)->first();

        $data['course'] = $course;

        return view('admin.courser_apply', $data);
    }

    public function courseApplyListDownload(Request $request, $course_id)
    {
        return Excel::download(new CourseApplyExport($course_id), 'course_apply_list.xlsx');
    }

    public function courseApplyListPdfDownload(Request $request, $course_id)
    {
        $applications = DB::table('members as m')
            ->select(
                DB::raw("DATE_FORMAT(a.created_at, '%Y-%m-%d %H:%i:%s') as apply_date"),
                'a.id as apply_id',
                'c.coursename as coursename',
                'm.name',
                'm.surname',
                'm.phone',
                'm.email',
                'm.age',
                'm.gender',
                'm.buddhism',
                'a.state',
                'a.updated_by',
                'a.application'
            )
            ->join('applies as a', 'a.member_id', '=', 'm.id')
            ->join('courses as c', 'c.id', '=', 'a.course_id')
            ->where('a.course_id', $course_id)
            ->where('a.cancel', 0)
            ->orderByRaw("DATE_FORMAT(a.created_at, '%Y-%m-%d %H:%i:%s')")
            ->get();

        $pdf = PDF::loadView('pdf.course_apply_list', compact('applications'));

        return $pdf->download('course_apply_list.pdf');
    }

    public function courseApplyListZipDownload(Request $request, $course_id)
    {
        $applications = DB::table('members as m')
            ->select(
                'm.name as first_name',
                'm.surname as last_name',
                'a.id as apply_id',
                'a.application',
                'c.id as course_id'
            )
            ->join('applies as a', 'a.member_id', '=', 'm.id')
            ->join('courses as c', 'c.id', '=', 'a.course_id')
            ->where('a.course_id', $course_id)
            ->where('a.cancel', 0)
            ->get();

        // Create a temporary file to store the zip
        $zipFileName = 'course_applications_' . $course_id . '.zip';
        $zipFilePath = storage_path('app/' . $zipFileName);

        // Create a new zip file
        $zipper = Madzipper::make($zipFilePath);

        foreach ($applications as $application) {
            // Get the path to the application file
            $filePath = storage_path('app/public/' . $application->application);

            if (file_exists($filePath)) {
                // Create a new file name
                $newFileName = $application->first_name . '_' . $application->last_name . '_course_' . $application->course_id . '.' . pathinfo($filePath, PATHINFO_EXTENSION);

                // Add file to the zip with the new file name
                $zipper->addString($newFileName, file_get_contents($filePath));
            }
        }

        // Close the zip file
        $zipper->close();

        // Return the zip file as a download
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    public function viewForm(Request $request, $course_id, $apply_id)
    {

        $apply = Apply::where("id", $apply_id)->first();
        $member = Member::where("id", $apply->member_id)->first();
        $course = Course::where("id", $apply->course_id)->first();

        $data = [];
        $data['apply'] = $apply;
        $data['member'] = $member;
        $data['course'] = $course;

        $provinces = Factory::province();
        $provinceArray = $provinces->toArray();
        usort($provinceArray, array($this, 'compareByNameTh'));

        $data["nations"] = MemberController::$nationals;
        $data["provinces"] = $provinceArray;

        return view('admin.courser_apply_member', $data);
    }

    public function updateApplyStatus($course_id, $apply_id, $status)
    {
       $admin = Auth::user();

        $apply = Apply::where("id", $apply_id)->first();
        $apply->state = $status;
        $apply->updated_by = $admin->name;
        $apply->save();

        return redirect()->route('admin.courseList', ['course_id' => $course_id]);
    }

    public function compareByNameTh($a, $b) {
        return strcmp($a['name_th'], $b['name_th']);
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
