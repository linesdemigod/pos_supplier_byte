<?php

namespace App\Models;

use App\BelongsToStoreOrWarehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    /** @use HasFactory<\Database\Factories\RepaymentFactory> */
    use HasFactory, BelongsToStoreOrWarehouse;

    protected $fillable = [
        'user_id',
        'customer_id',
        'date_paid',
        'amount_paid',
        'tenant_id',
        'credit_id',
        'shift_id',
        'payment_status',
        'reference',
        'payment_method',
        'customer_id'
    ];

    public function credit()
    {

        return $this->belongsTo(Credit::class);
    }

    public function customer()
    {

        return $this->belongsTo(Customer::class);
    }
    public function user()
    {

        return $this->belongsTo(User::class);
    }


    public function scopeSearch($query, $value)
    {

        return $query->where(function ($query) use ($value) {
            $query->where('date_paid', 'LIKE', '%' . $value . '%')
                ->orWhere('amount_paid', 'LIKE', '%' . $value . '%');
        });
    }
}
