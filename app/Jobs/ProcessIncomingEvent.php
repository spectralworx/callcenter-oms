<?php

namespace App\Jobs;

use App\Models\IncomingEvent;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessIncomingEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $incomingEventId) {}

    public function handle(): void
    {
        $event = IncomingEvent::findOrFail($this->incomingEventId);

        if ($event->status === 'processed') {
            return;
        }

        $payload = $event->payload;

        try {
            DB::transaction(function () use ($payload, $event) {
                $o = data_get($payload, 'order', []);
                $items = data_get($payload, 'items', null); // null znači "nije poslato", [] znači "poslato ali prazno"

                $wooOrderId = (string) data_get($o, 'woo_order_id', '');
                if ($wooOrderId === '') {
                    $wooOrderId = (string) data_get($payload, 'id', '');
                }
                if ($wooOrderId === '') {
                    throw new \RuntimeException('Missing WooCommerce Order ID');
                }

                $officeNotice = data_get($o, 'office_notice', null);
                $trackingNumbers = data_get($o, 'tracking_numbers', null);

                $update = [
                    'order_number' => (string) data_get($o, 'order_number'),
                    'first_name'   => (string) data_get($o, 'first_name', ''),
                    'last_name'    => (string) data_get($o, 'last_name', ''),
                    'phone'        => (string) data_get($o, 'phone'),
                    'email'        => (string) data_get($o, 'email'),
                    'address'      => (string) data_get($o, 'address'),
                    'city'         => (string) data_get($o, 'city'),
                    'postcode'     => (string) data_get($o, 'postcode'),
                    'total'        => (float) data_get($o, 'total', 0),
                    'tax_total'    => (float) data_get($o, 'tax_total', 0),
                    'currency'     => (string) data_get($o, 'currency', 'RSD'),
                    'status'       => (string) data_get($o, 'status', 'processing'),
                    'termal_code'  => (string) data_get($o, 'termal_code'),
                ];

                // tracking - upiši samo ako je poslato
                if ($trackingNumbers !== null) {
                    $update['tracking_numbers'] = $trackingNumbers;
                    $update['tracking_updated_at'] = now();
                }

                // office notice - upiši samo ako je poslato (ne overwrite null/prazno)
                if ($officeNotice !== null) {
                    $officeNoticeStr = trim((string) $officeNotice);
                    $update['office_notice'] = $officeNoticeStr !== '' ? $officeNoticeStr : null;
                    $update['office_notice_at'] = now();
                }

                $order = Order::updateOrCreate(
                    ['woo_order_id' => $wooOrderId],
                    $update
                );

                // Items sync - samo ako je "items" poslato u payload
                if (is_array($items)) {
                    OrderItem::where('order_id', $order->id)->delete();

                    foreach ($items as $it) {
                        OrderItem::create([
                            'order_id'   => $order->id,
                            'name'       => (string) data_get($it, 'name', 'Proizvod'),
                            'qty'        => (int) data_get($it, 'qty', 1),
                            'sku'        => (string) data_get($it, 'sku'),
                            'ean'        => (string) data_get($it, 'ean'),
                            'line_total' => (float) data_get($it, 'line_total', 0),
                            'line_tax'   => (float) data_get($it, 'line_tax', 0),
                        ]);
                    }
                }

                $event->markProcessed();
            });
        } catch (\Throwable $e) {
            $event->markFailed($e->getMessage());
            throw $e;
        }
    }
}