<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessIncomingEvent;
use App\Models\IncomingEvent;
use Illuminate\Http\Request;

class WooWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // HMAC već proverava middleware: woo.webhook.hmac

        $raw = $request->getContent();
        $payload = $request->json()->all();

        // Topic / delivery (ako dođe iz Woo)
        $topic = (string) $request->header('X-WC-Webhook-Topic', data_get($payload, 'event_type', 'order.upsert'));
        $deliveryId = (string) $request->header('X-WC-Webhook-Delivery-ID', data_get($payload, 'event_id', ''));

        // external_id za debug/search
        $externalId =
            (string) data_get($payload, 'order.woo_order_id', '') ?:
            (string) data_get($payload, 'id', '') ?:
            (string) data_get($payload, 'order_number', '');

        // Dedupe key:
        // - Ako deliveryId postoji -> stabilno
        // - Inače fallback -> hash raw body (isti body == isti key)
        $dedupeKey = $deliveryId !== ''
            ? "wc:{$topic}:{$deliveryId}"
            : "wc:{$topic}:" . hash('sha256', $raw);

        $signature = (string) $request->attributes->get('woo_signature', '');

        $event = IncomingEvent::firstOrCreate(
            ['dedupe_key' => $dedupeKey],
            [
                'source' => 'woocommerce',
                'event_type' => $topic,
                'external_id' => $externalId !== '' ? $externalId : null,
                'payload' => $payload,
                'signature' => $signature,
                'status' => 'queued',
            ]
        );

        if (!$event->wasRecentlyCreated) {
            return response()->json(['ok' => true, 'deduped' => true]);
        }

        ProcessIncomingEvent::dispatch($event->id);

        return response()->json(['ok' => true]);
    }
}