<?php
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

// Example expected fields (may vary slightly)
$transaction_id = $data['transaction_id'] ?? null;
$status = $data['status'] ?? null;
$provider_reference = $data['reference'] ?? null;

if ($transaction_id && $status) {
    $stmt = $pdo->prepare("UPDATE contributions 
        SET status = ?, provider_reference = ?
        WHERE transaction_id = ?");
    $stmt->execute([$status, $provider_reference, $transaction_id]);
}

http_response_code(200);
echo "Webhook received";
