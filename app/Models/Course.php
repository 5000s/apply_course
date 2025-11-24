<?php

namespace App\Models;

use App\Helper\ThaiLocal;
use App\Services\ThaiDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    //
    protected $dates = ['date_start','date_end', 'listed_date'];

    public function getCourseLocationAttribute(){

        $location = Location::where("id",$this->location_id)->first();

        return $location;
    }


    public function getCourseNameAttribute(){

        $courseName =  $this->category;
        $coursedate =  $this->courseDateTxt;
        $courseName .= ": $coursedate";

        return $courseName;
    }

    public function getCourseDateTxtAttribute(){

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

        if ($month == $monthEnd){
            if ($day == $dayEnd){
                $courseDate = "$day $miniMonth  $year";
            }else{
                $courseDate = "$day - $dayEnd  $miniMonth  $year";
            }
        }else{
            $courseDate = " $day $miniMonth - $dayEnd $miniMonthENd  $year";
        }

        return $courseDate;
    }

    public function getStartEnAttribute(){
        return $this->date_start->format("Y-m-d");
    }

    public function getStartAttribute(){
        return ThaiDate::thaiFormat($this->date_start);
    }

    public function getEndAttribute(){
        return ThaiDate::thaiFormat($this->date_end);
    }

    public function location(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Location::class,"location_id","id");
    }


    public static function generateCourseName($start_date, $end_date) {
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
