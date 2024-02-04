<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apply extends Model
{

    public function course(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Course::class,"course_id","id");
    }

    public function member(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Member::class,"member_id","id");
    }
}
