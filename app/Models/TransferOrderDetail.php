<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferOrderDetail extends Model
{
    protected $fillable = [
        'transfer_order_id',
        'item_id',
        'quantity',
        'price',
        'total'
    ];

    public function transferorder()
    {
        return $this->belongsTo(TransferOrder::class, 'transfer_order_id', 'id');

    }

    public function item()
    {

        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
