<?php

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use App\Models\CourseLocationLimit;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * หน้าแอดมิน: ตั้งค่าจำนวนรับ (course_limit) ต่อ ประเภทคอร์ส x สถานที่
 * โดยแบ่งประเภทคอร์สเป็น 3 กลุ่ม (1 วัน / 2-5 วัน / วิปัสสนา > 6 วัน)
 */
class CourseLimitController extends Controller
{
    private const LIMIT_FIELDS = ['male_limit', 'female_limit', 'max_limit'];
    private const MAX_SEATS = 9999;
    private const MAX_DESCRIPTION_LENGTH = 500;

    public function index(Request $request)
    {
        $group = $this->resolveGroup($request->query('group'));
        $showAll = $request->boolean('show_all');

        $groupCategories = CourseCategory::orderBy('id')->get()
            ->filter(fn(CourseCategory $category) => $category->limitGroup() === $group)
            ->values();

        // ตัวกรอง "คอร์ส": ใช้ได้เฉพาะคอร์สที่อยู่ในกลุ่มที่เลือก
        $categoryId = (int) $request->query('category_id');
        if (!$groupCategories->contains('id', $categoryId)) {
            $categoryId = 0;
        }

        $limits = $this->limitsByPair();

        $sections = Location::orderBy('id')->get()
            ->map(function (Location $location) use ($groupCategories, $limits, $categoryId, $showAll) {
                $rows = $groupCategories
                    ->map(fn(CourseCategory $category) => [
                        'category' => $category,
                        'limit' => $limits->get($this->pairKey($category->id, $location->id)),
                    ])
                    ->filter(function (array $row) use ($categoryId, $showAll) {
                        if ($categoryId) {
                            return (int) $row['category']->id === $categoryId;
                        }
                        // ปกติแสดงเฉพาะคอร์สที่ยังใช้งาน หรือที่เคยตั้งค่าไว้แล้ว
                        return $showAll || $row['category']->active || $row['limit'];
                    })
                    ->values();

                return ['location' => $location, 'rows' => $rows];
            })
            ->filter(fn(array $section) => $section['rows']->isNotEmpty())
            ->values();

        return view('admin.course_limit', [
            'title' => 'ตั้งค่าจำนวนรับ (Course Limit)',
            'groups' => CourseCategory::LIMIT_GROUPS,
            'group' => $group,
            'groupCategories' => $groupCategories,
            'categoryId' => $categoryId,
            'showAll' => $showAll,
            'sections' => $sections,
            'maxSeats' => self::MAX_SEATS,
            'maxDescriptionLength' => self::MAX_DESCRIPTION_LENGTH,
        ]);
    }

