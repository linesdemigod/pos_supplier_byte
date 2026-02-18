<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    /** @use HasFactory<\Database\Factories\PriceHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'old_price',
        'new_price'
    ];

    public function item()
    {

        return $this->belongsTo(Item::class);
    }

    public function user()
    {

        return $this->belongsTo(User::class);
    }

    public function scopeSearch($query, $value)
    {
        return $query->where(function ($query) use ($value) {
            $query->where('old_price', 'LIKE', '%' . $value . '%')
                ->orWhere('old_price', 'LIKE', '%' . $value . '%');
        })->orWhereHas('item', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%')
                ->orWhere('item_code', 'LIKE', '%' . $value . '%');
        })->orWhereHas('user', function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%');
        });

    }
}
