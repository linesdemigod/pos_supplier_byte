<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuantityHistory extends Model
{
    /** @use HasFactory<\Database\Factories\QuantityHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_id',
        'warehouse_id',
        'item_id',
        'change_type',
        'old_quantity',
        'new_quantity'
    ];

    public function user()
    {

        return $this->belongsTo(User::class);
    }

    public function item()
    {

        return $this->belongsTo(Item::class);
    }

    public function store()
    {

        return $this->belongsTo(Store::class);
    }

    public function warehouse()
    {

        return $this->belongsTo(Warehouse::class);
    }

    public function scopeSearch($query, $value)
    {
        return $query->where(function ($query) use ($value) {
            $query->where('change_type', 'LIKE', '%' . $value . '%');
        })->orWhereHas('item', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%')
                ->orWhere('item_code', 'LIKE', '%' . $value . '%');
        })->orWhereHas('user', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%');
        });
    }
}
