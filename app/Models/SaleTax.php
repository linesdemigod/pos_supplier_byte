<?php

namespace App\Models;

use App\BelongsToStoreOrWarehouse;
use Illuminate\Database\Eloquent\Model;

class SaleTax extends Model
{
    use BelongsToStoreOrWarehouse;

    protected $fillable = [
        'sale_id',
        'store_id',
        'tax_rate',
        'tax_amount',
        'tax_name',
    ];

    public function store()
    {

        return $this->belongsTo(Store::class);
    }

    public function sale()
    {

        return $this->belongsTo(Sale::class);
    }
}
