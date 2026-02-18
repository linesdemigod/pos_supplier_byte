<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySale extends Model
{
    /** @use HasFactory<\Database\Factories\DailySaleFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_id',
        'date',
        'total_sales',
        'open_time',
        'close_time',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeSearch($query, $value)
    {

        return $query->where(function ($query) use ($value) {
            // Search in the current model's columns
            $query->where('open_time', 'LIKE', '%' . $value . '%')
                ->orWhere('close_time', 'LIKE', '%' . $value . '%')
                ->orWhere('status', 'LIKE', '%' . $value . '%');
        })->orWhereHas('user', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%');
        });
    }
}
