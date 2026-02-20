<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WooCommerceClient
{
    /**
     * Proverava da li su Woo API kredencijali konfigurisani u .env / config.
     */
    public function isConfigured(): bool
    {
        $baseUrl = (string) config('services.woocommerce.base_url', '');
        $ck      = (string) config('services.woocommerce.consumer_key', '');
        $cs      = (string) config('services.woocommerce.consumer_secret', '');

        return $baseUrl !== '' && $ck !== '' && $cs !== '';
    }

    /**
     * Šalje PUT /wp-json/wc/v3/orders/{id} i menja status.
     *
     * @return array Woo API response
     * @throws \RuntimeException ako API nije konfigurisan ili request ne uspe
     */
    public function updateOrderStatus(string $wooOrderId, string $status): array
    {
        $baseUrl = rtrim((string) config('services.woocommerce.base_url'), '/');
        $ck      = (string) config('services.woocommerce.consumer_key');
        $cs      = (string) config('services.woocommerce.consumer_secret');
        $timeout = (int)    config('services.woocommerce.timeout', 10);

        if ($baseUrl === '' || $ck === '' || $cs === '') {
            throw new \RuntimeException('WooCommerce API config missing (base_url / consumer_key / consumer_secret).');
        }

        $url = "{$baseUrl}/orders/{$wooOrderId}";

        $resp = Http::timeout($timeout)
            ->acceptJson()
            ->asJson()
            ->withBasicAuth($ck, $cs)
            ->put($url, ['status' => $status]);

        if (! $resp->successful()) {
            $body = $resp->json();
            $msg  = is_array($body) ? (string) data_get($body, 'message', '') : '';
            $code = (string) $resp->status();

            throw new \RuntimeException("Woo API update failed [{$code}]: {$msg}");
        }

        return (array) $resp->json();
    }
}
