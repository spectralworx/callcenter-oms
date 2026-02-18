<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IncomingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'source',
        'event_type',
        'external_id',
        'dedupe_key',
        'payload',
        'signature',
        'status',
        'error',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function markProcessed(): void
    {
        $this->update([
            'status' => 'processed',
            'error' => null,
        ]);
    }

    public function markFailed(string $message): void
    {
        $this->update([
            'status' => 'failed',
            'error' => $message,
        ]);
    }
}
