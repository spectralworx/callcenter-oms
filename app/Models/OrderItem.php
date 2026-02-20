<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
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

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}