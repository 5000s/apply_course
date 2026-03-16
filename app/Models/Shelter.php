<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shelter extends Model
{
    use HasFactory;

    protected $table = 'shelters';

    protected $fillable = [
        'number',
        'index',
        'name',
        'surname',
        'note',
        'note_master',
        'extra_user',
        'member_id',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }
}
