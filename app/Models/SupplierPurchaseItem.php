<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPurchaseItem extends Model
{
    protected $fillable = [
        'name',
        'supplier_purchase_id',
        'item_id',
        'quantity',
        'purchase_unit_type',
        'conversion_rate',
        'cost_price',
        'subtotal',
        'total_units_added'
    ];

    public function supplierPurchase()
    {
        return $this->belongsTo(SupplierPurchase::class, 'supplier_purchase_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function store()
    {

        return $this->belongsTo(Store::class);
    }
}
