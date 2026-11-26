<?php

namespace App\Services;

/**
 * กฎการนับที่นั่ง (ไม่แตะฐานข้อมูล)
 *
 * $limits = ['male_limit' => int, 'female_limit' => int, 'max_limit' => int]
 * $counts = ['male' => int, 'female' => int, 'total' => int]  (นับเฉพาะใบสมัครที่ยืนยันแล้ว)
 *
 * จำนวนรับเป็น 0 (หรือไม่ได้กรอก) = ให้เจ้าหน้าที่ยืนยันเอง: ระบบไม่ยืนยันอัตโนมัติ และไม่ถือว่าเต็ม
 */
final class CourseSeatPolicy
{
    // ก่อนตัดรอบ 7 วัน: ยืนยันอัตโนมัติเมื่อที่นั่งที่ยืนยันแล้วยังไม่ถึง 50%
    const EARLY_CONFIRM_PERCENT = 50;
    // ตอนตัดรอบ 7 วัน: ยืนยันอัตโนมัติจนเต็ม 100%
    const FULL_PERCENT = 100;

    const GENDER_KEYS = ['ชาย' => 'male', 'หญิง' => 'female'];

    /**
     * ยังมีที่ว่าง (ยังไม่ถึง $percent% ทั้งจำนวนรวม และจำนวนตามเพศของผู้สมัคร)
     */
    public static function hasSeat(array $limits, array $counts, ?string $gender, int $percent): bool
    {
        $maxLimit = (int) ($limits['max_limit'] ?? 0);
        if ($maxLimit <= 0 || !self::isBelow((int) $counts['total'], $maxLimit, $percent)) {
            return false;
        }

        $key = self::genderKey($gender);
        if ($key === null) {
            return true;
        }

        $genderLimit = (int) ($limits["{$key}_limit"] ?? 0);

        return $genderLimit > 0 && self::isBelow((int) $counts[$key], $genderLimit, $percent);
    }

    /**
     * ที่นั่งเต็มแล้วสำหรับผู้สมัครเพศนี้ (จำนวนรวมเต็ม หรือ จำนวนตามเพศเต็ม)
     */
    public static function isFull(array $limits, array $counts, ?string $gender): bool
    {
        $maxLimit = (int) ($limits['max_limit'] ?? 0);
        if ($maxLimit > 0 && (int) $counts['total'] >= $maxLimit) {
            return true;
        }

        $key = self::genderKey($gender);
        if ($key === null) {
            return false;
        }

        $genderLimit = (int) ($limits["{$key}_limit"] ?? 0);

        return $genderLimit > 0 && (int) $counts[$key] >= $genderLimit;
    }

    /**
     * จำนวนที่นั่งหลังยืนยันผู้สมัครเพิ่ม 1 คน (คืนค่าเป็น array ใหม่)
     */
    public static function withConfirmed(array $counts, ?string $gender): array
    {
        $next = $counts; // array ของ PHP ถูก copy เมื่อแก้ไข ค่าเดิมของผู้เรียกไม่เปลี่ยน
        $next['total'] = (int) $counts['total'] + 1;

        $key = self::genderKey($gender);
        if ($key !== null) {
            $next[$key] = (int) $counts[$key] + 1;
        }

        return $next;
    }

    /**
     * จำนวนรับเป็น 0 (รวม หรือ เพศของผู้สมัคร) = เจ้าหน้าที่จัดการเอง ระบบไม่ยืนยัน/ไม่ยกเลิก
     */
    public static function isStaffManaged(array $limits, ?string $gender): bool
    {
        if ((int) ($limits['max_limit'] ?? 0) <= 0) {
            return true;
        }

        $key = self::genderKey($gender);

        return $key !== null && (int) ($limits["{$key}_limit"] ?? 0) <= 0;
    }

    public static function genderKey(?string $gender): ?string
    {
        return self::GENDER_KEYS[trim((string) $gender)] ?? null;
    }

    // count ยังไม่ถึง percent% ของ limit (เทียบแบบจำนวนเต็ม เลี่ยงปัดเศษทศนิยม)
    private static function isBelow(int $count, int $limit, int $percent): bool
    {
        return $count * 100 < $limit * $percent;
    }
}
