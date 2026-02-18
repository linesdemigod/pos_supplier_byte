<?php

namespace App\Models;

use App\BelongsToStoreOrWarehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory, BelongsToStoreOrWarehouse;

    protected $fillable = [
        'name',
        'phone',
        'store_id',
        'user_id',
        'location'
    ];


    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeSearch($query, $value)
    {
        if (empty($value)) {
            return $query;
        }

        return $query->where(function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%')
                ->orWhere('phone', 'LIKE', '%' . $value . '%')
            ;
        });
    }
}
