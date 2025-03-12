<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Member extends Model
{

    protected $fillable = [
    'gender',
    'name',
    'surname',
    'nickname',
    'age',
    'birthdate',
    'buddhism',
    'status',
    'phone',
    'phone_desc',
    'phone_2',
    'phone_2_desc',
    'phone_slug',
    'blacklist',
    'email',
    'province',
    'country',
    'facebook',
    'organization',
    'expertise',
    'degree',
    'career',
    'techo_year',
    'techo_courses',
    'blacklist_release',
    'blacklist_remark',
    'pseudo',
    'url_apply',
    'url_history',
    'url_image',
    'created_by',
    'updated_by',
    'created_at',
    'updated_at',
    'line',
    'nationality',
    'name_emergency',
    'surname_emergency',
    'phone_emergency',
    'relation_emergency',
    'dharma_ex',
    'dharma_ex_desc',
    'know_source',
    ];

    protected $dates = ['birthdate'];

    protected $casts = [
        'birthdate' => 'date', // or 'datetime'
    ];

    public static function getEnumValues($column)
    {
        $type = DB::select("SHOW COLUMNS FROM members WHERE Field = ?", [$column])[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $values = explode(',', $matches[1]);
        return array_map(fn($value) => trim($value, "'"), $values);
    }
}
