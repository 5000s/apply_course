<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestChangeEmail extends Model
{
    use HasFactory;
    protected $table = 'request_change_emails';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'previous_email',
        'new_email',
    ];
}
