<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class StoreRequest extends Model
{
    protected $fillable = [
        'requested_by',
        'approved_by',
        'store_id',
        'warehouse_id',
        'requested_date',
        'approval_date',
        'status',
        'reference'
    ];

    public function storeRequestDetails()
    {

        return $this->hasMany(StoreRequestDetail::class, 'store_request_id', 'id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }


    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
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


        return $query->where(function ($query) use ($value) {
            $query->where('reference', 'LIKE', '%' . $value . '%')
                ->orWhere('status', 'LIKE', '%' . $value . '%')
                ->orWhere('created_at', 'LIKE', '%' . $value . '%');

        })->orWhereHas('store', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%');
        })->orWhereHas('warehouse', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%');
        });


    }


}
