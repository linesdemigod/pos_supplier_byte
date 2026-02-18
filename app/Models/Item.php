<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'store_id',
        'name',
        'price',
        'item_code',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function itemRequestDetails()
    {
        return $this->hasMany(ItemRequestDetail::class);
    }

    public function storeInventories()
    {

        return $this->hasOne(StoreInventory::class);
    }

    public function holdSaleItems()
    {

        return $this->hasMany(HoldSaleItem::class);
    }

    public function scopeSearch($query, $value, $Category)
    {

        $items = $query->where(function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%')
                ->orWhere('item_code', 'LIKE', '%' . $value . '%')
            ;
        });

        if ($Category !== '0') {

            return $items->where('category_id', $Category);
        }

        return $items;


    }


    public function scopeFilter($query, $value)
    {
        return $query->where(function ($query) use ($value) {
            // Search in the current model's columns
            $query->where('name', 'LIKE', '%' . $value . '%')
                ->orWhere('item_code', 'LIKE', '%' . $value . '%');
        })->orWhereHas('category', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%');
        });
    }
}
