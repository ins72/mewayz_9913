<?php

// Test the exact flow that Laravel controller is doing
$packageId = 'starter';
$packages = [
    'starter' => ['amount' => 9.99, 'currency' => 'USD', 'name' => 'Starter Package'],
    'professional' => ['amount' => 29.99, 'currency' => 'USD', 'name' => 'Professional Package'],
    'enterprise' => ['amount' => 99.99, 'currency' => 'USD', 'name' => 'Enterprise Package'],
];

if (!isset($packages[$packageId])) {
    echo "Invalid package\n";
    exit;
}

$package = $packages[$packageId];
$checkoutData = [
    'amount' => $package['amount'],
    'currency' => $package['currency'],
    'success_url' => 'http://localhost:8001/success',
    'cancel_url' => 'http://localhost:8001/cancel',
    'metadata' => [
        'user_id' => null,
        'email' => null,
        'source' => 'laravel_api'
    ]
];

$webhookUrl = 'http://localhost:8001/api/webhook/stripe';
$pythonScript = '/app/backend/stripe_integration.py';
$inputData = json_encode($checkoutData);

echo "Checkout data: " . $inputData . "\n";

$escapedInput = escapeshellarg($inputData);
$command = "cd /app/backend && echo {$escapedInput} | python3 {$pythonScript} create_session '{$webhookUrl}'";

echo "Command: " . $command . "\n";

$output = shell_exec($command);
echo "Raw output: " . ($output ?: 'NULL') . "\n";

if (!$output) {
    echo "No output from Python script\n";
    exit;
}

$result = json_decode($output, true);
echo "Decoded result: " . print_r($result, true) . "\n";

if (!$result || !isset($result['success'])) {
    echo "Invalid JSON response\n";
    exit;
}

if (!$result['success']) {
    echo "Python script returned error: " . $result['error'] . "\n";
    exit;
}

echo "Success! Session ID: " . $result['session_id'] . "\n";
echo "Checkout URL: " . $result['url'] . "\n";