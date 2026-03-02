<?php

namespace App\Models;

use App\BelongsToStoreOrWarehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    /** @use HasFactory<\Database\Factories\CreditFactory> */
    use HasFactory, BelongsToStoreOrWarehouse;

    protected $fillable = [
        'user_id',
        'customer_id',
        'store_id',
        'subtotal',
        'total_amount',
        'discount',
        'total_tax_amount',
        'reference',
        'status'
    ];

    public function user()
    {

        return $this->belongsTo(User::class);
    }

    public function customer()
    {

        return $this->belongsTo(Customer::class);
    }

    public function repayments()
    {

        return $this->hasMany(Repayment::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function creditTaxes()
    {

        return $this->hasMany(CreditTax::class);
    }

    public function creditItems()
    {

        return $this->hasMany(CreditItem::class);
    }




    public function scopeSearch($query, $value)
    {

        return $query->where(function ($query) use ($value) {
            $query->where('created_at', 'LIKE', '%' . $value . '%');
        })->orWhereHas('customer', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%');
        });
    }

    public function scopeDate($query, $value)
    {

        return $query->where(function ($query) use ($value) {
            $query->where('created_at', 'LIKE', '%' . $value . '%');
        });
    }
}
