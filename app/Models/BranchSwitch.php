<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchSwitch extends Model
{
    /** @use HasFactory<\Database\Factories\BranchSwitchFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_allowed'
    ];

    protected $casts = [
        'user_allowed' => 'array', // Automatically cast JSON to array
    ];

    public function allowedUserString()
    {

        return implode(', ', $this->user_allowed);
    }
}
