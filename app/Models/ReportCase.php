<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCase extends Model
{
    use HasFactory;
    protected $table = 'report_cases';
    protected $fillable = [
        'gender',
        'name',
        'surname',
        'birthdate',
        'phone',
        'email',
        'ipv4',
        'ipv6',
        'city',
        'province',
        'country',
        'latitude',
        'longitude',
    ];
}
