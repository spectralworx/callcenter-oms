<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WooCommerce
    |--------------------------------------------------------------------------
    |
    | base_url     → https://tvojshop.rs/wp-json/wc/v3
    | consumer_key → ck_xxxxxxxxxxxxxxxxxxxx
    | consumer_secret → cs_xxxxxxxxxxxxxxxxxxxx
    |
    | Ključeve generišeš u: WooCommerce → Settings → Advanced → REST API
    | Permissions: Read/Write
    |
    */
    'woocommerce' => [
        'webhook_secret'  => env('WOOCOMMERCE_WEBHOOK_SECRET'),
        'base_url'        => env('WOOCOMMERCE_BASE_URL'),        // https://tvojshop.rs/wp-json/wc/v3
        'consumer_key'    => env('WOOCOMMERCE_CONSUMER_KEY'),    // ck_...
        'consumer_secret' => env('WOOCOMMERCE_CONSUMER_SECRET'), // cs_...
        'timeout'         => env('WOOCOMMERCE_TIMEOUT', 10),     // sekunde
    ],

];
