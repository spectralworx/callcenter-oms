<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WooCommerceClient
{
    public function updateOrderStatus(string $wooOrderId, string $status): array
    {
        $baseUrl = rtrim((string) config('services.woocommerce.base_url'), '/');
        $ck = (string) config('services.woocommerce.consumer_key');
        $cs = (string) config('services.woocommerce.consumer_secret');
        $timeout = (int) config('services.woocommerce.timeout', 10);

        if ($baseUrl === '' || $ck === '' || $cs === '') {
            throw new \RuntimeException('WooCommerce API config missing (base_url/ck/cs).');
        }

        // PUT /orders/{id}
        $url = "{$baseUrl}/orders/{$wooOrderId}";

        $resp = Http::timeout($timeout)
            ->acceptJson()
            ->asJson()
            // Basic auth (Woo REST API podržava)
            ->withBasicAuth($ck, $cs)
            ->put($url, [
                'status' => $status,
            ]);

        if (! $resp->successful()) {
            $body = $resp->json();
            $msg = is_array($body) ? (string) data_get($body, 'message', '') : '';
            $code = (string) ($resp->status());

            throw new \RuntimeException("Woo update failed [{$code}] {$msg}");
        }

        return (array) $resp->json();
    }
}