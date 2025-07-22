<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Apply;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Location;
use App\Models\Member;
use Carbon\Carbon;
use Google\Service\AdMob\App;
use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Sheets;

class GoogleSheetController extends Controller
{

    private array $sheetTitleKeywords = [
        'อานาฯ แก่งคอย',
        'อานาฯ ภูเก็ต',
        '1 อานาฯ แสงธรรม',
        'อานาฯ อ่อนนุช',
    ];

    private $spreadsheetId = '1xRD8ASsukcUaZDhVRwnAmVR4wP8fg8H3r6IY4EEfGtg';



    private function importFromKaengKhoi(array $values): int
    {
        $count = 0;

        foreach ($values as $i => $row) {
            if ($i === 0 || empty($row[1])) continue; // Skip header or blank rows

            $timestamp =  isset($row[1]) ? date('Y-m-d H:i:s', strtotime($row[1])) : null;
            $email = $row[2] ?? null;

            $firstName = trim($row[3] ?? '');
            $lastName = trim($row[4] ?? '');
            $coursePreference = trim($row[16] ?? '');
            $birthday = isset($row[23]) ? $this->convertToGregorian($row[8]) : null;


            Application::updateOrCreate(
                [
                    'timestamp' => $timestamp,
                    'email' => $email,
                ],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'birthday' => $birthday,

                    'f' => $row[0] ?? null,
                    'timestamp' => $timestamp,
                    'email' => $email,
                    'nickname' => $row[5] ?? null,
                    'gender' => $row[6] ?? null,
                    'status' => $row[7] ?? null,
                    'age' => is_numeric($row[10] ?? '') ? (int) $row[10] : null,
                    'occupation' => $row[11] ?? null,
                    'province' => $row[12] ?? null,
                    'nationality' => $row[13] ?? null,
                    'phone' => preg_replace('/[^0-9+]/', '', $row[14] ?? ''),
                    'education' => $row[15] ?? null,
                    'has_experience' => isset($row[17]) ? str_contains($row[17], 'เคย') || str_contains(strtolower($row[17]), 'yes') : null,
                    'meditation_history' => $row[18] ?? null,
                    'course_preference' => $coursePreference,
                    'application_reason' => $row[19] ?? null,
                    'heard_from' => $row[20] ?? null,
                    'emergency_contact_name' => $row[21] ?? null,
                    'emergency_contact_phone' => preg_replace('/[^0-9+]/', '', $row[22] ?? ''),
                    'emergency_contact_relationship' => $row[23] ?? null,
                    'location_id' => 1
                ]
            );


            $count++;
        }

