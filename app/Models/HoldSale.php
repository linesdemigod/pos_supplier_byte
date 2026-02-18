<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoldSale extends Model
{
    /** @use HasFactory<\Database\Factories\HoldSaleFactory> */
    use HasFactory;

    protected $fillable = [
        'id',
        'total',
        'store_id',
    ];


    public function holdSaleItems()
    {
        return $this->hasMany(HoldSaleItem::class);
    }
}
