<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferOrder extends Model
{
    protected $fillable = [
        'store_request_id',
        'store_id',
        'warehouse_id',
        'user_id',
        'order_number',
        'status',
        'dispatched_by',
        'accepted_by'
    ];

    public function dispatchedtedBy()
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }


    public function acceptedBy()
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function transferOrderDetails()
    {

        return $this->hasMany(TransferOrderDetail::class);
    }

    public function storeRequest()
    {

        return $this->belongsTo(StoreRequest::class, 'store_request_id');
    }

    public function store()
    {

        return $this->belongsTo(Store::class);
    }

    public function warehouse()
    {

        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {

        return $this->belongsTo(User::class);
    }

    public function scopeSearch($query, $value)
    {

        return $query->where(function ($query) use ($value) {
            $query->where('order_number', 'LIKE', '%' . $value . '%')
                ->orWhere('status', 'LIKE', '%' . $value . '%')
                ->orWhere('created_at', 'LIKE', '%' . $value . '%');

        })->orWhereHas('store', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%');
        })->orWhereHas('user', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%');
        })->orWhereHas('storeRequest', function ($query) use ($value) {
            $query->where('reference', 'LIKE', '%' . $value . '%');
        });
        ;
    }
}
