<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeRestriction extends Model
{
    /** @use HasFactory<\Database\Factories\TimeRestrictionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'user_exemptions'
    ];

    protected $casts = [
        'user_exemptions' => 'array',
    ];

    public function allowedUserString()
    {

        return implode(', ', $this->user_exemptions);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
