<?php

return [
    'live_public_key' => env('STRIPE_LIVE_PUBLIC_KEY', ''),
    'live_secret_key' => env('STRIPE_LIVE_SECRET_KEY', ''),
    'test_public_key' => env('STRIPE_TEST_PUBLIC_KEY', ''),
    'test_secret_key' => env('STRIPE_TEST_SECRET_KEY', ''),
    'test_account' => env('STRIPE_TEST_ACCOUNT', ''),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),
    'client_id' => env('STRIPE_CLIENT_ID', ''),
];
