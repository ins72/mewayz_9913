<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "STRIPE_API_KEY: " . env('STRIPE_API_KEY') . PHP_EOL;
echo "STRIPE_KEY: " . env('STRIPE_KEY') . PHP_EOL;
echo "STRIPE_SECRET: " . env('STRIPE_SECRET') . PHP_EOL;