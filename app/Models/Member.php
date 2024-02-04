<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
