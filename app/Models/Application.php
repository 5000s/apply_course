<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'f',
        'timestamp',
        'email',
        'first_name',
        'last_name',
        'nickname',
        'gender',
        'status',
        'birthday',
        'age',
        'occupation',
        'province',
        'nationality',
        'phone',
        'education',
        'course_preference',
        'has_experience',
        'has_experience_course',
        'meditation_history',
        'application_reason',
        'heard_from',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'location_id',
    ];

    protected $casts = [
        'birthday' => 'date',
        'timestamp' => 'datetime',
    ];

    private static function sanitizeToUtf8mb3($text)
    {
        // removes all characters beyond U+FFFF (like emojis, math symbols, etc.)
        return preg_replace('/[^\x{0000}-\x{FFFF}]/u', '', $text ?? '');
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            foreach ($model->attributes as $key => $value) {
                if (is_string($value)) {
                    $model->{$key} = self::sanitizeToUtf8mb3($value);
                }
            }
        });
    }


}
