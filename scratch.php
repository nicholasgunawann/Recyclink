<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = \Illuminate\Http\Request::create('/webhook/dompetx', 'POST', [
    'reference' => 'RL-202607-000008_attempt_1784136750',
    'status' => 'PAID'
]);

$controller = app(\App\Http\Controllers\Api\DompetxWebhookController::class);
$response = $controller->handle($request);
echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";
