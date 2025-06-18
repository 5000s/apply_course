<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $table = "team_members";

    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'position',
        'join_at',
        'leave_at',
    ];

    protected $dates  = [ 'join_at', 'leave_at' ];


    public function member()
    {
        return $this->belongsTo(Member::class, 'user_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
