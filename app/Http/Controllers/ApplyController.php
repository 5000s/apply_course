<?php

namespace App\Http\Controllers;

use App\Models\Apply;
use App\Models\CourseCategory;
use App\Models\Location;
use App\Models\Member;
use Carbon\Carbon;
use Google\Service\AdMob\App;
use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Assume you have a Course model

class ApplyController extends Controller
{

    public function showCourseForStudent()
    {

        $now = Carbon::now();

        // Query to get courses
        $courses_saraburi = self::getCourses(1, $now);
        $courses_surin = self::getCourses(2, $now);
        $courses_hadyai = self::getCourses(3, $now);
        $courses_bangkok = self::getCourses(4, $now);
        $courses_phuket = self::getCourses(5, $now);

        $data = [];

        $data['courses_saraburi'] = $courses_saraburi;
        $data['location_saraburi'] = Location::where("id", 1)->first();

        $data['courses_surin'] = $courses_surin;
        $data['location_surin'] = Location::where("id", 2)->first();

        $data['courses_hadyai'] = $courses_hadyai;
        $data['location_hadyai'] = Location::where("id", 3)->first();

        $data['courses_bangkok'] = $courses_bangkok;
        $data['location_bangkok'] = Location::where("id", 4)->first();

        $data['courses_phuket'] = $courses_phuket;
        $data['location_phuket'] = Location::where("id", 5)->first();




        return view('courses.list', $data); // Return the view with the courses list
    }

    public static function getCourses($location_id, $dateStart)
    {
        return DB::table('courses as c')
            ->join('course_categories as cc', 'c.category_id', '=', 'cc.id') // Join with course_categories table
            ->select(
                'c.id',
                'c.date_start',
                'cc.show_name as name', // Get course category name
                'cc.show_name_en as name_en', // Get course category name
                'c.date_end',
                'c.category',
                'c.state'
            )
            ->whereDate('c.date_start', '>', $dateStart)
            //            ->whereIn('c.category_id', [1, 3, 5, 6, 8])
            ->where('c.location_id', $location_id)
            ->orderBy('c.date_start', 'asc')
            ->get()
            ->map(function ($course) {


                $stateMap = [
                    'เปิดรับสมัคร' => ['key' => 'open',      'en' => 'Open for registration'],
                    'ปิดรับสมัคร'  => ['key' => 'closed',    'en' => 'Closed'],
                    'ยกเลิกคอร์ส'  => ['key' => 'cancelled', 'en' => 'Cancelled'],
                ];

                $entry = $stateMap[$course->state] ?? ['key' => 'other', 'en' => (string)$course->state];

                // normalized key for translations
                $course->state_key = $entry['key'];

                // direct English label (if you want to use it right away)
                $course->state_en  = $entry['en'];



                // Parse the start and end dates
                $startDate = Carbon::parse($course->date_start)->locale('th');
                $endDate = Carbon::parse($course->date_end)->locale('th');

                // Add 543 years to convert Gregorian year to Thai Buddhist year
                $thaiYear = $endDate->year + 543;

                // Format the date range with Thai Buddhist year
                if ($startDate->isSameDay($endDate)) {
                    $course->date_range = "{$startDate->translatedFormat('j F')} $thaiYear";
                } else {
                    $course->date_range = "{$startDate->translatedFormat('j')} – {$endDate->translatedFormat('j F')} $thaiYear";
                }

                $course->month_year = "{$endDate->translatedFormat('F')} $thaiYear";


                // ---- English version ----
                $startDateEn = Carbon::parse($course->date_start)->locale('en');
                $endDateEn   = Carbon::parse($course->date_end)->locale('en');

                if ($startDateEn->isSameDay($endDateEn)) {
                    $course->date_range_en = $startDateEn->translatedFormat('j F Y');
                } else {
                    $course->date_range_en = $startDateEn->translatedFormat('j') . " – " . $endDateEn->translatedFormat('j F Y');
                }

                $course->month_year_en = $endDateEn->translatedFormat('F Y');

                return $course;
            });
    }

