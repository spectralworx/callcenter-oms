<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'name',
        'qty',
        'sku',
        'ean',
        'line_total',
        'line_tax',
    ];

    protected $casts = [
        'qty' => 'integer',
        'line_total' => 'decimal:2',
        'line_tax' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getLineTotalWithTaxAttribute()
    {
        return (float) $this->line_total + (float) $this->line_tax;
    }
}
