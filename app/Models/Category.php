<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'category_code',
        'name',
        'description'
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function scopeSearch($query, $value)
    {
        return $query->where(function ($query) use ($value) {
            // Search in the current model's columns
            $query->where('name', 'LIKE', '%' . $value . '%')
                ->orWhere('category_code', 'LIKE', '%' . $value . '%');
        });
    }
}
