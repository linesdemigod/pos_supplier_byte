<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreInventory extends Model
{
    /** @use HasFactory<\Database\Factories\StoreInventoryFactory> */
    use HasFactory;

    protected $fillable = [
        'store_id',
        'item_id',
        'quantity',
        'low_stock_threshold'
    ];

    public function item()
    {

        return $this->belongsTo(Item::class);
    }
    public function store()
    {

        return $this->belongsTo(Store::class);
    }

    public function scopeSearch($query, $value)
    {
        return $query->where(function ($query) use ($value) {
            // Search in the current model's columns
            $query->where('quantity', 'LIKE', '%' . $value . '%');
        })->orWhereHas('item', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%');
        });
    }
}
