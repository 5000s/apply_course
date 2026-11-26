<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    use HasFactory;
    protected $table = 'course_categories';

    // กลุ่มประเภทคอร์ส (ใช้ในหน้าตั้งค่าจำนวนรับ course_limit) แบ่งตามความยาวคอร์สในคอลัมน์ day
    const GROUP_ONE_DAY = 'one_day';     // คอร์ส 1 วัน
    const GROUP_SHORT = 'short';         // คอร์ส 2-5 วัน
    const GROUP_VIPASSANA = 'vipassana'; // คอร์สวิปัสสนา มากกว่า 6 วัน

    const LIMIT_GROUPS = [
        self::GROUP_ONE_DAY => 'คอร์ส 1 วัน',
        self::GROUP_SHORT => 'คอร์ส 2-5 วัน',
        self::GROUP_VIPASSANA => 'คอร์สวิปัสสนา (มากกว่า 6 วัน)',
    ];

    // day ไม่เกินค่านี้ = คอร์ส 1 วัน (ไป-กลับ), day ตั้งแต่ค่า VIPASSANA ขึ้นไป = วิปัสสนา
    const ONE_DAY_MAX_DAY = 1;
    const VIPASSANA_MIN_DAY = 6;

    // ผ่านการอบรมคอร์สในหมวดเหล่านี้ = ศิษย์เตโชวิปัสสนา (ใช้ทั้ง cron ปรับสถานะสมาชิก และ Auto Confirm)
    const VIPASSANA_PASSED_CATEGORY_IDS = [1, 2, 3, 4, 6, 8, 10, 12];

    public static function limitGroupForDay($day): string
    {
        $day = (int) $day;

        if ($day <= self::ONE_DAY_MAX_DAY) {
            return self::GROUP_ONE_DAY;
        }

        if ($day >= self::VIPASSANA_MIN_DAY) {
            return self::GROUP_VIPASSANA;
        }

        return self::GROUP_SHORT;
    }

    public function limitGroup(): string
    {
        return self::limitGroupForDay($this->day);
    }
}
