<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    /** @use HasFactory<\Database\Factories\ReturnItemFactory> */
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
        'warehouse_id',
        'store_id',
        'price',
        'quantity',
        'total',
        'reference',
        'purchase_date',
        'return_date',
        'reason'
    ];

    public function user()
    {

        return $this->belongsTo(User::class);
    }

    public function store()
    {

        return $this->belongsTo(Store::class);
    }

    public function warehouse()
    {

        return $this->belongsTo(Warehouse::class);
    }

    public function item()
    {

        return $this->belongsTo(Item::class);
    }

    public function scopeSearch($query, $value)
    {
        return $query->where(function ($query) use ($value) {
            // Search in the current model's columns
            $query->where('reference', 'LIKE', '%' . $value . '%')
                ->orWhere('price', 'LIKE', '%' . $value . '%')
                ->orWhere('purchase_date', 'LIKE', '%' . $value . '%')
                ->orWhere('return_date', 'LIKE', '%' . $value . '%');
        })->orWhereHas('item', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%');
        });
    }

}
