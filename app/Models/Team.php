<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = "team";


    protected $fillable = [
        'name',
        'description',
        'leader_id',
    ];

    use HasFactory;

    public function leader(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Member::class, 'leader_id');
    }

    public function members(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

}
