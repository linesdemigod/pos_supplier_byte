<?php

namespace App\Models;

use App\BelongsToStoreOrWarehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashMovement extends Model
{
    /** @use HasFactory<\Database\Factories\CashMovementFactory> */
    use HasFactory, BelongsToStoreOrWarehouse;

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'reason',
        'store_id',
        'shift_id',
        'status',
        'approved_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeSearch($query, $value)
    {
        return $query->where(function ($query) use ($value) {
            // Search in the current model's columns
            $query->where('created_at', 'LIKE', '%' . $value . '%')
                ->orWhere('reason', 'LIKE', '%' . $value . '%')
                ->orWhere('amount', 'LIKE', '%' . $value . '%');
        });
    }
}
