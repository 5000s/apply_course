<?php

namespace Tests\Unit;

use App\Models\Course;
use Carbon\Carbon;
use Tests\TestCase; // ต้องบูต Laravel เพราะ Eloquent แปลงวันที่ผ่าน connection (ไม่ได้ query DB)

class CourseApplyCutoffTest extends TestCase
{
    private function course(?string $dateStart): Course
    {
        $course = new Course();
        $course->date_start = $dateStart ? Carbon::parse($dateStart) : null;

        return $course;
    }

    public function test_cutoff_is_seven_days_before_start(): void
    {
        $this->assertSame('2026-12-03 00:00:00', $this->course('2026-12-10 00:00:00')->applyCutoffDate()->toDateTimeString());
    }

    /**
     * @dataProvider cutoffProvider
     */
    public function test_is_past_apply_cutoff(string $now, bool $expected): void
    {
        $this->assertSame($expected, $this->course('2026-12-10')->isPastApplyCutoff(Carbon::parse($now)));
    }

    public function cutoffProvider(): array
    {
        return [
            '8 days before (open)' => ['2026-12-02 23:59:59', false],
            'cutoff day 00:00' => ['2026-12-03 00:00:00', true],
            'cutoff day afternoon' => ['2026-12-03 15:00:00', true],
            'course start day' => ['2026-12-10 08:00:00', true],
        ];
    }

    public function test_no_start_date_is_never_cut_off(): void
    {
        $this->assertNull($this->course(null)->applyCutoffDate());
        $this->assertFalse($this->course(null)->isPastApplyCutoff(Carbon::parse('2030-01-01')));
    }
}
