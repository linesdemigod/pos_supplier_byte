<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoldSaleItem extends Model
{
    /** @use HasFactory<\Database\Factories\HoldSaleItemFactory> */
    use HasFactory;

    protected $fillable = [
        'hold_sale_id',
        'item_id',
        'quantity',
        'rate',
        'subtotal',
    ];


    public function holdSale()
    {
        return $this->belongsTo(HoldSale::class);
    }

    public function item()
    {

        return $this->belongsTo(Item::class);
    }
}
