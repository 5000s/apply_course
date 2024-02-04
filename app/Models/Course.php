<?php

namespace App\Models;

use App\Helper\ThaiLocal;
use App\Services\ThaiDate;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    //
    protected $dates = ['date_start','date_end'];

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
}
