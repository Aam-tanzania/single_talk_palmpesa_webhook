<?php
require 'db.php';

$payload = file_get_contents("php://input");

// Log everything FIRST
file_put_contents(
    "webhook_debug.log",
    date('Y-m-d H:i:s') . " | " . $payload . PHP_EOL,
    FILE_APPEND
);

$data = json_decode($payload, true);

if (!isset($data['order_id'], $data['payment_status'])) {
    http_response_code(200);
    exit;
}

$orderId = $data['order_id'];
$status  = $data['payment_status'];

// Update DB
$stmt = $pdo->prepare("
    UPDATE contributions 
    SET status = ?
    WHERE transaction_id = ?
");
$stmt->execute([$status, $orderId]);

http_response_code(200);
echo "OK";
