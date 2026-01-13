<?php
require 'db.php';

$name  = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$amount = $_POST['amount'];

$transaction_id = uniqid("ST_");

// Save initial record
$stmt = $pdo->prepare("INSERT INTO contributions 
(name, email, phone, amount, transaction_id) 
VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$name, $email, $phone, $amount, $transaction_id]);

$payload = [
    "name" => $name,
    "email" => $email,
    "phone" => $phone,
    "amount" => $amount,
    "transaction_id" => $transaction_id,
    "address" => "Dar es Salaam",
    "postcode" => "11111",
    "callback_url" => "http://localhost/webhook/webhook.php"
];

$ch = curl_init("https://palmpesa.drmlelwa.co.tz/api/palmpesa/initiate");

curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer YOUR_API_TOKEN",
        "Content-Type: application/json",
        "Accept: application/json"
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($ch);
curl_close($ch);

echo "<h3>📲 Payment Request Sent</h3>";
echo "<p>Please check your phone and approve the payment.</p>";
