<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWooWebhookHmac
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = env('WOOCOMMERCE_WEBHOOK_SECRET');

        if (!is_string($secret) || $secret === '') {
            return response('Webhook secret missing', 500);
        }

        $raw = $request->getContent();
        $provided = (string) $request->header('X-WC-Signature', '');

        if ($provided === '') {
            return response('Missing signature', 401);
        }

        // base64(hmac_sha256(raw_body, secret))
        $computed = base64_encode(hash_hmac('sha256', $raw, $secret, true));

        if (!hash_equals($computed, $provided)) {
            return response('Invalid signature', 401);
        }

        $request->attributes->set('woo_signature', $provided);

        return $next($request);
    }
}