    public static function getCourseWithMember($location_id, $member_id, $dateStart)
    {
        return DB::table('courses as c')
            ->join('course_categories as cc', 'c.category_id', '=', 'cc.id') // Join with course_categories table
            ->leftJoin('applies as a', function ($join) use ($member_id) {
                $join->on('c.id', '=', 'a.course_id')
                    ->where('a.member_id', '=', $member_id)
                    ->where('a.cancel', '=', 0);
            }) // Join with applies table for member-specific data
            ->select(
                'c.id',
                'c.date_start',
                'cc.show_name as name', // Get course category name
                'cc.show_name_en as name_en', // Get course category name
                'c.date_end',
                'c.category',
                'c.category_id',
                'c.state',
                'a.id as apply_id', // Member's application ID
                'a.cancel'
            )
            ->whereDate('c.date_start', '>', $dateStart) // Filter courses starting after $dateStart
            ->where('c.location_id', $location_id) // Filter by location
            ->orderBy('c.date_start', 'asc') // ✅ Order by start date (earliest first)
            ->get()
            ->map(function ($course) {


                $stateMap = [
                    'เปิดรับสมัคร' => ['key' => 'open',      'en' => 'Open for registration'],
                    'ปิดรับสมัคร'  => ['key' => 'closed',    'en' => 'Closed'],
                    'ยกเลิกคอร์ส'  => ['key' => 'cancelled', 'en' => 'Cancelled'],
                ];

                $entry = $stateMap[$course->state] ?? ['key' => 'other', 'en' => (string)$course->state];

                // normalized key for translations
                $course->state_key = $entry['key'];

                // direct English label (if you want to use it right away)
                $course->state_en  = $entry['en'];



                // Parse the start and end dates
                $startDate = Carbon::parse($course->date_start)->locale('th');
                $endDate = Carbon::parse($course->date_end)->locale('th');

                // Add 543 years to convert Gregorian year to Thai Buddhist year
                $thaiYear = $endDate->year + 543;

                // Format the date range with Thai Buddhist year
                if ($startDate->isSameDay($endDate)) {
                    $course->date_range = "{$startDate->translatedFormat('j F')} $thaiYear";
                } else {
                    $course->date_range = "{$startDate->translatedFormat('j')} – {$endDate->translatedFormat('j F')} $thaiYear";
                }

                $course->month_year = "{$endDate->translatedFormat('F')} $thaiYear";


                // ---- English version ----
                $startDateEn = Carbon::parse($course->date_start)->locale('en');
                $endDateEn   = Carbon::parse($course->date_end)->locale('en');

                if ($startDateEn->isSameDay($endDateEn)) {
                    $course->date_range_en = $startDateEn->translatedFormat('j F Y');
                } else {
                    $course->date_range_en = $startDateEn->translatedFormat('j') . " – " . $endDateEn->translatedFormat('j F Y');
                }

                $course->month_year_en = $endDateEn->translatedFormat('F Y');

                return $course;
            });
    }

    public static function getCourse(int $course_id)
    {
        $course = DB::table('courses as c')
            ->join('course_categories as cc', 'c.category_id', '=', 'cc.id')
            ->where('c.id', $course_id)
            ->select(
                'c.id',
                'c.date_start',
                'c.date_end',
                'c.category',
                'c.category_id',
                'c.state',
                'cc.show_name as name',
                'cc.show_name_en as name_en',
                'c.location_id as location_id'
            )
            ->first();

        if (! $course) {
            return null;
        }

        // ---- State mapping (TH -> key + EN) ----
        $stateMap = [
            'เปิดรับสมัคร' => ['key' => 'open',      'en' => 'Open for registration'],
            'เต็มแล้ว'     => ['key' => 'full',      'en' => 'Full'],
            'ปิดรับสมัคร'  => ['key' => 'closed',    'en' => 'Closed'],
            'ยกเลิกคอร์ส'  => ['key' => 'cancelled', 'en' => 'Cancelled'],
        ];
        $entry = $stateMap[$course->state] ?? ['key' => 'other', 'en' => (string) $course->state];
        $course->state_key = $entry['key'];
        $course->state_en  = $entry['en'];

        // ---- TH (B.E.) date range ----
        $startTh = Carbon::parse($course->date_start)->locale('th');
        $endTh   = Carbon::parse($course->date_end)->locale('th');
        $thaiYear = $endTh->year + 543;

        $course->date_range = $startTh->isSameDay($endTh)
            ? "{$startTh->translatedFormat('j F')} {$thaiYear}"
            : "{$startTh->translatedFormat('j')} – {$endTh->translatedFormat('j F')} {$thaiYear}";

        $course->month_year = "{$endTh->translatedFormat('F')} {$thaiYear}";

        // ---- EN (Gregorian) date range ----
        $startEn = Carbon::parse($course->date_start)->locale('en');
        $endEn   = Carbon::parse($course->date_end)->locale('en');

        $course->date_range_en = $startEn->isSameDay($endEn)
            ? $startEn->translatedFormat('j F Y')
            : $startEn->translatedFormat('j') . ' – ' . $endEn->translatedFormat('j F Y');

        $course->month_year_en = $endEn->translatedFormat('F Y');

        return $course;
    }


