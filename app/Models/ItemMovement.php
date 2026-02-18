<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMovement extends Model
{
    /** @use HasFactory<\Database\Factories\ItemMovementFactory> */
    use HasFactory;

    protected $fillable = [
        'item_id',
        'store_id',
        'warehouse_id',
        'movement_type',
        'quantity'
    ];
}
