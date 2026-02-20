<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'woo_order_id',
        'order_number',
        'first_name',
        'last_name',
        'phone',
        'email',
        'address',
        'city',
        'postcode',
        'total',
        'tax_total',
        'currency',
        'status',
        'termal_code',
        'is_printed',
        'printed_at',
        'tracking_numbers',
        'tracking_updated_at',
        'office_notice',
        'office_notice_at',
    ];

    protected $casts = [
        'is_printed' => 'boolean',
        'printed_at' => 'datetime',
        'tracking_numbers' => 'array',
        'tracking_updated_at' => 'datetime',
        'office_notice_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getCustomerNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}