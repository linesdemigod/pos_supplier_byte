<?php

namespace App\Models;

use App\BelongsToStoreOrWarehouse;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use BelongsToStoreOrWarehouse;

    protected $fillable = [
        'name',
        'store_id',
        'user_id',
        'contact_info',
        'address',
        'email'
    ];

    public function supplierPurchases()
    {
        return $this->hasMany(SupplierPurchase::class);
    }

    public function user()
    {
        return $this->belongsTo(user::class, 'user_id');
    }

    public function store()
    {

        return $this->belongsTo(Store::class);
    }

    public function payments()
    {

        return $this->hasMany(Payment::class);
    }

    public function scopeSearch($query, $value)
    {

        return $query->where('name', 'like', '%' . $value . '%')
            ->orWhere('contact_info', 'like', '%' . $value . '%')
            ->orWhere('address', 'like', '%' . $value . '%');

    }
}
