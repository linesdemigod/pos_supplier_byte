<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseInventory extends Model
{
    /** @use HasFactory<\Database\Factories\WarehouseInventoryFactory> */
    use HasFactory;

    protected $fillable = [
        'item_id',
        'quantity',
        'warehouse_id'
    ];

    public function item()
    {

        return $this->belongsTo(Item::class);
    }
    public function warehouse()
    {

        return $this->belongsTo(Warehouse::class);
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
