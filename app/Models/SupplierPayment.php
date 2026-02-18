<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    protected $fillable = [
        'name',
        'store_id',
        'user_id',
        'supplier_purchase_id',
        'payment_method',
        'amount_paid',
        'status',
        'supplier_id',
        'reference'
    ];

    public function user()
    {

        return $this->belongsTo(User::class);
    }

    public function supplierPurchase()
    {

        return $this->belongsTo(supplierPurchase::class);
    }

    public function store()
    {

        return $this->belongsTo(Store::class);
    }
}
