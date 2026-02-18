<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPointPermission extends Model
{
    /** @use HasFactory<\Database\Factories\SalesPointPermissionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'permission_name',
        'status'
    ];

    public function user()
    {

        return $this->belongsTo(User::class);
    }
}
