<?php
$apiKey = 'dompk_8ab188aa00331c0a87c6ef69660e9ed137908fd3f336720cfdf42e64acf4e6df';
$apiUrl = 'https://api.dompetx.com/v1/payments';

$payload = [
    'amount' => 50000,
    'currency' => 'IDR',
    'reference' => 'ORDER-' . time(),
    'method' => 'BCA',
    'metadata' => [
        'order_name' => 'Test Pesanan',
        'customer_name' => 'Budi Santoso',
    ]
];

$body = json_encode($payload);
$timestamp = (string) time();
$ks = $timestamp . '.' . $body;
$signature = hash_hmac('sha256', $ks, $apiKey);

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-DOMPAY-API-Key: ' . $apiKey,
    'X-DOMPAY-Signature: ' . $signature,
    'X-DOMPAY-Timestamp: ' . $timestamp,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";