    public function index(Request $request, $member_id)
    {
        $user = Auth::user();

        $location_id = $request->input('location', 1);
        $now = Carbon::now();

        // Query to get courses
        $courses = self::getCourseWithMember($location_id, $member_id, $now);
        $location = Location::where("id", $location_id)->first();
        $member = Member::where("id", $member_id)->first();

        $customOrder = [4, 1, 3, 5]; // IDs in the order you want
        $locations = Location::whereIn("id", $customOrder)
            ->orderByRaw("FIELD(id, " . implode(",", $customOrder) . ")")
            ->get();


        $data = [];
        $data['courses'] = $courses;
        $data['course_location'] = $location;
        $data['course_locations'] = $locations;
        $data['selected_location_id'] = $location_id;
        $data['member_id'] = $member_id;
        $data['member'] = $member;
        $data['user'] = $user;

        $member_status = $member->status;

        if ($member_status == "ผู้สมัครใหม่" || $member_status == "ศิษย์อานาฯ ๑ วัน") {
            $data['allow_types'] = CourseCategory::where('allow_new', 1)->pluck('id')->all();
        } else if ($member_status == "ศิษย์อานาปานสติ") {
            $data['allow_types'] = CourseCategory::where('allow_anapa', 1)->pluck('id')->all();
        } else if ($member_status == "ศิษย์เตโชวิปัสสนา") {
            $data['allow_types'] = CourseCategory::where('allow_techo', 1)->pluck('id')->all();
        }





        return view('courses.index', $data); // Return the view with the courses list
    }

    public function memberApplyHistory(Request $request, $member_id)
    {
        $user = Auth::user();


        if (!$this->checkUserAccessMember($member_id)) {
            return redirect()->route('profile')->withErrors('The Member is not found.');
        }

        $member = Member::where("id", $member_id)->first();

        $applies  = DB::table('applies as a')
            ->select(
                'a.id as apply_id',
                'a.created_at as apply_date',
                'a.state as state',
                'cc.show_name as course_name', // Get course category name
                'cc.show_name_en as course_name_en', // Get course category name
                'c.id as course_id',
                'a.cancel as cancel',
                'c.coursename',
                'c.category',
                'c.location',
                'c.date_start',
                'c.date_end',
                'l.show_name as location_name',
                'l.show_name_en as location_name_en',
                DB::raw('DATEDIFF(c.date_start, NOW()) as days_until_start')
            )
            ->join('courses as c', 'c.id', '=', 'a.course_id')
            ->join('course_categories as cc', 'c.category_id', '=', 'cc.id') // Join course categories
            ->join('locations as l', 'c.location_id', '=', 'l.id') // Join course categories

            ->where('a.member_id', $member_id)
            ->orderBy('c.date_start', 'desc')
            ->get()
            ->map(function ($course) {

                $stateMap = [
                    'เปิดรับสมัคร' => ['key' => 'open',      'en' => 'Open for registration'],
                    'ปิดรับสมัคร'  => ['key' => 'closed',    'en' => 'Closed'],
                    'ยกเลิกคอร์ส'  => ['key' => 'cancelled', 'en' => 'Cancelled'],
                ];

                $entry = $stateMap[$course->state] ?? ['key' => 'other', 'en' => (string)$course->state];

                // normalized key for translations
                $course->state_key = $entry['key'];

                // direct English label (if you want to use it right away)
                $course->state_en  = $entry['en'];



                // Parse the start and end dates
                $startDate = Carbon::parse($course->date_start)->locale('th');
                $endDate = Carbon::parse($course->date_end)->locale('th');

                // Add 543 years to convert Gregorian year to Thai Buddhist year
                $thaiYear = $endDate->year + 543;

                // Format the date range with Thai Buddhist year
                if ($startDate->isSameDay($endDate)) {
                    $course->date_range = "{$startDate->translatedFormat('j F')} $thaiYear";
                } else {
                    $course->date_range = "{$startDate->translatedFormat('j')} – {$endDate->translatedFormat('j F')} $thaiYear";
                }

                $course->month_year = "{$endDate->translatedFormat('F')} $thaiYear";


                // ---- English version ----
                $startDateEn = Carbon::parse($course->date_start)->locale('en');
                $endDateEn   = Carbon::parse($course->date_end)->locale('en');

                if ($startDateEn->isSameDay($endDateEn)) {
                    $course->date_range_en = $startDateEn->translatedFormat('j F Y');
                } else {
                    $course->date_range_en = $startDateEn->translatedFormat('j') . " – " . $endDateEn->translatedFormat('j F Y');
                }

                $course->month_year_en = $endDateEn->translatedFormat('F Y');

                return $course;
            });

        return view('members.history', compact('applies', 'member_id', 'user', 'member'));
    }

