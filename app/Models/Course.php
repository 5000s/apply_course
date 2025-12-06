<?php

namespace App\Models;

use App\Helper\ThaiLocal;
use App\Services\ThaiDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    //
    protected $dates = ['date_start', 'date_end', 'listed_date'];

    public function getCourseLocationAttribute()
    {

        $location = Location::where("id", $this->location_id)->first();

        return $location;
    }


    public function getCourseNameAttribute()
    {

        $courseName =  $this->category;
        $coursedate =  $this->courseDateTxt;
        $courseName .= ": $coursedate";

        return $courseName;
    }

    public function getCourseDateTxtAttribute()
    {

        $courseDate = "";

        $start = $this->date_start;
        $day = $start->day;
        $month = $start->month;
        $miniMonth = ThaiLocal::miniMonth($month);
        $year = $start->year + 543;

        $end = $this->date_end;
        $dayEnd = $end->day;
        $monthEnd = $end->month;
        $miniMonthENd = ThaiLocal::miniMonth($monthEnd);

        if ($month == $monthEnd) {
            if ($day == $dayEnd) {
                $courseDate = "$day $miniMonth  $year";
            } else {
                $courseDate = "$day - $dayEnd  $miniMonth  $year";
            }
        } else {
            $courseDate = " $day $miniMonth - $dayEnd $miniMonthENd  $year";
        }

        return $courseDate;
    }

    public function getCourseLongDateTxtAttribute()
    {
        $thai_days = [
            'Sunday' => 'อา.',
            'Monday' => 'จ.',
            'Tuesday' => 'อ.',
            'Wednesday' => 'พ.',
            'Thursday' => 'พฤ.',
            'Friday' => 'ศ.',
            'Saturday' => 'ส.'
        ];

        $thai_months = ThaiLocal::month();

        $start = $this->date_start;
        $end = $this->date_end;

        $startDayAbbr = $thai_days[$start->format('l')];
        $startDay = $start->day;
        $startMonth = $thai_months[$start->month - 1];
        $startYear = $start->year + 543;

        $endDayAbbr = $thai_days[$end->format('l')];
        $endDay = $end->day;
        $endMonth = $thai_months[$end->month - 1];
        $endYear = $end->year + 543;

        $courseDesc = "";
        // Check if category exists and contains "ศิษย์เก่า"
        if (isset($this->category) && str_contains($this->category, "ศิษย์เก่า")) {
            $courseDesc = " (ศิษย์เก่า)";
        }

        if ($start->month == $end->month && $start->year == $end->year) {
            if ($start->day == $end->day) {
                return "$startDayAbbr $startDay $startMonth $startYear" . $courseDesc;
            } else {
                return "$startDayAbbr $startDay – $endDayAbbr $endDay $startMonth $startYear" . $courseDesc;
            }
        } else {
            return "$startDayAbbr $startDay $startMonth – $endDayAbbr $endDay $endMonth $endYear" . $courseDesc;
        }
    }

    public function getCourseLongDateTxtEnAttribute()
    {
        $start = $this->date_start;
        $end = $this->date_end;

        $startDayAbbr = $start->format('D.'); // Sun.
        $startDay = $start->day;
        $startMonth = $start->format('F'); // August
        $startYear = $start->year;

        $endDayAbbr = $end->format('D.');
        $endDay = $end->day;
        $endMonth = $end->format('F');
        $endYear = $end->year;

        if ($start->month == $end->month && $start->year == $end->year) {
            if ($start->day == $end->day) {
                return "$startDayAbbr $startDay $startMonth $startYear";
            } else {
                return "$startDayAbbr $startDay – $endDayAbbr $endDay $startMonth $startYear";
            }
        } elseif ($start->year == $end->year) {
            return "$startDayAbbr $startDay $startMonth – $endDayAbbr $endDay $endMonth $endYear";
        } else {
            return "$startDayAbbr $startDay $startMonth $startYear – $endDayAbbr $endDay $endMonth $endYear";
        }
    }

    public function getStartEnAttribute()
    {
        return $this->date_start->format("Y-m-d");
    }

    public function getStartAttribute()
    {
        return ThaiDate::thaiFormat($this->date_start);
    }

    public function getEndAttribute()
    {
        return ThaiDate::thaiFormat($this->date_end);
    }

    public function location(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Location::class, "location_id", "id");
    }


    public static function generateCourseName($start_date, $end_date)
    {
        Carbon::setLocale('th');

        $start = Carbon::parse($start_date);
        $end = Carbon::parse($end_date);

        $thai_days = [
            'Sunday' => 'อา.',
            'Monday' => 'จ.',
            'Tuesday' => 'อ.',
            'Wednesday' => 'พ.',
            'Thursday' => 'พฤ.',
            'Friday' => 'ศ.',
            'Saturday' => 'ส.'
        ];

        $start_day_abbr = $thai_days[$start->format('l')]; // Get Thai abbreviation
        $start_day = $start->format('d'); // Get day
        $start_month = $start->translatedFormat('M'); // Get month
        $start_year = $start->year + 543; // Convert to Buddhist year (พ.ศ.)

        if ($start->equalTo($end)) {
            return "{$start_day_abbr} {$start_day} {$start_month}. {$start_year}";
        }

        $end_day_abbr = $thai_days[$end->format('l')];
        $end_day = $end->format('d');
        $end_month = $end->translatedFormat('M');
        $end_year = $end->year + 543;

        if ($start->format('m') === $end->format('m') && $start->format('Y') === $end->format('Y')) {
            return "{$start_day_abbr} {$start_day} - {$end_day_abbr} {$end_day} {$start_month}. {$start_year}";
        }

        return "{$start_day_abbr} {$start_day} {$start_month}. - {$end_day_abbr} {$end_day} {$end_month}. {$end_year}";
    }
}
