<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRequest extends Model
{
    /** @use HasFactory<\Database\Factories\ItemRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_id',
        'warehouse_id',
        'total',// total price
        'status',
        'reference'
    ];

    public function itemRequestDetails()
    {

        return $this->hasMany(ItemRequestDetail::class, 'item_request_id', 'id');
    }

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

    public function scopeSearch($query, $value)
    {

        $items = $query->where(function ($query) use ($value) {
            $query->where('reference', 'LIKE', '%' . $value . '%')
                ->orWhere('status', 'LIKE', '%' . $value . '%')
                ->orWhere('created_at', 'LIKE', '%' . $value . '%');

        })->orWhereHas('store', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%');
        })->orWhereHas('warehouse', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%');
        });
        ;

        // return $this->where('status', '!=', 'cancelled');


    }
}