    public function checkUserAccessMember($member_id): bool
    {
        $user = Auth::user();

        if ($user->admin == 1) {
            return true;
        }

        $user_id = $user->id;


        $email = Auth::user()->email;

        $members = Member::where("email", $email)->where("id", $member_id)->first();
        if ($members) {
            return true;
        }
        return false;
    }

    public function show($member_id, $course_id)
    {



        if (!$this->checkUserAccessMember($member_id)) {
            return redirect()->route('profile')->withErrors('The Member is not found.');
        }

        $course = self::getCourse($course_id);

        $location = Location::find($course->location_id);

        // Ensure the course status is 'open', otherwise show an error or redirect
        $apply = Apply::where('course_id', $course_id)
            ->where('member_id', $member_id)
            ->where(function ($q) {
                $q->where('cancel', 0)
                    ->orWhereNull('cancel');
            })
            ->orderByDesc('id')
            ->first();



        $member = Member::where("id", $member_id)->first();

        if (!$apply) {
            $apply = new Apply();
            $apply->member_id = $member_id;
            $apply->course_id = $course_id;
            $apply->application = "";
        }

        if ($course->state != 'เปิดรับสมัคร') {
            return redirect()->route('courses.index', $member_id)->withErrors('Course is not open for application.');
        }
        $CourseApplyController = new CourseApplyController();

        $lang = "th";
        if (preg_match('/[a-zA-Z]/', $member->first_name)) {
            $lang = "en";
        }
        return  $CourseApplyController->applyConfirm($member_id, $course_id, $member->first_name, $member->last_name, $member->gender, $member->phone, $member->birth_date, $lang);

        // return view('courses.show', compact('course', 'location', 'member_id', 'apply', 'member')); // Return the view with the course details
    }

    public function save(Request $request, $member_id)
    {
        ini_set('upload_max_filesize', '10M');
        ini_set('post_max_size', '12M');

        $course_id = $request->input("course_id");

        if (!$this->checkUserAccessMember($member_id)) {
            return redirect()->route('profile')->withErrors('The Member is not found.');
        }


        $course = Course::where("id", $course_id)->first();
        $van = $request->input("van");
        $shelter = $request->input("shelter", "ทั่วไป");

        $course = Course::where("id", $course_id)->first();
        if (!$course) {
            return redirect()->route('profile')->withErrors('The Course is not found.');
        }


        if ($course) {
            $apply = new Apply();
            $apply->member_id = $member_id;
            $apply->course_id = $course_id;
            $apply->van = $van;
            $apply->shelter = $shelter;
            $apply->cancel = 0;
            $apply->state = "ยื่นใบสมัคร";
            $apply->created_by = "USER";
            $apply->save();


            if ($request->hasFile('registration_form')) {

                $request->validate([
                    'course_id' => 'required|exists:courses,id',
                    'van' => 'required|in:no,yes',
                    'registration_form' => 'required|file|mimes:pdf,jpg,jpeg,png|max:8192', // 4MB Max
                ]);


                $file = $request->file('registration_form');

                logger()->info('File size being uploaded: ' . $file->getSize());

                $extension = $file->getClientOriginalExtension();
                $filename = $this->generateFileName($member_id, $course_id, $extension);
                $storagePath = 'uploads/courses/' . $course_id;

                $file->storeAs($storagePath, $filename, 'public');
                $apply->application = $storagePath . '/' . $filename;

                $apply->save();

                return redirect()->route('courses.show', ['member_id' => $member_id, 'course_id' => $course_id]);
            } else {
                dd($request->file('registration_form'));
            }
        } else {
            return redirect()->route('profile')->withErrors('The Course is not found.');
        }
    }


