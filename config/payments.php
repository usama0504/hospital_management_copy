<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payment gateway
    |--------------------------------------------------------------------------
    | 'sandbox' = test mode: ek nakli checkout page, koi asli paisa nahi chalta.
    | Production (APP_ENV=production) mein sandbox khud band ho jata hai.
    | Asli gateway (JazzCash, Easypaisa...) ka adapter App\Services\Payments
    | mein alag class ke taur par add hota hai (PaymentGateway interface).
    */
    'driver' => env('PAYMENT_GATEWAY', 'sandbox'),

    'currency' => 'PKR',

    /*
    |--------------------------------------------------------------------------
    | Manual JazzCash payment
    |--------------------------------------------------------------------------
    | Patient hospital ke JazzCash number par paise bhejta hai aur Transaction ID
    | portal mein likhta hai; staff check karke bill "Paid" karta hai.
    | Number code mein nahi, sirf .env mein rakhein.
    */
    'manual_jazzcash' => [
        'enabled' => env('MANUAL_JAZZCASH_ENABLED', false),
        'number' => env('MANUAL_JAZZCASH_NUMBER'),
        'name' => env('MANUAL_JAZZCASH_NAME', 'Health Care Hospital'),
    ],
];
