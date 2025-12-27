<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseLocationLimit extends Model
{
    use HasFactory;

    protected $table = 'course_limit';

    protected $fillable = [
        'course_category_id',
        'location_id',
        'male_limit',
        'female_limit',
        'max_limit',
        'description',
    ];

    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
