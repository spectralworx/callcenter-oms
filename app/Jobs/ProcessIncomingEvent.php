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

        // ako je neko već obradio, preskoči
        if ($event->status === 'processed') {
            return;
        }

        $payload = $event->payload;

        try {
            DB::transaction(function () use ($payload, $event) {

                $o = data_get($payload, 'order', []);
                $items = data_get($payload, 'items', []);

                $wooOrderId = (string) data_get($o, 'woo_order_id', '');
                if ($wooOrderId === '') {
                    throw new \RuntimeException('Missing order.woo_order_id');
                }

                $order = Order::updateOrCreate(
                    ['woo_order_id' => $wooOrderId],
                    [
                        'order_number' => (string) data_get($o, 'order_number', null),

                        'first_name' => (string) data_get($o, 'first_name', ''),
                        'last_name'  => (string) data_get($o, 'last_name', ''),
                        'phone'      => (string) data_get($o, 'phone', null),
                        'email'      => (string) data_get($o, 'email', null),

                        'address'  => (string) data_get($o, 'address', null),
                        'city'     => (string) data_get($o, 'city', null),
                        'postcode' => (string) data_get($o, 'postcode', null),

                        'total'    => (float) data_get($o, 'total', 0),
                        'tax_total'=> (float) data_get($o, 'tax_total', 0),
                        'currency' => (string) data_get($o, 'currency', 'RSD'),

                        'status' => (string) data_get($o, 'status', 'processing'),

                        'termal_code' => (string) data_get($o, 'termal_code', null),

                        'tracking_numbers' => data_get($o, 'tracking_numbers', null),
                        'tracking_updated_at' => data_get($o, 'tracking_numbers') ? now() : null,

                        'office_notice' => (string) data_get($o, 'office_notice', null),
                        'office_notice_at' => data_get($o, 'office_notice') ? now() : null,
                    ]
                );

                // osveži items: najjednostavnije je da obrišemo pa napravimo ponovo
                OrderItem::where('order_id', $order->id)->delete();

                foreach ($items as $it) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'name' => (string) data_get($it, 'name', 'Proizvod'),
                        'qty'  => (int) data_get($it, 'qty', 1),
                        'sku'  => (string) data_get($it, 'sku', null),
                        'ean'  => (string) data_get($it, 'ean', null),
                        'line_total' => (float) data_get($it, 'line_total', 0),
                        'line_tax'   => (float) data_get($it, 'line_tax', 0),
                    ]);
                }

                $event->markProcessed();
            });
        } catch (\Throwable $e) {
            $event->markFailed($e->getMessage());
            throw $e; // da queue retry radi
        }
    }
}
