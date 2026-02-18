<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlySale extends Model
{
    /** @use HasFactory<\Database\Factories\MonthlySaleFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_id',
        'month',
        'year',
        'total_sales',
        'open_date',
        'close_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(user::class, 'user_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeSearch($query, $value)
    {

        return $query->where(function ($query) use ($value) {
            // Search in the current model's columns
            $query->where('year', 'LIKE', '%' . $value . '%')
                ->orWhere('month', 'LIKE', '%' . $value . '%')
                ->orWhere('status', 'LIKE', '%' . $value . '%');
        })->orWhereHas('user', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%');
        });
    }
}
