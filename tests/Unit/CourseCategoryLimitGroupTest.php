<?php

namespace Tests\Unit;

use App\Models\CourseCategory;
use PHPUnit\Framework\TestCase;

class CourseCategoryLimitGroupTest extends TestCase
{
    /**
     * @dataProvider dayProvider
     */
    public function test_groups_course_type_by_its_day_length($day, string $expected): void
    {
        $this->assertSame($expected, CourseCategory::limitGroupForDay($day));
    }

    public function dayProvider(): array
    {
        return [
            'null day counts as one-day course' => [null, CourseCategory::GROUP_ONE_DAY],
            'day 0 (1-day course, no night)' => [0, CourseCategory::GROUP_ONE_DAY],
            'day 1' => [1, CourseCategory::GROUP_ONE_DAY],
            'day 2 starts the short group' => [2, CourseCategory::GROUP_SHORT],
            'day 3 (3 วัน 2 คืน / 4 วัน 3 คืน)' => [3, CourseCategory::GROUP_SHORT],
            'day 5 ends the short group' => [5, CourseCategory::GROUP_SHORT],
            'day 6 is vipassana' => [6, CourseCategory::GROUP_VIPASSANA],
            'day 7 (8 วัน 7 คืน)' => [7, CourseCategory::GROUP_VIPASSANA],
            'day 14' => [14, CourseCategory::GROUP_VIPASSANA],
            'numeric string from DB' => ['3', CourseCategory::GROUP_SHORT],
        ];
    }

    public function test_every_group_has_a_label(): void
    {
        $this->assertSame(
            [CourseCategory::GROUP_ONE_DAY, CourseCategory::GROUP_SHORT, CourseCategory::GROUP_VIPASSANA],
            array_keys(CourseCategory::LIMIT_GROUPS)
        );
    }
}
