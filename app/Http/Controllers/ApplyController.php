<?php

namespace App\Http\Controllers;

use App\Models\Apply;
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
    public function index(Request $request, $member_id)
    {
        $location_id = $request->input('location', 1);
        $category_id = $request->input('category', 0);
        $now_year = Carbon::now()->year;
        $year = $request->input('year', $now_year);

        // Query to get courses
        $courses = DB::table('courses as c')
            ->select(
                'c.location',
                'c.id',
                'c.category',
                'c.date_start',
                'c.date_end',
                'c.state',
                'a.id as apply_id',
                'a.cancel'
            )
            ->leftJoin('applies as a', function($join) use ($member_id) {
                $join->on('c.id', '=', 'a.course_id')
                    ->where('a.member_id', '=', $member_id)
                    ->where('a.cancel', '=', 0);
            })
            ->where('c.location_id', $location_id)
            ->where(function ($query) use ($year) {
                $query->whereYear('c.date_start', $year)
                    ->orWhereYear('c.date_end', $year);
            })
            ->groupBy('c.location', 'c.id', 'c.category', 'c.date_start', 'c.date_end', 'c.state', 'a.id', 'a.cancel');

        if ($category_id != 0){
            $courses = $courses->where('c.category_id', $category_id)->get();
        } else {
            $courses = $courses->whereIn('c.category_id', [1,3,5,6,8]);
            $courses = $courses->get();
        }

        return view('courses.index', compact('courses', 'member_id')); // Return the view with the courses list
    }

    public function memberApplyHistory(Request $request, $member_id)
    {

        if ( !$this->checkUserAccessMember($member_id)){
            return redirect()->route('profile')->withErrors('The Member is not found.');
        }

        $applies  = DB::table('applies as a')
            ->select(
                'a.id as apply_id',
                'a.created_at as apply_date',
                'a.state as state',
                'c.id as course_id',
                'c.coursename',
                'c.category',
                'c.location',
                'c.date_start',
                'c.date_end',
                DB::raw('DATEDIFF(c.date_start, NOW()) as days_until_start')
            )
            ->join('courses as c', 'c.id', '=', 'a.course_id')
            ->where('a.member_id', $member_id)
            ->orderBy('a.created_at', 'desc')
            ->get();

        return view('members.history', compact('applies', 'member_id'));
    }

    public function checkUserAccessMember($member_id): bool
    {
        $user_id = Auth::user()->id;
        $email = Auth::user()->email;

        $members = Member::where("email",$email)->where("id",$member_id)->first();
        if($members){
            return true;
        }
        return false;
    }

    public function show($member_id, $course_id)
    {

       if ( !$this->checkUserAccessMember($member_id)){
           return redirect()->route('profile')->withErrors('The Member is not found.');
       }

        $course = Course::findOrFail($course_id); // Find the course or fail
        // Ensure the course status is 'open', otherwise show an error or redirect
        $apply = Apply::where("course_id",$course_id)->where("member_id",$member_id)
            ->where("cancel",0)->orderBy("id","desc")->first();


        if(!$apply){
            $apply = new Apply();
            $apply->member_id = $member_id;
            $apply->course_id = $course_id;
            $apply->application = "";
        }

        if ($course->state != 'เปิดรับสมัคร') {
            return redirect()->route('courses.index', $member_id)->withErrors('Course is not open for application.');
        }
        return view('courses.show', compact('course','member_id', 'apply' )); // Return the view with the course details
    }

    public function save(Request $request, $member_id)
    {
        ini_set('upload_max_filesize', '10M');
        ini_set('post_max_size', '12M');


        if ( !$this->checkUserAccessMember($member_id)){
            return redirect()->route('profile')->withErrors('The Member is not found.');
        }

        $course_id = $request->input("course_id");
        $cancel= $request->input("cancel");


        $course = Course::where("id",$course_id)->first();

        if ($cancel == "cancel") {
            $applys = Apply::where("course_id",$course_id)->where("member_id",$member_id)->get();
            foreach ($applys as $apply){
                $apply->cancel = 1;
                $apply->cancel_at = Carbon::now();
                $apply->updated_by = "USER";
                $apply->application = null;
                $apply->save();
            }

            return redirect()->route('courses.show', ['member_id' => $member_id, 'course_id' => $course_id]);

        }else if ($request->hasFile('registration_form')) {

            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'registration_form' => 'required|file|mimes:pdf,jpg,jpeg,png|max:8192', // 4MB Max
            ]);

            $apply = new Apply();

            $apply->member_id = $member_id;
            $apply->course_id = $course_id;
            $apply->cancel = 0;
            $apply->state = "ยื่นใบสมัคร";

            $apply->created_by = "USER";


            $file = $request->file('registration_form');

            logger()->info('File size being uploaded: ' . $file->getSize());

            $extension = $file->getClientOriginalExtension();
            $filename = $this->generateFileName($member_id,$course_id,$extension);
            $storagePath = 'uploads/courses/' . $course_id;

            $file->storeAs($storagePath, $filename, 'public');
            $apply->application = $storagePath . '/' . $filename;

            $apply->save();
            // Save course application with file path (adjust according to your database structure)
            // Application::create([...]);



             return redirect()->route('courses.show', ['member_id' => $member_id, 'course_id' => $course_id]);

//            return redirect()->route('courses.index',['member_id' => $member_id])->with('status', 'Applied successfully');
        }else if($course->category_id == 8){
            // อานาปา 1 วัน

            $regstration_name = $request->input("regstration_name");

            $apply = new Apply();

            $apply->member_id = $member_id;
            $apply->course_id = $course_id;
            $apply->cancel = 0;
            $apply->state = "ยื่นใบสมัคร";

            $apply->created_by = "USER";
            $apply->application = $regstration_name;
            $apply->save();

            return redirect()->route('courses.show', ['member_id' => $member_id, 'course_id' => $course_id]);

        }else{
            dd($request->file('registration_form'));
        }
    }

    public function edit($id)
    {
        $courseApplication = CourseApplication::findOrFail($id); // Assuming you have a CourseApplication model

        // Check if the application belongs to the authenticated user
        // This check is important for security reasons
        // if ($courseApplication->user_id != auth()->id()) {
        //     abort(403);
        // }

        return view('courses.edit', compact('courseApplication'));
    }

    public function cancel(Request $request, $id)
    {
        // Similar to edit, find the application and cancel it
        // $courseApplication = CourseApplication::findOrFail($id);
        // Perform the cancellation logic
        return redirect()->route('courses.index')->with('status', 'Application cancelled');
    }

    function generateFileName($course_id, $member_id, $fileExtension) {
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
        $hashedFilename = $member_id . "_" . $course_id . "_" .$shortHash . "." . $fileExtension;

        return $hashedFilename;
    }
}
