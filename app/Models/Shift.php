<?php

namespace App\Models;

use App\BelongsToStoreOrWarehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    /** @use HasFactory<\Database\Factories\ShiftFactory> */
    use HasFactory, BelongsToStoreOrWarehouse;

    protected $fillable = [
        'user_id',
        'store_id',
        'opened_at',
        'closed_at',
        'status',
        'total_sales',
        'store_id',
        'starting_cash',
        'closing_cash',
        'expected_cash',
        'cash_difference'
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
