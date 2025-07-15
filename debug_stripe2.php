<?php

// Test alternative approach using echo and pipe
$webhookUrl = 'http://localhost:8001/api/webhook/stripe';
$pythonScript = '/app/backend/stripe_integration.py';
$checkoutData = [
    'amount' => 9.99,
    'currency' => 'USD',
    'success_url' => 'http://localhost:8001/success',
    'cancel_url' => 'http://localhost:8001/cancel',
    'metadata' => ['test' => 'true']
];

$inputData = json_encode($checkoutData);
$escapedInput = escapeshellarg($inputData);
$command = "cd /app/backend && echo {$escapedInput} | python3 {$pythonScript} create_session '{$webhookUrl}'";

echo "Command: " . $command . "\n";
echo "Input data: " . $inputData . "\n";

$output = shell_exec($command);
echo "Output: " . ($output ?: 'NULL') . "\n";

if ($output) {
    $result = json_decode($output, true);
    echo "Decoded result: " . print_r($result, true) . "\n";
}