<?php
require 'db.php';

// Enable error reporting (DEV ONLY)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Validate input early
if (!$_POST['name'] || !$_POST['phone'] || !$_POST['amount']) {
    die("Missing required fields");
}

$name  = trim($_POST['name']);
$email = trim($_POST['email']);
$phone = preg_replace('/\s+/', '', $_POST['phone']);
$amount = (int) $_POST['amount'];

$transaction_id = uniqid("ST_");

// Save initial record
$stmt = $pdo->prepare("
    INSERT INTO contributions 
    (name, email, phone, amount, transaction_id, status) 
    VALUES (?, ?, ?, ?, ?, 'INITIATED')
");
$stmt->execute([$name, $email, $phone, $amount, $transaction_id]);

$payload = [
    "name" => $name,
    "email" => $email,
    "phone" => $phone,
    "amount" => $amount,
    "transaction_id" => $transaction_id,
    "address" => "Dar es Salaam",
    "postcode" => "11111",
    "callback_url" => "https://tyisha-innovatory-ossie.ngrok-free.dev/webhook/webhook.php"
];

$ch = curl_init("https://palmpesa.drmlelwa.co.tz/api/palmpesa/initiate");

curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer UgGnf1bYJb1vC8MoZXa7LDXcWS6sA7mxWR12MaPgr05kDowvakyzP6jBLsbs",
        "Content-Type: application/json",
        "Accept: application/json"
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => false, // ignore SSL verification (TEST ONLY)
    CURLOPT_SSL_VERIFYHOST => false  // ignore SSL verification (TEST ONLY)
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    $error = curl_error($ch);
    file_put_contents("payment_error.log", $error.PHP_EOL, FILE_APPEND);
    die("Payment gateway error. Try again.");
}

curl_close($ch);

// Decode response
$result = json_decode($response, true);

// 🔍 SAVE RAW RESPONSE FOR DEBUGGING
file_put_contents(
    "payment_debug.log",
    date('Y-m-d H:i:s') . " | $transaction_id | $response" . PHP_EOL,
    FILE_APPEND
);

// Handle API response clearly
if ($httpCode !== 200 || empty($result)) {
    echo "<h3>❌ Payment Failed</h3>";
    echo "<pre>$response</pre>";
    exit;
}

// OPTIONAL: update status from API response
if (isset($result['status'])) {
    $stmt = $pdo->prepare("UPDATE contributions SET status=? WHERE transaction_id=?");
    $stmt->execute([$result['status'], $transaction_id]);
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Processing Payment</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="loader-box">
    <div class="spinner"></div>
    <h3>📲 Waiting for USSD Prompt</h3>
    <p>Please approve the payment on your phone</p>
    <small>Transaction ID: <?= $transaction_id ?></small>
</div>

</body>
</html>
