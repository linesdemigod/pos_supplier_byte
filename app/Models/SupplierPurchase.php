<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPurchase extends Model
{
    protected $fillable = [
        'name',
        'store_id',
        'user_id',
        'supplier_id',
        'status',
        'total_amount',
        'reference'
    ];

    public function user()
    {
        return $this->belongsTo(user::class, 'user_id');
    }

    public function store()
    {

        return $this->belongsTo(Store::class);
    }

    public function supplier()
    {

        return $this->belongsTo(Supplier::class);
    }


    public function supplierPurchaseItems()
    {

        return $this->hasMany(SupplierPurchaseItem::class, 'supplier_purchase_id');
    }

    public function supplierPayments()
    {

        return $this->hasMany(SupplierPayment::class);
    }

    public function scopeSearch($query, $value)
    {

        return $query->where('reference', 'like', '%' . $value . '%')
            ->orWhere('total_amount', 'like', '%' . $value . '%')
            ->orWhere('status', 'like', '%' . $value . '%');

    }
}
