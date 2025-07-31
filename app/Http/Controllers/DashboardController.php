<?php

namespace App\Http\Controllers;

use App\Models\Apply;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\CourseCategory;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        // for the filter selects
        $types     = CourseCategory::all();
        $locations = Location::all();

        return view('dashboard.index', compact('types', 'locations'));
    }

    public function data(Request $request)
    {

        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');


        // 0) Parse & default filters
        $start   = $request->date_start
            ? Carbon::parse($request->date_start)->startOfDay()
            : Carbon::create(2013, 1, 1)->startOfDay();
        $end     = $request->date_end
            ? Carbon::parse($request->date_end)->endOfDay()
            : Carbon::now();
        $typeIds = (array) $request->input('course_type', []);
        $locIds  = (array) $request->input('course_location', []);

        // 1) Build Course query & pluck IDs
        $courseQ = Course::query();

        if ($request->filled('date_start') && $request->filled('date_end')) {
            $courseQ->whereBetween('date_start', [$start, $end]);
        }

        if (count($locIds)) {
            $courseQ->whereIn('location_id', $locIds);
        }

        if (count($typeIds)) {
            $courseQ->whereIn('category_id', $typeIds);
        }

        $courseIds = $courseQ->pluck('id')->all();

        // 2) Load Applies for those courses
        $apps = Apply::with(['member','course'])
            ->when(count($courseIds), fn($q) => $q->whereIn('course_id', $courseIds))
            ->get();

        // 3) Distinct members
        $members = $apps
            ->pluck('member')
            ->filter()            // drop nulls
            ->unique('id')        // one per member
            ->values();

        // 4) Nationality
        $nationality = $members
            ->map(fn($m) => $this->normalizeNationalityCountry(
                $m->nationality ?: $m->country
            ))
            ->filter()
            ->countBy()
            ->toArray();

        // 5) Gender
        $gender = $members
            ->pluck('gender')
            ->filter()
            ->countBy()
            ->toArray();

        // 6) Age buckets (5-year)
        $ageRanges = [];
        foreach ($members as $m) {
            if (! is_null($m->age)) {
                $bucket = floor($m->age / 5) * 5;
                $label  = "{$bucket}-" . ($bucket + 4);
                $ageRanges[$label] = ($ageRanges[$label] ?? 0) + 1;
            }
        }

        // 7) First‐course month per member (using the earliest date_start)
        $firstMonths = $apps
            ->groupBy('member_id')
            ->map(function($group) {
                // get the minimum date_start string
                $minDate = $group->min(fn($a) => $a->course->date_start);
                // format it as “YYYY-MM”
                return Carbon::parse($minDate)->format('Y-m');
            });

// 8) Count how many distinct members start in each month, then sort
        $monthlyCounts = $firstMonths
            ->countBy()
            ->sortKeys();



// 9) Build the cumulative series (only one count per member)
        $running    = 0;
        $cumulative = [];
        foreach ($monthlyCounts as $month => $count) {
            $running    += $count;
            $cumulative[$month] = $running;
        }


        // 10) JSON response
        return response()->json([
            'nationality' => $nationality,
            'gender'      => $gender,
            'ageRanges'   => $ageRanges,
            'monthly'     => $cumulative,
        ]);
    }





    private function normalizeNationalityCountry(?string $raw): ?string
    {
        if (! $raw) {
            return null;
        }

        $original = trim($raw);
        $val      = mb_strtolower($original);

        $map = [
            'Thailand'        => ['thailand','thai','ไทย','ประทศไทย','thailand (british)','ไมย'],
            'Myanmar'         => ['myanmar','burma'],
            'United Kingdom'  => ['united kingdom','uk','british','england','scotland','wales'],
            'United States'   => ['united state','united satate','usa', 'america' ,'อเมริกา'],
            'Australia'       => ['australia','australian','new zealander','australia' , 'ออสเตร'],
            'France'          => ['france','french','ฝรั่งเศส','français','french'],
            'Germany'         => ['germany','german','gemany','เยอ'],
            'India'           => ['india','indian','india (hindu)','indian (uae)'],
            'China'           => ['china','chinese','chaina'],
            'Japan'           => ['japan','japanese','nippon' ,'ญี่ปุ่น'],
            'Russia'          => ['russia','russian','rus'],
            'Canada'          => ['canada','canadian','แคนนาดา'],
            'Singapore'       => ['singapore','singaporean'],
            'Malaysia'        => ['malaysia','malaysian','มาเล'],
            'Vietnam'         => ['vietnam','vietnamese'],
            'Indonesia'       => ['indonesia','indonesian'],
            'Poland'          => ['poland','polish'],
            'Italy'           => ['italy','italian','italy','อิตาลี่ ดัช'],
            'Netherlands'     => ['netherlands','nederland','dutch'],
            'Spain'           => ['spain','spanish'],
            'Turkey'          => ['turkey','turkish'],
            'Mexico'          => ['mexico','mexican'],
            'Norway'          => ['norway','norwegian'],
            'South Korea'     => ['south korea','korea','south korean','korean'],
            'Taiwan'          => ['taiwan','taiwanese'],
            'Ireland'         => ['ireland','irish'],
            'Philippines'     => ['philippines','filipino','filipino'],
            'Slovakia'        => ['slovakia','slovak','slovakai'],
            'Hong Kong'       => ['hong kong','hongkong','hong kong'],
            'Colombia'        => ['colombia','colombian'],
            'Latvia'          => ['latvia','latvian'],
            'Lithuania'       => ['lithuania','lithuanian'],
            'Peru'            => ['peru','peruvian'],
            'Brazil'          => ['brazil','brazilian'],
            'Israel'          => ['israel','israeli'],
            'Laos'          => ['lao','ลาว'],
            'Unknown'         => ['unknown'],
        ];

        foreach ($map as $canonical => $aliases) {
            foreach ($aliases as $alias) {
                if (str_contains($val, mb_strtolower($alias))) {
                    return $canonical;
                }
            }
        }

        // no alias matched → return the original string
        return $original;
    }


    // app/Http/Controllers/DashboardController.php

    /**
     * AJAX: return course‐type options for the given locations.
     */
    public function typesByLocation(Request $request)
    {
        $locIds = $request->input('locations', []);           // array of selected location IDs
        if (! count($locIds)) {
            return response()->json([]);
        }

        // Find distinct category_ids from courses at these locations:
        $catIds = \App\Models\Course::whereIn('location_id', $locIds)
            ->pluck('category_id')
            ->unique()
            ->values();

        // Load those categories:
        $categories = \App\Models\CourseCategory::whereIn('id', $catIds)
            ->orderBy('name')
            ->get(['id','name']);

        return response()->json($categories);
    }

}
