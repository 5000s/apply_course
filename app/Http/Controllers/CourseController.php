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
use Google\Service\Drive\Label;
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

    public function courseEdit(Request $request, $course_id)
    {

        $course = Course::where("id", $course_id)->first();
        $locations = Location::all();
        $categories = CourseCategory::all();

        return view('admin.course_create', compact('course', 'locations', 'categories'));
    }


    /**
     * Store a newly created course in storage.
     */
    public function courseSave(Request $request)
    {
        $user = Auth::user();
        if ($user->admin != 1 || $user->editor != 1) {
            return back()->withErrors(['error' => 'You have no permission to create/edit']);
        }

        // Validate the incoming request
        $request->validate([
            'date_start'  => 'required|date',
            'date_end'    => 'required|date',
            'location_id' => 'required|integer|exists:locations,id',
            'category_id' => 'required|integer|exists:course_categories,id',
            'state'       => 'required|string',
            'description'  => 'string',
            'listed'       => 'required|string',
            'listed_date'  => 'required|date'
        ]);


        // Create a new course instance
        $course = new Course();
        $course->date_start   = $request->input('date_start');
        $course->date_end     = $request->input('date_end');
        $course->listed     = $request->input('listed');
        $course->listed_date     = $request->input('listed_date');
        $course->location_id  = $request->input('location_id');
        $course->category_id  = $request->input('category_id');
        $course->state        = $request->input('state');
        $course->description        = $request->input('description');
        $location = Location::where("id",  $course->location_id)->first()->name;
        $category = CourseCategory::where("id", $course->category_id)->first()->name;

        $course->location = $location;
        $course->category = $category;
        $course->courseyear = Carbon::parse($course->date_start)->year;

        $course->coursename =   $this->generateCourseName($course->date_start, $course->date_end);
        $course->save();

        return   redirect()->route('admin.courses.edit', ['course_id' => $course->id])->with('success', 'Course created successfully!');
    }

    function generateCourseName($start_date, $end_date)
    {
        Carbon::setLocale('th');

        $start = Carbon::parse($start_date);
        $end = Carbon::parse($end_date);

        $thai_days = [
            'Sunday' => 'อา.',
            'Monday' => 'จ.',
            'Tuesday' => 'อ.',
            'Wednesday' => 'พ.',
            'Thursday' => 'พฤ.',
            'Friday' => 'ศ.',
            'Saturday' => 'ส.'
        ];

        $start_day_abbr = $thai_days[$start->format('l')]; // Get Thai abbreviation
        $start_day = $start->format('d'); // Get day
        $start_month = $start->translatedFormat('M'); // Get month
        $start_year = $start->year + 543; // Convert to Buddhist year (พ.ศ.)

        if ($start->equalTo($end)) {
            return "{$start_day_abbr} {$start_day} {$start_month}. {$start_year}";
        }

        $end_day_abbr = $thai_days[$end->format('l')];
        $end_day = $end->format('d');
        $end_month = $end->translatedFormat('M');
        $end_year = $end->year + 543;

        if ($start->format('m') === $end->format('m') && $start->format('Y') === $end->format('Y')) {
            return "{$start_day_abbr} {$start_day} - {$end_day_abbr} {$end_day} {$start_month}. {$start_year}";
        }

        return "{$start_day_abbr} {$start_day} {$start_month}. - {$end_day_abbr} {$end_day} {$end_month}. {$end_year}";
    }

    /**
     * Update the specified course in storage.
     */
    public function courseUpdate(Request $request, $id)
    {

        $user = Auth::user();
        if ($user->admin != 1 || $user->editor != 1) {
            return back()->withErrors(['error' => 'You have no permission to create/edit']);
        }

        // Validate the incoming request
        $request->validate([
            'date_start'  => 'required|date',
            'date_end'    => 'required|date',
            'location_id' => 'required|integer|exists:locations,id',
            'category_id' => 'required|integer|exists:course_categories,id',
            'state'       => 'required|string',
            'description'  => 'string',
            'listed'       => 'required|string',
            'listed_date'  => 'required|date'
        ]);


        // Fetch the existing course, or throw a 404 if not found
        $course = Course::findOrFail($id);

        $course->date_start   = $request->input('date_start');
        $course->date_end     = $request->input('date_end');
        $course->listed     = $request->input('listed');
        $course->listed_date     = $request->input('listed_date');
        $course->location_id  = $request->input('location_id');
        $course->category_id  = $request->input('category_id');
        $course->state        = $request->input('state');
        $course->description        = $request->input('description');
        $location = Location::where("id",  $course->location_id)->first()->name;
        $category = CourseCategory::where("id", $course->category_id)->first()->name;

        $course->location = $location;
        $course->category = $category;
        $course->courseyear = Carbon::parse($course->date_start)->year;

        $course->coursename =   $this->generateCourseName($course->date_start, $course->date_end);
        $course->save();

        return   redirect()->route('admin.courses.edit', ['course_id' => $course->id])->with('success', 'Course updated successfully!');
    }



    public function courseList(Request $request)
    {

        $data = [];
        $locationChoose = "";

        // Initial
        $location_id = $request->input('location', 0);
        $category_id = $request->input('category', 0);
        $status = $request->input('status', 'เปิดรับสมัคร');

        $now_year = Carbon::now()->year;
        $year = $request->input('year', $now_year);
        $month = $request->input('month', 0);

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
                DB::raw('SUM(CASE WHEN a.state != "ยกเลิกสมัคร" AND (a.cancel IS NULL  OR a.cancel = 0 ) THEN 1 ELSE 0 END) as total_count'),
                DB::raw('SUM(CASE WHEN a.state = "ยื่นใบสมัคร" THEN 1 ELSE 0 END)  as apply_count'),
                DB::raw('SUM(CASE WHEN a.state = "ยืนยันแล้ว" THEN 1 ELSE 0 END)  as confirm_count'),
                DB::raw('SUM(CASE WHEN a.state = "ผ่านการอบรม" THEN 1 ELSE 0 END)  as pass_count'),
                DB::raw('SUM(CASE WHEN a.state = "ยุติกลางคัน" THEN 1 ELSE 0 END) as failed_count'),
                DB::raw('SUM(CASE WHEN a.state = "ยกเลิกสมัคร" or a.cancel = 0 THEN 1 ELSE 0 END) as cancel_count')
            )
            ->leftJoin('applies as a', 'c.id', '=', 'a.course_id');

        if ($status != "ทั้งหมด") {
            $courses = $courses->where('c.state', $status);
        }

        // Apply filters
        if ($location_id != "0") {
            $courses = $courses->where('c.location_id', $location_id);
        }

        if ($category_id != "0") {
            if ($category_id == 1 || $category_id == "1") {
                $courses = $courses->whereIn('c.category_id', [1, 2, 3, 4, 6, 8, 10, 12]);
            } else if ($category_id == 2  || $category_id == "2") {
                $courses = $courses->whereIn('c.category_id', [5, 7, 9, 11, 13, 14]);
            }
        }

        if ($month != 0) {

            $courses = $courses->where(function ($query) use ($month) {
                $query->whereMonth('c.date_start', $month)
                    ->orWhereMonth('c.date_end', $month);
            });
        }

        // Year filter
        $courses = $courses->where(function ($query) use ($year) {
            $query->whereYear('c.date_start', $year)
                ->orWhereYear('c.date_end', $year);
        });


        // Group by and get results
        $courses = $courses->groupBy('c.state', 'c.location', 'c.id', 'c.category', 'c.date_start', 'c.date_end', 'cc.show_name')
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
        $group = $request->input('group', "all");
        $currentCourse = Course::findOrFail($course_id);

        $base = DB::table('members as m')
            ->select(
                DB::raw("DATE_FORMAT(a.created_at, '%Y-%m-%d %H:%i:%s') as apply_date"),
                'a.id as apply_id',
                'a.shelter as shelter',
                'a.remark as remark',
                'a.van as van',
                'c.id as course_id',
                'm.id as uid',
                'm.name',
                'm.surname',
                'm.phone',
                'm.degree',
                'm.expertise',
                'm.career',
                'm.nationality',
                'm.shelter_number',
                'm.email',
                'm.age',
                'm.gender',
                'm.medical_condition',
                'm.buddhism',
                'm.status',
                'a.state',
                'a.role',
                'a.cancel',
                'a.updated_by'
            )
            ->join('applies as a', 'a.member_id', '=', 'm.id')
            ->join('courses as c', 'c.id', '=', 'a.course_id')
            ->where('a.course_id', $course_id)

            ->orderByRaw("DATE_FORMAT(a.created_at, '%Y-%m-%d %H:%i:%s')");

        // ----------------- ดึงข้อมูลตามแท็บที่เลือก -----------------
        switch ($group) {
            case 'male':
                $members = (clone $base)
                    ->whereNull('a.cancel')
                    ->where('a.shelter', 'ทั่วไป')
                    ->where('m.gender', 'ชาย')
                    ->where('m.buddhism', 'ฆราวาส')
                    ->get();
                break;

            case 'female':
                $members = (clone $base)
                    ->whereNull('a.cancel')
                    ->where('a.shelter', 'ทั่วไป')
                    ->where('m.gender', 'หญิง')
                    ->where('m.buddhism', 'ฆราวาส')
                    ->get();
                break;

            case 'malespecial':
                $members = (clone $base)
                    ->whereNull('a.cancel')
                    ->where('a.shelter', 'กุฏิพิเศษ')
                    ->where('m.gender', 'ชาย')
                    ->where('m.buddhism', 'ฆราวาส')
                    ->get();
                break;

            case 'femalespecial':
                $members = (clone $base)
                    ->whereNull('a.cancel')
                    ->where('a.shelter', 'กุฏิพิเศษ')
                    ->where('m.gender', 'หญิง')
                    ->where('m.buddhism', 'ฆราวาส')
                    ->get();
                break;


            case 'monk':
                $members = (clone $base)
                    ->whereNull('a.cancel')
                    ->where('m.buddhism', 'ภิกษุ')
                    ->get();
                break;

            case 'nun':
                $members = (clone $base)
                    ->whereNull('a.cancel')
                    ->where('m.buddhism', 'แม่ชี')
                    ->get();
                break;


            case 'cancel':
                $members = (clone $base)
                    ->where('a.cancel', 1)
                    ->get();
                break;

            default:        // all
                $members = (clone $base)->get();
                break;
        }

        // ----------------- นับจำนวนแต่ละกลุ่ม (เอาไว้โชว์บนแท็บ/สรุป) -----------------
        $stats = [
            'male'   => (clone $base)->whereNull('a.cancel')->where('a.shelter', 'ทั่วไป')->where('m.gender', 'ชาย')->where('m.buddhism', 'ฆราวาส')->count(),

            'female' => (clone $base)->whereNull('a.cancel')->where('a.shelter', 'ทั่วไป')->where('m.gender', 'หญิง')->where('m.buddhism', 'ฆราวาส')->count(),

            'malespecial'   => (clone $base)->whereNull('a.cancel')->where('a.shelter', 'กุฏิพิเศษ')->where('m.gender', 'ชาย')->where('m.buddhism', 'ฆราวาส')->count(),

            'femalespecial' => (clone $base)->whereNull('a.cancel')->where('a.shelter', 'กุฏิพิเศษ')->where('m.gender', 'หญิง')->where('m.buddhism', 'ฆราวาส')->count(),

            'monk'   => (clone $base)->whereNull('a.cancel')->where('m.buddhism', 'ภิกษุ')->count(),

            'nun'    => (clone $base)->whereNull('a.cancel')->where('m.buddhism', 'แม่ชี')->count(),

            'all'    => (clone $base)
                ->whereNull('a.cancel')->count(),

            'cancel'    => (clone $base)->where('a.cancel', 1)->count(),
        ];


        // ─── add 'gap' onto each member ──────────────────────────────────────
        $members = $members->map(function ($member) use ($currentCourse) {
            // find last completed 7-day course before this one
            $lastDate = DB::table('applies as a')
                ->join('courses as c', 'a.course_id', '=', 'c.id')
                ->join('course_categories as cc', 'c.category_id', '=', 'cc.id')
                ->where('a.member_id', $member->uid)
                ->where('a.state', 'ผ่านการอบรม')
                ->where('cc.day', ">=", 7)
                ->where('c.date_start', '<', $currentCourse->date_start)
                ->orderByDesc('c.date_start')
                ->value('c.date_start');

            if ($lastDate) {
                // calculate months between lastDate and the current course’s start date
                $months = Carbon::parse($lastDate)
                    ->diffInMonths(Carbon::parse($currentCourse->date_start));

                $member->gap = "{$months}";
            } else {
                $member->gap = null;
            }

            return $member;
        });


        $completedCoursesRaw = DB::table('applies as a')
            ->join('courses as c', 'a.course_id', '=', 'c.id')
            ->select(
                'a.member_id',
                'a.role',
                'c.category',
                'c.id as course_id',
                DB::raw("DATE_FORMAT(c.date_start, '%Y-%m-%d') as date_start")
            )
            ->where('a.state', 'ผ่านการอบรม')
            ->where('a.role', "!=", 'ธรรมะบริกร')
            ->where(function ($query) {
                $query->where('a.cancel', 0)->orWhereNull('a.cancel');
            })
            ->orderBy('a.created_at', 'desc')
            ->get()
            ->groupBy('member_id', 'course_id')
            ->map(function ($group) {
                return $group->unique('course_id')->take(3)->values()->toArray();
            })
            ->toArray();

        $completedServiceCoursesRaw = DB::table('applies as a')
            ->join('courses as c', 'a.course_id', '=', 'c.id')
            ->select(
                'a.member_id',
                'a.role',
                'c.category',
                'c.id as course_id',
                DB::raw("DATE_FORMAT(c.date_start, '%Y-%m-%d') as date_start")
            )
            ->where('a.state', 'ผ่านการอบรม')
            ->where('a.role', 'ธรรมะบริกร')
            ->where(function ($query) {
                $query->where('a.cancel', 0)->orWhereNull('a.cancel');
            })
            ->orderBy('a.created_at', 'desc')
            ->get()
            ->groupBy('member_id', 'course_id')
            ->map(function ($group) {
                return $group->unique('course_id')->take(3)->values()->toArray();
            })
            ->toArray();


        $data = [];
        $data['members'] = $members;
        $data['stats'] = $stats;
        $data['completedCourses'] = $completedCoursesRaw;
        $data['completedServiceCourses'] = $completedServiceCoursesRaw;

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
            ->where(function ($query) {
                $query->where('a.cancel', 0)
                    ->orWhereNull('a.cancel');
            })
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
            ->where(function ($query) {
                $query->where('a.cancel', 0)
                    ->orWhereNull('a.cancel');
            })
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
        $user = Auth::user();
        if ($user->admin != 1 || $user->editor != 1) {
            return back()->withErrors(['error' => 'You have no permission to create/edit']);
        }

        $admin = Auth::user();

        if ($status == 'ยกเลิกการสมัคร') {
            $apply = Apply::where("id", $apply_id)->first();
            $apply->cancel = 1;
            $apply->cancel_at = now();
            $apply->updated_by = $admin->name;
            $apply->save();
        } else {
            $apply = Apply::where("id", $apply_id)->first();
            $apply->state = $status;
            $apply->updated_by = $admin->name;
            $apply->save();
        }

        return redirect()->route('admin.courseList', ['course_id' => $course_id]);
    }

    public function compareByNameTh($a, $b)
    {
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



    public function rankingMonk() {}


    public function calculateGap(int $courseId)
    {
        // load current course
        $currentCourse = Course::findOrFail($courseId);

        // 1️⃣ all members who applied to this course
        $memberIds = Apply::where('course_id', $courseId)
            ->pluck('member_id')
            ->unique();

        $applicants = Member::whereIn('id', $memberIds)->get();

        // 2️⃣ & 3️⃣ compute Y-M-D gap for each
        $results = $applicants
            ->map(function (Member $member) use ($currentCourse) {
                $lastCourse = Course::join('applies', 'courses.id', '=', 'applies.course_id')
                    ->join('course_categories', 'courses.category_id', '=', 'course_categories.id')
                    ->where('applies.member_id', $member->id)
                    ->where('applies.state', 'ผ่านการอบรม')
                    ->where('course_categories.day', 7)
                    ->where('courses.date_start', '<', $currentCourse->date_start)
                    ->orderByDesc('courses.date_start')
                    ->select('courses.date_start')
                    ->first();

                if ($lastCourse) {
                    $diff = Carbon::now()->diff(Carbon::parse($lastCourse->date_start));
                    $years  = $diff->y;
                    $months = $diff->m;
                    $days   = $diff->d;
                } else {
                    $years = $months = $days = null;
                }

                return [
                    'member'    => $member,
                    'last_date' => optional($lastCourse)->date_start,
                    'years'     => $years,
                    'months'    => $months,
                    'days'      => $days,
                ];
            })
            ->sortByDesc(function ($row) {
                // sort by total days as a rough proxy: years*365 + months*30 + days
                if (is_null($row['years'])) return -1;
                return $row['years'] * 365 + $row['months'] * 30 + $row['days'];
            })
            ->values();

        return view('admin.gap', [
            'course'  => $currentCourse,
            'results' => $results,
        ]);
    }



    public function setCourseData()
    {
        $courses = Course::all();


        $categoryMap = [
            'คอร์สเตโชวิปัสสนา' => 1,
            'คอร์สฤาษี (๑๔ วัน)' => 2,
            'คอร์สเตโชฯ (ศิษย์เก่า)' => 3,
            'คอร์สวิถีอาสวะ' => 4,
            'คอร์สอานาปานสติ' => 5,
            'คอร์สศิษย์เก่า (๓ วัน)' => 6,
            'ธรรมะแคมป์' => 7,
            'คอร์สเตโชฯ (อาวุโส)' => 8,
            'คอร์สอานาปานสติ ๑ วัน' => 11,
            'คอร์สอินเตอร์' => null,
            'คอร์สวิปัสสานาสติปัฏฐานสี่ เผากิเลส (ไทย-อังกฤษ)' => 15,
            'คอร์สวิปัสสานาสติปัฏฐานสี่ เผากิเลส' => 1,
            'คอร์สวิปัสสานาสติปัฏฐานสี่ เผากิเลส (คอร์สทดลอง)' => 16,
            'คอร์สสมาธิอานาปานสติ 3 วัน 2 คืน' => 13,
            'คอร์สสมาธิอานาปานสติ 1 วัน' => 11,
            'คอร์สสมาธิอานาปานสติ 4 วัน 3 คืน' => 14,
            'คอร์สวิปัสสานาสติปัฏฐานสี่ เผากิเลส 3 วัน 2 คืน (ศิษย์เก่า)' => 10,
            'คอร์สวิปัสสานาสติปัฏฐานสี่ เผากิเลส (ศิษย์เก่า)' => 3,
        ];

        $locationMap = [
            'แก่งคอย' => 1,
            'ลานหิน' => 2,
            'หาดใหญ่' => 3,
            'มูลนิธิ อ่อนนุช' => 4,
            'ภูเก็ต' => 5,
        ];

        foreach ($courses as $course) {

            $course->location_id = $locationMap[$course->location] ?? null;
            $course->category_id = $categoryMap[$course->category] ?? null;


            $course->save();
        }
    }
}
