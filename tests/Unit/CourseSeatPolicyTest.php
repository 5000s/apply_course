<?php

namespace Tests\Unit;

use App\Services\CourseSeatPolicy;
use PHPUnit\Framework\TestCase;

class CourseSeatPolicyTest extends TestCase
{
    private const LIMITS = ['male_limit' => 10, 'female_limit' => 20, 'max_limit' => 25];

    private function counts(int $male, int $female): array
    {
        return ['male' => $male, 'female' => $female, 'total' => $male + $female];
    }

    /**
     * @dataProvider hasSeatProvider
     */
    public function test_has_seat(array $counts, ?string $gender, int $percent, bool $expected): void
    {
        $this->assertSame($expected, CourseSeatPolicy::hasSeat(self::LIMITS, $counts, $gender, $percent));
    }

    public function hasSeatProvider(): array
    {
        $early = CourseSeatPolicy::EARLY_CONFIRM_PERCENT;
        $full = CourseSeatPolicy::FULL_PERCENT;

        return [
            'empty course, early' => [$this->counts(0, 0), 'ชาย', $early, true],
            'male 4/10 under 50%' => [$this->counts(4, 0), 'ชาย', $early, true],
            'male 5/10 reached 50%' => [$this->counts(5, 0), 'ชาย', $early, false],
            'male full but female has seat' => [$this->counts(5, 0), 'หญิง', $early, true],
            'total 12/25 under 50% (12.5)' => [$this->counts(3, 9), 'หญิง', $early, true],
            'total 13/25 reached 50%' => [$this->counts(4, 9), 'หญิง', $early, false],
            'female 10/20 reached 50%' => [$this->counts(0, 10), 'หญิง', $early, false],
            'male 9/10 under 100%' => [$this->counts(9, 0), 'ชาย', $full, true],
            'male 10/10 full' => [$this->counts(10, 0), 'ชาย', $full, false],
            'total 25/25 full' => [$this->counts(5, 20), 'หญิง', $full, false],
            'unknown gender uses total only' => [$this->counts(10, 0), null, $full, true],
        ];
    }

    public function test_zero_limit_means_staff_confirm(): void
    {
        $limits = ['male_limit' => 0, 'female_limit' => 20, 'max_limit' => 25];

        $this->assertFalse(CourseSeatPolicy::hasSeat($limits, $this->counts(0, 0), 'ชาย', CourseSeatPolicy::FULL_PERCENT));
        $this->assertFalse(CourseSeatPolicy::isFull($limits, $this->counts(0, 0), 'ชาย'));
        $this->assertFalse(CourseSeatPolicy::hasSeat(['max_limit' => 0] + $limits, $this->counts(0, 0), 'หญิง', 100));
    }

    public function test_null_limits_are_treated_as_zero(): void
    {
        $limits = ['male_limit' => null, 'female_limit' => null, 'max_limit' => null];

        $this->assertFalse(CourseSeatPolicy::hasSeat($limits, $this->counts(0, 0), 'ชาย', 100));
        $this->assertFalse(CourseSeatPolicy::isFull($limits, $this->counts(5, 5), 'ชาย'));
    }

    public function test_is_full(): void
    {
        $this->assertTrue(CourseSeatPolicy::isFull(self::LIMITS, $this->counts(10, 0), 'ชาย'));
        $this->assertFalse(CourseSeatPolicy::isFull(self::LIMITS, $this->counts(10, 0), 'หญิง'));
        $this->assertTrue(CourseSeatPolicy::isFull(self::LIMITS, $this->counts(5, 20), 'ชาย'));
        $this->assertFalse(CourseSeatPolicy::isFull(self::LIMITS, $this->counts(9, 15), 'ชาย'));
    }

    public function test_is_staff_managed(): void
    {
        $this->assertFalse(CourseSeatPolicy::isStaffManaged(self::LIMITS, 'ชาย'));
        $this->assertFalse(CourseSeatPolicy::isStaffManaged(self::LIMITS, null));
        $this->assertTrue(CourseSeatPolicy::isStaffManaged(['max_limit' => 0] + self::LIMITS, 'หญิง'));
        $this->assertTrue(CourseSeatPolicy::isStaffManaged(['male_limit' => 0] + self::LIMITS, 'ชาย'));
        $this->assertFalse(CourseSeatPolicy::isStaffManaged(['male_limit' => 0] + self::LIMITS, 'หญิง'));
    }

    public function test_with_confirmed_returns_new_counts(): void
    {
        $counts = $this->counts(1, 2);

        $this->assertSame(['male' => 2, 'female' => 2, 'total' => 4], CourseSeatPolicy::withConfirmed($counts, 'ชาย'));
        $this->assertSame(['male' => 1, 'female' => 2, 'total' => 4], CourseSeatPolicy::withConfirmed($counts, null));
        $this->assertSame($this->counts(1, 2), $counts);
    }
}