        return $count;
    }

    private function importFromPhuket(array $values): int
    {
        $count = 0;

        foreach ($values as $i => $row) {
            if ($i === 0 || empty($row[1])) continue;

            $timestamp =  isset($row[1]) ? date('Y-m-d H:i:s', strtotime($row[1])) : null;
            $email = $row[2] ?? null;

            $firstName = trim($row[3] ?? '');
            $lastName = trim($row[4] ?? '');
            $coursePreference = trim($row[15] ?? '');
            $birthday = isset($row[8]) ? $this->convertToGregorian($row[8]) : null;

            Application::updateOrCreate(
                [
                    'timestamp' => $timestamp,
                    'email' => $email,
                ],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'birthday' => $birthday,

                    'f' => $row[0] ?? null,
                    'timestamp' => $timestamp,
                    'email' => $email,
                    'nickname' => $row[5] ?? null,
                    'status' => $row[6] ?? null,
                    'gender' => $row[7] ?? null,
                    'age' => is_numeric($row[10] ?? '') ? (int) $row[10] : null,
                    'occupation' => $row[11] ?? null,
                    'province' => $row[12] ?? null,
                    'phone' => preg_replace('/[^0-9+]/', '', $row[13] ?? ''),
                    'education' => $row[14] ?? null,
                    'course_preference' => $coursePreference,
                    'has_experience' => isset($row[17]) ? str_contains($row[17], 'เคย') || str_contains(strtolower($row[17]), 'yes') : null,
                    'meditation_history' => $row[18] ?? null,
                    'application_reason' => $row[19] ?? null,
                    'heard_from' => $row[20] ?? null,
                    'emergency_contact_name' => $row[21] ?? null,
                    'emergency_contact_phone' => preg_replace('/[^0-9+]/', '', $row[22] ?? ''),
                    'emergency_contact_relationship' => $row[23] ?? null,
                    'nationality' => $row[25] ?? null,
                    'location_id' => 5
                ]
            );

            $count++;
        }

        return $count;
    }

    function sanitizeToUtf8mb3($text)
    {
        return preg_replace('/[^\x{0000}-\x{FFFF}]/u', '', $text ?? '');
    }


    private function importFromSaengtham(array $values): int
    {
        $count = 0;

        foreach ($values as $i => $row) {
            if ($i === 0 || empty($row[1])) continue;


            $timestamp = isset($row[1]) ? date('Y-m-d H:i:s', strtotime($row[1])) : null;
            $email = $row[2] ?? null;

            $firstName = trim($row[4] ?? '');
            $lastName = trim($row[5] ?? '');
            $coursePreference = trim($row[16] ?? '');
            $birthday = isset($row[9]) ? $this->convertToGregorian($row[9]) : null;

            Application::updateOrCreate(
                [
                    'timestamp' => $timestamp,
                    'email' => $email,
                ],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'birthday' => $birthday,
                    'timestamp' => $timestamp,
                    'has_experience_course'  => isset($row[3]) ? str_contains($row[3], 'เคย') || str_contains(strtolower($row[17]), 'yes') : null,
                    'email' => $email,
                    'nickname' => $row[6] ?? null,
                    'gender' => $row[7] ?? null,
                    'status' => $row[8] ?? null,
                    'age' => is_numeric($row[11] ?? '') ? (int) $row[11] : null,
                    'occupation' => $row[12] ?? null,
                    'province' => $row[13] ?? null,
                    'phone' => preg_replace('/[^0-9+]/', '', $row[14] ?? ''),
                    'education' => $row[15] ?? null,
                    'course_preference' => $coursePreference,
                    'has_experience' => isset($row[17]) ? str_contains($row[17], 'เคย') || str_contains(strtolower($row[17]), 'yes') : null,
                    'meditation_history' => $row[18] ?? null,
                    'application_reason' => $row[19] ?? null,
                    'heard_from' => $row[20] ?? null,
                    'emergency_contact_name' => $row[21] ?? null,
                    'emergency_contact_phone' => preg_replace('/[^0-9+]/', '', $row[22] ?? ''),
                    'emergency_contact_relationship' => $row[23] ?? null,
                    'location_id' => 3
                ]
            );

            $count++;
        }

        return $count;
    }

    private function importFromOnnut(array $values): int
    {
        $count = 0;

        foreach ($values as $i => $row) {
            if ($i === 0 || empty($row[1])) continue;

            $firstName = trim($row[4] ?? '');
            $lastName = trim($row[5] ?? '');
            $coursePreference = trim($row[14] ?? '');
            $birthday = isset($row[22]) ? $this->convertToGregorian($row[22]) : null;
            $timestamp = isset($row[1]) ? date('Y-m-d H:i:s', strtotime($row[1])) : null;
            $email = $row[2] ?? null;


            Application::updateOrCreate(
                [
                    'timestamp' => $timestamp,
                    'email' => $email,
                ],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'birthday' => $birthday,
                    'course_preference' => $coursePreference,
                    'f' => $row[0] ?? null,
                    'timestamp' => $timestamp,
                    'email' => $email,
                    'nickname' => $row[6] ?? null,
                    'gender' => $row[7] ?? null,
                    'status' => $row[8] ?? null,
                    'age' => is_numeric($row[9] ?? '') ? (int) $row[9] : null,
                    'occupation' => $row[10] ?? null,
                    'province' => $row[11] ?? null,
                    'phone' => preg_replace('/[^0-9+]/', '', $row[12] ?? ''),
                    'education' => $row[13] ?? null,
                    'has_experience' => isset($row[15]) ? str_contains($row[15], 'เคย') || str_contains(strtolower($row[15]), 'yes') : null,
                    'meditation_history' => $row[16] ?? null,
                    'application_reason' => $row[17] ?? null,
                    'heard_from' => $row[18] ?? null,
                    'emergency_contact_name' => $row[19] ?? null,
                    'emergency_contact_phone' => preg_replace('/[^0-9+]/', '', $row[20] ?? ''),
                    'emergency_contact_relationship' => $row[21] ?? null,
                    'nationality' => $row[26] ?? null,
                    'location_id' => 4
                ]
            );

            $count++;
        }

        return $count;
    }





    private function convertToGregorian($thaiDate): ?string
    {
        if (!$thaiDate) return null;

        $date = \DateTime::createFromFormat('m/d/Y', $thaiDate);
        if ($date && $date->format('Y') > 2500) {
            $date->modify('-543 years');
        }

        return $date ? $date->format('Y-m-d') : null;
    }



    private function getFilteredSheetTitles(Google_Service_Sheets $service, string $spreadsheetId): array
    {
        $sheets = $service->spreadsheets->get($spreadsheetId)->getSheets();
        $titles = [];
        $index = 1;

        foreach ($sheets as $sheet) {
            $title = $sheet->getProperties()->getTitle();

            foreach ($this->sheetTitleKeywords as $keyword) {
                if (str_contains($title, $keyword)) {
                    $titles[$index++] = $title;
                    break;
                }
            }
        }

        return $titles; // [1 => 'อานาฯ แก่งคอย', 2 => 'อานาฯ ภูเก็ต', ...]
    }


    public function showSheetButtons()
    {
        return view('sheet.list', [
            'sheetList' => $this->sheetTitleKeywords
        ]);
    }

    public function importSheetByIndex($index)
    {
        $sheetName = $this->sheetTitleKeywords[$index] ?? null;

        if (!$sheetName) {
            return redirect()->route('admin.import.sheet.buttons')->with('error', 'Invalid sheet selected.');
        }


        $client = new \Google_Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->addScope(\Google_Service_Sheets::SPREADSHEETS_READONLY);
        $service = new \Google_Service_Sheets($client);

        $range = "'$sheetName'";
        $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues();

        if (empty($values)) {
            return redirect()->route('admin.import.sheet.buttons')->with('error', "No data found in $sheetName.");
        }

        // Dispatch to correct import method based on sheet index

        $location_id = 1;

        switch ($index) {
            case 0:
                $imported = $this->importFromKaengKhoi($values);
                $location_id = 1;
                break;
            case 1:
                $imported = $this->importFromPhuket($values);
                $location_id = 5;
                break;
            case 2:
                $imported = $this->importFromSaengtham($values);
                $location_id = 3;
                break;
            case 3:
                $imported = $this->importFromOnnut($values);
                $location_id = 4;
                break;
            default:
                return redirect()->route('admin.import.sheet.buttons')->with('error', 'No import logic defined for this sheet.');
        }

        return redirect()->route('admin.applications.sync', ['locationId' => $location_id])
            ->with('success', "นำเข้าข้อมูล $imported รายการจาก \"$sheetName\"");
    }


    private function extractCourseDatesFromPreference(string $preferenceText): array
    {
        // Month mappings
        $thaiMonths = [
            'มกราคม'   => '01', 'กุมภาพันธ์' => '02', 'มีนาคม'   => '03',
            'เมษายน'   => '04', 'พฤษภาคม'   => '05', 'มิถุนายน'  => '06',
            'กรกฎาคม'  => '07', 'สิงหาคม'    => '08', 'กันยายน'   => '09',
            'ตุลาคม'   => '10', 'พฤศจิกายน' => '11', 'ธันวาคม'   => '12',
        ];

        $dates = [];

        // Build a regex matching only valid Thai month names
        $thaiMonthPattern = implode('|', array_keys($thaiMonths));

        // --- Extract Thai dates ---
        // Matches: 27 เมษายน 2568
        if (preg_match_all(
            '/\b(\d{1,2})\s+(' . $thaiMonthPattern . ')\s+(\d{4})\b/u',
            $preferenceText,
            $thaiMatches,
            PREG_SET_ORDER
        )) {
            foreach ($thaiMatches as $m) {
                $day   = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                $month = $thaiMonths[$m[2]];
                $year  = (int)$m[3] - 543; // Buddhist to Gregorian
                $dates[] = "{$year}-{$month}-{$day}";
            }
        }

        // --- Extract English dates ---
        // Matches: March 23, 2025
        if (preg_match_all('/\b([A-Z][a-z]+)\s+(\d{1,2}),\s*(\d{4})\b/', $preferenceText, $engMatches, PREG_SET_ORDER)) {
            foreach ($engMatches as $m) {
                $date = \DateTime::createFromFormat('F j, Y', "{$m[1]} {$m[2]}, {$m[3]}");
                if ($date) {
                    $dates[] = $date->format('Y-m-d');
                }
            }
        }

        // Remove duplicates and return
        return array_values(array_unique($dates));
    }



    public function showUnsyncedApplications(Request $request, $locationId)
    {

        $dateCheck = $request->input("date");

        $force_check = $request->input("check");
        $memberIdSet = [];

        $location = Location::find($locationId);

        if ($force_check == "1"){
            $memberIdSet = Member::pluck('id')->flip();
        }

        $categoryId = 11; // Anapa 1 day Course

        $courses = Course::where('location_id', $locationId)
            ->where('category_id', $categoryId)
            ->get();

        $courseArray = [];
        foreach ($courses as $course) {
            $dateKey = Carbon::parse($course->date_start)->format('Y-m-d');
            $courseArray[$dateKey] = $course;
        }


        $applications = Application::where('location_id', $locationId)
            ->get()
            ->map(function ($app) use ($force_check, $memberIdSet) {
                if ($app->member_id) {
                    if ($force_check == "1" ) {
                        if (isset($memberIdSet[$app->member_id])){
                            $app->is_synced = true;
                            return $app;
                        }
                    } else {
                        $app->is_synced = true;
                        return $app;
                    }
                }

                // Normalize gender
                $normalizedGender = null;
                if (str_contains($app->gender, 'ชาย')) {
                    $normalizedGender = 'ชาย';
                } elseif (str_contains($app->gender, 'หญิง')) {
                    $normalizedGender = 'หญิง';
                }

                // Remove Thai titles from first name
                $normalizedFirstName = preg_replace('/^(แม่ชี|พระ|สามเณร|นาง|นาย|นส.)/u', '', trim($app->first_name));
                $lastName = trim($app->last_name);

                // Clean phone numbers
                $appPhone = $this->normalizePhone($app->phone);

                $birthMonth = null;
                $birthDay = null;
                if (!empty($app->birthday)) {
                    try {
                        $birth = \Carbon\Carbon::parse($app->birthday);
                        $birthMonth = $birth->month;
                        $birthDay = $birth->day;
                    } catch (\Exception $e) {
                        // Ignore invalid birthday
                    }
                }

                // Try to find a matching member
                $matchedMember = Member::where('gender', $normalizedGender)
                    ->where(function ($q) use ($normalizedFirstName) {
                        $q->where('name', 'LIKE', '%' . $normalizedFirstName . '%');
                    })
                    ->where(function ($q) use ($lastName) {
                        $q->where('surname', 'LIKE', '%' . $lastName . '%');
                    })
                    ->where(function ($query) use ($appPhone, $birthMonth, $birthDay) {
                        $query->where(function ($q) use ($appPhone) {
                            $q->whereRaw("
                            REGEXP_REPLACE(REPLACE(REPLACE(REPLACE(phone, '+', ''), '-', ''), ' ', ''), '^0+', '') = ?
                        ", [$appPhone]);
                        });

                        if ($birthMonth && $birthDay) {
                            $query->orWhere(function ($q) use ($birthMonth, $birthDay) {
                                $q->whereMonth('birthdate', $birthMonth)
                                    ->whereDay('birthdate', $birthDay);
                            });
                        }
                    })
                    ->first();

                $app->member_id = $matchedMember?->id;
                if ($app->is_synced != ($matchedMember !== null)){
                    $app->is_synced = $matchedMember !== null;
                    $app->save();
                }

                return $app;
            });


        $courseListDates = [];


        foreach ($applications as $app) {
            $app->has_apply = false;

            $courseDates = $this->extractCourseDatesFromPreference($app->course_preference);

            foreach ($courseDates as $date) {
                if (!isset($courseListDates[$date])){
                    $courseListDates[$date] = $date;
                }
            }


            $notMemberApplyDates = [];
            foreach ($courseDates as $date) {
                if (!empty($dateCheck)) {
                    if ($dateCheck == $date){
                        $notMemberApplyDates[$date] = Carbon::parse($date)->addYear("543")->format('d-m-Y');
                    }
                }else{
                    $notMemberApplyDates[$date] = Carbon::parse($date)->addYear("543")->format('d-m-Y');
                }
            }

            $app->courseDates = $notMemberApplyDates;

            if ($app->member_id && $app->course_preference) {

//                if ($app->member_id == 217799)
//                    dd($courseDates, $app->course_preference);
//
                foreach ($courseDates as $date) {



                    if (isset($courseArray[$date])) {
                        $course = $courseArray[$date];


                        $isCheckApplyMore = true;
                        if (!empty($dateCheck)) {
                            if ($dateCheck == $date){
                                $app->has_apply = true;
                                $isCheckApplyMore = false;
                                $courseDateSelect = [];
                                $courseDateSelect[$date] = $course->date_start->addYear(543)->format("d-m-Y");
                                $app->courseDates = $courseDateSelect;
                            }
                        }

                        if ($isCheckApplyMore){
                            $apply = Apply::where('course_id', $course->id)
                                ->where('member_id', $app->member_id)
                                ->first();
                            if ($apply) {

                                $app->has_apply = true;
                            }else{
                                $app->has_apply = false;
                                if (empty($dateCheck)) {
                                    break;
                                }
                            }
                        }


                    }else{
                        $app->has_apply = false;
                    }
                }
            }
        }

        if (!empty($dateCheck)) {
            $applications = $applications->filter(function ($app) use ($dateCheck) {
                $courseDates = $this->extractCourseDatesFromPreference($app->course_preference);
                return in_array($dateCheck, $courseDates);
            })->values(); // reset index
        }

        return view('sheet.sync', compact('applications', 'location', 'courseListDates'));
    }



    function normalizePhone($phone)
    {
        // Remove all non-digit characters except +
        $clean = preg_replace('/[^0-9+]/', '', $phone ?? '');

        // Remove leading zeros
        return ltrim($clean, '0');
    }



    public function syncToMembers(Request $request)
    {
        set_time_limit(600);

        $locationId = $request->input('location_id');
        $dateCheck = $request->input("date");

        $applications = Application::where('location_id', $locationId)->get();

        $imported = 0;
        $courseImported = 0;
        $applyImported = 0;

        $categoryId = 11; // Anapa 1 day Course

        $courses = Course::where('location_id', $locationId)
            ->where('category_id', $categoryId)
            ->get();

        $courseArray = [];
        foreach ($courses as $course) {
            $dateKey = Carbon::parse($course->date_start)->format('Y-m-d');
            $courseArray[$dateKey] = $course;
        }


        $location_name = Location::find($locationId)->name;
        $category_name = CourseCategory::find($categoryId)->name;

        if (!empty($dateCheck)) {
            $applications = $applications->filter(function ($app) use ($dateCheck) {
                $courseDates = $this->extractCourseDatesFromPreference($app->course_preference);
                return in_array($dateCheck, $courseDates);
            })->values(); // reset index
        }


        foreach ($applications as $app) {

            $member = Member::find( $app->member_id);

            $normalizedGender = null;
            if (str_contains($app->gender, 'ชาย')) {
                $normalizedGender = 'ชาย';
            } elseif (str_contains($app->gender, 'หญิง')) {
                $normalizedGender = 'หญิง';
            }

            if (str_contains($app->status, 'แม่ชี')){
                $app->status = 'แม่ชี';
            }else if (str_contains($app->status, 'พระ') || str_contains($app->status, 'ภิกษุ')){
                $app->status = 'ภิกษุ';
            }else{
                $app->status = 'ฆราวาส';
            }

            if(!$member){
                $member = $this->findMatchingMember($app);
                if ($member){
                    $app->member_id = $member->id;
                    $app->save();
                }
            }

            if (!$member) {
                $member = Member::create([
                    'gender' => $normalizedGender,
                    'name' => $app->first_name,
                    'surname' => $app->last_name,
                    'nickname' => $app->nickname,
                    'age' => $app->age ?? 0,
                    'birthdate' => $app->birthday,
                    'buddhism' => $app->status ?? 'ฆราวาส',
                    'status' => 'ผู้สมัครใหม่',
                    'phone' => $app->phone,
                    'email' => $app->email ?? '-',
                    'province' => $app->province,
                    'nationality' => $app->nationality,
                    'degree' => $app->education,
                    'career' => $app->occupation,
                    'dharma_ex' => $app->has_experience ? 'เคย' : 'ไม่เคย',
                    'dharma_ex_desc' => $app->meditation_history,
                    'know_source' => $app->heard_from,
                    'name_emergency' => $app->emergency_contact_name,
                    'phone_emergency' => $app->emergency_contact_phone,
                    'relation_emergency' => $app->emergency_contact_relationship,
                    'created_by' => auth()->user()->name ?? 'system',
                    'updated_by' => auth()->user()->name ?? 'system'
                ]);
                $app->member_id = $member->id;
                $app->is_synced = 1;


                $imported++;
            }

            $applies = Apply::where("member_id", $member->id)->get();
            $courseDateList = $this->extractCourseDatesFromPreference($app->course_preference);


            if (count($courseDateList) > 0){

                foreach ($courseDateList as $date) {

                    $isPassDateCheck = true;

                    if (!empty($dateCheck)){
                        if ($dateCheck != $date){
                            $isPassDateCheck = false;
                        }
                    }

                    if ($isPassDateCheck){
                        $courseDate = Carbon::parse($date);

                        if (isset($courseArray[$date])) {
                            $course = $courseArray[$date];
                        }else{
                            $course = new Course();
                            $course->date_start   = $courseDate;
                            $course->date_end     = $courseDate;
                            $course->listed     = "yes";
                            $course->listed_date     = Carbon::now();
                            $course->location_id  = $locationId;
                            $course->category_id  = $categoryId;
                            $course->state        = "เปิดรับสมัคร";
                            $course->description        =  "คอร์สสมาธิอานาปานสติ 1 วัน";
                            $course->location = $location_name;
                            $course->category = $category_name;
                            $course->courseyear = $courseDate->year;

                            $course->coursename =   Course::generateCourseName($courseDate, $courseDate);
                            $course->save();

                            $courseArray[$date] = $course;

                            $courseImported++;
                        }

                        $apply = Apply::where("course_id", $course->id)->where("member_id", $member->id)->first();


                        if (!$apply){

                            $apply = new Apply();
                            $apply->member_id = $member->id;
                            $apply->course_id = $course->id;
                            $apply->state = "ยื่นใบสมัคร";
                            $apply->firsttime = "yes";
                            if (count($applies) > 0) {
                                $apply->firsttime = "no";
                            }
                            $apply->shelter = "ทั้วไป";
                            $apply->confirmed = "no";
                            $apply->van = "no";
                            $apply->priority_id = 4;
                            $apply->created_by = "Google Sheet";;
                            $apply->updated_by = "Google Sheet";;

                            $apply->save();


                            $applyImported++;
                        }
                    }
                }
            }

        }

        return redirect()->back()->with('success', "นำเข้าข้อมูลสมาชิกใหม่จำนวน $imported คน, เพิ่มคอร์ส $courseImported และเพิ่มข้อมูลสมัคร $applyImported รายการ");

    }


    /**
     * Attempt to find a matching Member by one of three criteria:
     * 1. Name + Surname
     * 2. Name + Phone
     * 3. Name + Birth Day + Birth Month
     *
     * @param \App\Models\Application $app
     * @return \App\Models\Member|null
     */
    private function findMatchingMember(Application $app): ?Member
    {
        // Normalize gender
        $gender = str_contains($app->gender, 'ชาย') ? 'ชาย' : (str_contains($app->gender, 'หญิง') ? 'หญิง' : null);

        // Strip Thai honorifics
        $first = preg_replace('/^(แม่ชี|พระ|สามเณร|นาง|นาย)/u', '', trim($app->first_name));
        $last = trim($app->last_name);

        // Clean phone
        $phone = $this->normalizePhone($app->phone);

        // Extract day and month
        $day = $month = null;
        if ($app->birthday) {
            try {
                $dt = \Carbon\Carbon::parse($app->birthday);
                $day = $dt->day;
                $month = $dt->month;
            } catch (\Exception $e) {
                // ignore
            }
        }

        // Build query with OR conditions
        return Member::where('gender', $gender)
            ->where(function ($q) use ($first, $last, $phone, $day, $month) {
                // 1) Name + Surname
                $q->where(function ($q1) use ($first, $last) {
                    $q1->where('name', 'LIKE', "%{$first}%")
                        ->where('surname', 'LIKE', "%{$last}%");
                })
                    // 2) OR Name + Phone
                    ->orWhere(function ($q2) use ($first, $phone) {
                        $q2->where('name', 'LIKE', "%{$first}%")
                            ->whereRaw(
                                "REGEXP_REPLACE(REPLACE(REPLACE(REPLACE(phone,'+',''),'-',''),' ',''), '^0+', '') = ?",
                                [$phone]
                            );
                    });

                // 3) OR Name + Birth Day+Month
                if ($day && $month) {
                    $q->orWhere(function ($q3) use ($first, $day, $month) {
                        $q3->where('name', 'LIKE', "%{$first}%")
                            ->whereMonth('birthdate', $month)
                            ->whereDay('birthdate', $day);
                    });
                }
            })
            ->first();
    }


}
