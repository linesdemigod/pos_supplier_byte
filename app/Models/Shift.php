<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    /** @use HasFactory<\Database\Factories\ShiftFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'status',
        'total_sales',
        'tenant_id'
    ];

    public function user()
    {

        return $this->belongsTo(User::class);
    }

    public function sales()
    {

        return $this->hasMany(Sale::class);

    }

    public function payments()
    {

        return $this->hasMany(Payment::class);

    }

    public function store()
    {

        return $this->belongsTo(Store::class);
    }

}