    public function save(Request $request)
    {
        $seatRule = 'nullable|integer|min:0|max:' . self::MAX_SEATS;

        $data = $request->validate([
            'group' => 'nullable|string',
            'category_id' => 'nullable|integer',
            'show_all' => 'nullable|boolean',
            'limits' => 'required|array',
            'limits.*' => 'array',
            'limits.*.*.male_limit' => $seatRule,
            'limits.*.*.female_limit' => $seatRule,
            'limits.*.*.max_limit' => $seatRule,
            'limits.*.*.description' => 'nullable|string|max:' . self::MAX_DESCRIPTION_LENGTH,
        ], [], [
            'limits.*.*.male_limit' => 'จำนวนรับชาย',
            'limits.*.*.female_limit' => 'จำนวนรับหญิง',
            'limits.*.*.max_limit' => 'จำนวนรับรวมสูงสุด',
            'limits.*.*.description' => 'หมายเหตุ',
        ]);

        $categories = CourseCategory::all()->keyBy('id');
        $locations = Location::all()->keyBy('id');
        $existing = $this->limitsByPair();

        $changes = [];
        $errors = [];

        foreach ($data['limits'] as $categoryId => $byLocation) {
            foreach ($byLocation as $locationId => $input) {
                // รับเฉพาะคู่ คอร์ส x สถานที่ ที่มีอยู่จริง
                if (!$categories->has($categoryId) || !$locations->has($locationId)) {
                    continue;
                }

                $seats = collect(self::LIMIT_FIELDS)
                    ->mapWithKeys(fn(string $field) => [$field => $this->toSeat($input[$field] ?? null)]);
                $filled = $seats->filter(fn($value) => $value !== null)->count();
                $limit = $existing->get($this->pairKey($categoryId, $locationId));

                if ($filled === 0) {
                    // เว้นว่างทั้ง 3 ช่อง = ไม่ตั้งค่า → ลบรายการเดิม (ระบบจะใช้ค่าเริ่มต้น)
                    if ($limit) {
                        $changes[] = ['action' => 'delete', 'limit' => $limit];
                    }
                    continue;
                }

                if ($filled < count(self::LIMIT_FIELDS)) {
                    $errors[] = "{$categories[$categoryId]->name} @ {$locations[$locationId]->name}: "
                        . 'กรุณากรอกจำนวน ชาย / หญิง / รวม ให้ครบทั้ง 3 ช่อง (หรือเว้นว่างทั้งหมดเพื่อไม่ตั้งค่า)';
                    continue;
                }

                $changes[] = [
                    'action' => 'save',
                    'limit' => $limit,
                    'attributes' => $seats->all() + [
                        'course_category_id' => (int) $categoryId,
                        'location_id' => (int) $locationId,
                        'description' => $this->toDescription($input['description'] ?? null),
                    ],
                ];
            }
        }

        if ($errors) {
            return back()->withErrors($errors)->withInput();
        }

        $summary = $this->applyChanges($changes);

        return redirect()
            ->route('admin.courseLimits', array_filter([
                'group' => $this->resolveGroup($data['group'] ?? null),
                'category_id' => $data['category_id'] ?? null,
                'show_all' => !empty($data['show_all']) ? 1 : null,
            ]))
            ->with('success', "บันทึกแล้ว: แก้ไข/เพิ่ม {$summary['saved']} รายการ, ยกเลิกการตั้งค่า {$summary['deleted']} รายการ");
    }

    /**
     * @param array<int, array{action: string, limit: ?CourseLocationLimit, attributes?: array}> $changes
     * @return array{saved: int, deleted: int}
     */
    private function applyChanges(array $changes): array
    {
        return DB::transaction(function () use ($changes) {
            $saved = 0;
            $deleted = 0;

            foreach ($changes as $change) {
                if ($change['action'] === 'delete') {
                    $change['limit']->delete();
                    $deleted++;
                    continue;
                }

                $limit = ($change['limit'] ?? new CourseLocationLimit())->fill($change['attributes']);
                if ($limit->isDirty()) {
                    $limit->save();
                    $saved++;
                }
            }

            return ['saved' => $saved, 'deleted' => $deleted];
        });
    }

    /**
     * course_limit ทั้งหมด จัดกลุ่มตามคู่ คอร์ส x สถานที่
     * ถ้ามีข้อมูลซ้ำคู่เดียวกัน ใช้แถวแรก (id น้อยสุด) ให้ตรงกับ CourseAutoConfirmService::courseLimit()
     */
    private function limitsByPair(): Collection
    {
        return CourseLocationLimit::orderBy('id')->get()
            ->groupBy(fn(CourseLocationLimit $limit) => $this->pairKey($limit->course_category_id, $limit->location_id))
            ->map(fn(Collection $rows) => $rows->first());
    }

    private function pairKey($categoryId, $locationId): string
    {
        return (int) $categoryId . '-' . (int) $locationId;
    }

    private function resolveGroup($group): string
    {
        return array_key_exists((string) $group, CourseCategory::LIMIT_GROUPS)
            ? (string) $group
            : CourseCategory::GROUP_ONE_DAY;
    }

    private function toSeat($value): ?int
    {
        return ($value === null || $value === '') ? null : (int) $value;
    }

    private function toDescription($value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
