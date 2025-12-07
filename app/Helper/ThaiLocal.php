<?php

namespace App\Helper;

use Carbon\Carbon;

class ThaiLocal
{
    public static function month(): array
    {
        return [
            'มกราคม',
            'กุมภาพันธ์',
            'มีนาคม',
            'เมษายน',
            'พฤษภาคม',
            'มิถุนายน',
            'กรกฎาคม',
            'สิงหาคม',
            'กันยายน',
            'ตุลาคม',
            'พฤศจิกายน',
            'ธันวาคม'
        ];
    }

    public static function miniMonths(): array
    {
        $thai_months = [
            'ม.ค.',
            'ก.พ.',
            'มี.ค.',
            'เม.ย.',
            'พ.ค.',
            'มิ.ย.',
            'ก.ค.',
            'ส.ค.',
            'ก.ย.',
            'ต.ค.',
            'พ.ย.',
            'ธ.ค.',
        ];

        return $thai_months;
    }

    public static function miniMonth($month): string
    {
        $thai_months = self::miniMonths();

        return $thai_months[$month];
    }
}
