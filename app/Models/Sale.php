<?php

namespace App\Models;

use App\BelongsToStoreOrWarehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    /** @use HasFactory<\Database\Factories\SaleFactory> */
    use HasFactory, BelongsToStoreOrWarehouse;

    protected $fillable = [
        'user_id',
        'customer_id',
        'store_id',
        'discount',
        'subtotal',
        'grandtotal',
        'daily_sale_id',
        'monthly_sale_id',
        'payment_method',
        'reference',
        'payment_status',
        'shift_id',
        'total_tax'
    ];

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(user::class, 'user_id');
    }


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }



    public function scopeSearch($query, $value)
    {

        return $query->where(function ($query) use ($value) {
            $query->where('reference', 'LIKE', '%' . $value . '%')
                ->orWhere('payment_method', 'LIKE', '%' . $value . '%')
                ->orWhere('created_at', 'LIKE', '%' . $value . '%');
        })->orWhereHas('customer', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%');
        })->orWhereHas('user', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%');
        });
    }

    public function scopeCustomerSearch($query, $value)
    {

        return $query->where(function ($query) use ($value) {
            $query->where('sales.id', 'LIKE', '%' . $value . '%');
        })->orWhereHas('customer', function ($query) use ($value) {
            $query->where('customers.name', 'LIKE', '%' . $value . '%');
        });
    }
}
