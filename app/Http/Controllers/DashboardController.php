<?php

namespace App\Http\Controllers;

use App\Models\Apply;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\CourseCategory;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
        $apps = Apply::with(['member', 'course'])
            ->when(count($courseIds), fn($q) => $q->whereIn('course_id', $courseIds))
            ->whereNotNull('member_id')
            ->whereNotNull('course_id')
            ->whereHas('course')              // << ต้องมี course จริง
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
            if (! empty($m->birthdate)) {
                // calculate age as of today
                $age = Carbon::parse($m->birthdate)->age;

                // only include realistic ages
                if ($age >= 0) {
                    // bucket into 5-year ranges
                    $bucket = floor($age / 5) * 5;
                    $label  = "{$bucket}-" . ($bucket + 4);
                    $ageRanges[$label] = ($ageRanges[$label] ?? 0) + 1;
                }
            }
        }

        // 7) First‐course month per member (using the earliest date_start)
        $firstMonths = $apps
            ->groupBy('member_id')
            ->map(function ($group) {
                // ดึงวันที่เริ่มคอร์สที่มีจริงเท่านั้น
                $dates = $group->pluck('course.date_start')->filter();  // << กัน null
                if ($dates->isEmpty()) {
                    return null; // ไม่มีวันที่ให้คำนวณ
                }
                // หา earliest แล้วฟอร์แมตเป็น YYYY-MM
                return Carbon::parse($dates->min())->format('Y-m');
            })
            ->filter(); // ตัด null ออก

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



        // ===== 1) ดึงสรุปจาก SQL ตรง ๆ =====
        $rows = DB::table('applies as a')
            ->join('courses as c', 'a.course_id', '=', 'c.id')
            ->join('members as m', 'a.member_id', '=', 'm.id')
            ->join('course_categories as cc', 'cc.id', '=', 'c.category_id')
            ->selectRaw("
            c.location,
            DATE_FORMAT(c.date_start, '%b-%y')   as month_label,
            cc.show_name                         as course_category,

            SUM(CASE WHEN m.gender = 'ชาย' AND (m.nationality = 'ไทย' OR (m.nationality IS NULL AND m.country = 'Thailand')) THEN 1 ELSE 0 END) as male_th,
            SUM(CASE WHEN m.gender = 'หญิง' AND (m.nationality = 'ไทย' OR (m.nationality IS NULL AND m.country = 'Thailand')) THEN 1 ELSE 0 END) as female_th,

            SUM(CASE WHEN m.gender = 'ชาย' AND ((m.nationality IS NOT NULL AND m.nationality <> 'ไทย') OR (m.nationality IS NULL AND (m.country IS NULL OR m.country <> 'Thailand'))) THEN 1 ELSE 0 END) as male_for,
            SUM(CASE WHEN m.gender = 'หญิง' AND ((m.nationality IS NOT NULL AND m.nationality <> 'ไทย') OR (m.nationality IS NULL AND (m.country IS NULL OR m.country <> 'Thailand'))) THEN 1 ELSE 0 END) as female_for
        ")
            ->where('a.state', 'ผ่านการอบรม')
            ->whereBetween('c.date_start', [$start, $end])
            ->when(count($typeIds), fn($q) => $q->whereIn('c.category_id', $typeIds))
            ->when(count($locIds),  fn($q) => $q->whereIn('c.location_id', $locIds))
            ->groupByRaw("c.location, DATE_FORMAT(c.date_start, '%b-%y'), cc.show_name")
            ->orderBy('c.location')
            ->orderByRaw("MIN(c.date_start)")
            ->get();

        // ===== 2) สร้าง months & summary สำหรับตาราง =====
        $months = collect($rows)->pluck('month_label')->unique()->values()->all();

        // summary[location][course_category][month_label] = {ชายไทย,หญิงไทย,ชายต่างชาติ,หญิงต่างชาติ}
        $summary = [];
        foreach ($rows as $r) {
            $loc = $r->location ?? 'ไม่ระบุสถานที่';
            $cat = $r->course_category ?? 'ไม่ระบุประเภท';
            $mon = $r->month_label;

            $summary[$loc]              = $summary[$loc] ?? [];
            $summary[$loc][$cat]        = $summary[$loc][$cat] ?? [];
            $summary[$loc][$cat][$mon]  = [
                'ชายไทย'     => (int)$r->male_th,
                'หญิงไทย'    => (int)$r->female_th,
                'ชายต่างชาติ' => (int)$r->male_for,
                'หญิงต่างชาติ' => (int)$r->female_for,
            ];
        }

        // เติมเดือนที่หายให้เป็น 0 และเรียงตาม $months
        foreach ($summary as $loc => $cats) {
            foreach ($cats as $cat => $monMap) {
                foreach ($months as $ml) {
                    if (!isset($summary[$loc][$cat][$ml])) {
                        $summary[$loc][$cat][$ml] = [
                            'ชายไทย' => 0,
                            'หญิงไทย' => 0,
                            'ชายต่างชาติ' => 0,
                            'หญิงต่างชาติ' => 0,
                        ];
                    }
                }
                // sort key เดือนตามลำดับใน $months
                $summary[$loc][$cat] = collect($summary[$loc][$cat])
                    ->sortBy(fn($v, $k) => array_search($k, $months))
                    ->all();
            }
        }



        // 10) JSON response
        return response()->json([
            'nationality' => $nationality,
            'gender'      => $gender,
            'ageRanges'   => $ageRanges,
            'monthly'     => $cumulative,

            'months'      => $months,
            'summary'     => $summary,
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
            'Thailand'        => ['thailand', 'thai', 'ไทย', 'ไมย', 'ไท', 'พุทธ', 'สงขลา', 'ทย'],
            'Myanmar'         => ['myanmar', 'burma', 'พม่า'],
            'United Kingdom'  => ['united kingdom', 'uk', 'british', 'england', 'scotland', 'wales', 'english'],
            'United States'   => ['united state', 'united satate', 'usa', 'america', 'อเมริกา'],
            'Australia'       => ['australia', 'australian', 'new zealander', 'australia', 'ออสเตร', 'aussie'],
            'Austria'       =>   ['austria'],
            'France'          => ['france', 'french', 'ฝรั่งเศส', 'français', 'french'],
            'Germany'         => ['germany', 'german', 'gemany', 'เยอ'],
            'India'           => ['india', 'indian', 'india (hindu)', 'indian (uae)'],
            'China'           => ['china', 'chinese', 'chaina', 'จีน'],
            'Japan'           => ['japan', 'japanese', 'nippon', 'ญี่ปุ่น'],
            'Russia'          => ['russia', 'russian', 'rus'],
            'Canada'          => ['canada', 'canadian', 'แคนนาดา'],
            'Singapore'       => ['singapore', 'singaporean'],
            'Switzerland'     => ['switzerland', 'สวิส', 'swedish'],
            'Malaysia'        => ['malaysia', 'malaysian', 'มาเล'],
            'Vietnam'         => ['vietnam', 'vietnamese'],
            'Indonesia'       => ['indonesia', 'indonesian'],
            'Poland'          => ['poland', 'polish'],
            'Italy'           => ['italy', 'italian', 'italy', 'อิตา'],
            'Netherlands'     => ['netherlands', 'nederland', 'dutch', 'duch', 'ดัช'],
            'Spain'           => ['spain', 'spanish'],
            'Turkey'          => ['turkey', 'turkish'],
            'Mexico'          => ['mexico', 'mexican'],
            'Norway'          => ['norway', 'norwegian'],
            'South Korea'     => ['south korea', 'korea', 'south korean', 'korean'],
            'Taiwan'          => ['taiwan', 'taiwanese'],
            'Ireland'         => ['ireland', 'irish'],
            'Philippines'     => ['philippines', 'filipino', 'filipino'],
            'Slovakia'        => ['slovakia', 'slovak', 'slovakai'],
            'Hong Kong'       => ['hong kong', 'hongkong', 'hong kong'],
            'Colombia'        => ['colombia', 'colombian'],
            'Latvia'          => ['latvia', 'latvian'],
            'Lithuania'       => ['lithuania', 'lithuanian'],
            'Peru'            => ['peru', 'peruvian'],
            'Brazil'          => ['brazil', 'brazilian'],
            'Israel'          => ['israel', 'israeli'],
            'Laos'          => ['lao', 'ลาว'],
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
            ->get(['id', 'name']);

        return response()->json($categories);
    }
}
