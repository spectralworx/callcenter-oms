<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessIncomingEvent;
use App\Models\IncomingEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class WooWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1) Sigurnost: HMAC potpis (naš custom header)
        $secret = config('services.woocommerce.webhook_secret');

        if (!$secret) {
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        $signature = (string) $request->header('X-CC-Signature', '');
        $raw = $request->getContent();

        $expected = hash_hmac('sha256', $raw, $secret);

        // constant-time compare
        if (!$signature || !hash_equals($expected, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $payload = $request->json()->all();

        // 2) Minimalni podaci
        $eventType  = (string) data_get($payload, 'event_type', 'order.upsert');
        $externalId = (string) data_get($payload, 'order.woo_order_id', data_get($payload, 'order_number', ''));

        // 3) Dedupe ključ (da Woo retry ne napravi duplikat)
        // Ako WP šalje event_id -> koristi njega, inače hash body
        $eventId = (string) data_get($payload, 'event_id', '');
        $dedupeKey = $eventId !== ''
            ? "woo:{$eventType}:{$eventId}"
            : "woo:{$eventType}:" . hash('sha256', $raw);

        // 4) Upis incoming event (idempotent)
        $event = IncomingEvent::firstOrCreate(
            ['dedupe_key' => $dedupeKey],
            [
                'source' => 'woocommerce',
                'event_type' => $eventType,
                'external_id' => $externalId ?: null,
                'payload' => $payload,
                'signature' => $signature,
                'status' => 'queued',
            ]
        );

        // Ako je već postojao, ne šalji opet job
        if (!$event->wasRecentlyCreated) {
            return response()->json(['ok' => true, 'deduped' => true]);
        }

        // 5) Asinhrono procesiranje
        ProcessIncomingEvent::dispatch($event->id);

        return response()->json(['ok' => true]);
    }
}
