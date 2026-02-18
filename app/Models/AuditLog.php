<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'actor',
        'action',
        'woo_order_id',
        'meta',
        'ip',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
