<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    /** @use HasFactory<\Database\Factories\StoreFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'phone',
        'location'
    ];
    public function customers()
    {

        return $this->hasMany(Customer::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function itemRequest()
    {

        return $this->hasOne(ItemRequest::class);
    }
}