    public function update(Request $request, $member_id, $apply_id)
    {

        $course_id = $request->input("course_id");

        if (!$this->checkUserAccessMember($member_id)) {
            return redirect()->route('profile')->withErrors('The Member is not found.');
        }

        $course = Course::where("id", $course_id)->first();
        if (!$course) {
            return redirect()->route('profile')->withErrors('The Course is not found.');
        }


        $apply = Apply::where("id", $apply_id)->where("member_id", $member_id)->first();
        $van = $request->input("van");
        $shelter = $request->input("shelter", "ทั่วไป");

        $apply->van = $van;
        $apply->shelter = $shelter;
        $apply->save();

        return redirect()->route('courses.show', ['member_id' => $member_id, 'course_id' => $course_id]);
    }


    public function cancel(Request $request, $member_id)
    {

        $course_id = $request->input("course_id");
        $cancel = $request->input("cancel");

        if (!$this->checkUserAccessMember($member_id)) {
            return redirect()->route('profile')->withErrors('The Member is not found.');
        }


        if ($cancel == "cancel") {
            $applys = Apply::where("course_id", $course_id)->where("member_id", $member_id)->get();
            foreach ($applys as $apply) {
                $apply->cancel = 1;
                $apply->cancel_at = Carbon::now();
                $apply->updated_by = "USER";
                $apply->application = null;
                $apply->save();
            }

            return redirect()->route('courses.show', ['member_id' => $member_id, 'course_id' => $course_id]);
        } else {
            dd($request->file('registration_form'));
        }
    }

    public function cancelByUser(Request $request, $member_id, $apply_id)
    {

        if (!$this->checkUserAccessMember($member_id)) {
            return redirect()->route('profile')->withErrors('The Member is not found.');
        }

        $apply = Apply::where("id", $apply_id)->where("member_id", $member_id)->first();

        if (!$apply) {
            return redirect()->route('profile')->withErrors('The Apply is not found.');
        }

        $applys = Apply::where("course_id", $apply->course_id)->where("member_id", $member_id)->get();

        foreach ($applys as $apply) {
            $apply->cancel = 1;
            $apply->cancel_at = Carbon::now();
            $apply->updated_by = "USER";
            $apply->application = null;
            $apply->save();
        }

        return redirect()->route('courses.history', ['member_id' => $member_id]);
    }


    function generateFileName($course_id, $member_id, $fileExtension)
    {
        $inputString = $member_id . "_" . $course_id;

        // Generate an MD5 hash of the input string
        $hash = md5($inputString);

        // Prime numbers less than 32, adjusted for zero-based indexing
        $primePositions = [1, 2, 4, 6, 10, 12, 16, 18, 22, 28, 30];

        $shortHash = '';
        foreach ($primePositions as $position) {
            $shortHash .= $hash[$position];
        }

        // Concatenate the short hash and the file extension to form the final string
        $hashedFilename = $member_id . "_" . $course_id . "_" . $shortHash . "." . $fileExtension;

        return $hashedFilename;
    }

    public function updateRemark(int $apply_id)
    {
        $apply = Apply::find($apply_id);

        if ($apply) {
            $apply->remark = request('remark');
            $apply->save();
            return response()->json(['status' => 'ok']);
        } else {
            return response()->json(['status' => 'error']);
        }
    }
}
