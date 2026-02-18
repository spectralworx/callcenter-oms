<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

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
        'total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'is_printed' => 'boolean',
        'printed_at' => 'datetime',
        'tracking_numbers' => 'array',
        'tracking_updated_at' => 'datetime',
        'office_notice_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function markAsPrinted(): void
    {
        $this->update([
            'is_printed' => true,
            'printed_at' => now(),
        ]);
    }

    public function hasTracking(): bool
    {
        return !empty($this->tracking_numbers);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
