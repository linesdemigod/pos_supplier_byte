<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreRequestDetail extends Model
{

    protected $fillable = [
        'store_request_id',
        'item_id',
        'requested_quantity'
    ];

    public function storeRequest()
    {

        return $this->belongsTo(StoreRequest::class);
    }

    public function item()
    {

        return $this->belongsTo(Item::class);
    }
}
