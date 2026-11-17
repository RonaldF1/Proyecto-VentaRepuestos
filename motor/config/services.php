<?php

return [

    'gemini' => [
        'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        'key'   => env('GEMINI_API_KEY'),
    ],

    'openai' => [
        'key'   => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    ],

    'conector' => [
        'proveedor' => env('CONECTOR_PROVEEDOR', 'shippo'),
    ],

    'shippo' => [
        'key' => env('SHIPPO_API_KEY'),
        'url' => env('SHIPPO_API_URL', 'https://api.goshippo.com'),
    ],

    'woocommerce' => [
        'url'    => env('WOOCOMMERCE_URL'),
        'key'    => env('WOOCOMMERCE_KEY'),
        'secret' => env('WOOCOMMERCE_SECRET'),
    ],

    'webhook' => [
        'secret' => env('WC_WEBHOOK_SECRET'),
    ],

];
