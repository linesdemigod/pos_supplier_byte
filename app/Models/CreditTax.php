<?php

namespace App\Models;

use App\BelongsToStoreOrWarehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditTax extends Model
{
    /** @use HasFactory<\Database\Factories\CreditTaxFactory> */
    use HasFactory, BelongsToStoreOrWarehouse;


    protected $fillable = [
        'credit_id',
        'tax_rate',
        'tax_amount',
        'tax_name',
    ];

    public function credit()
    {

        return $this->belongsTo(Credit::class);
    }
}
