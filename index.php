<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Singles Talks • Contribution</title>
  <style>
    :root {
      --primary: #c41e3b;
      --primary-dark: #a01831;
      --light: #fff8f9;
      --gray: #555;
      --light-gray: #eee;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: system-ui, -apple-system, sans-serif;
      background: linear-gradient(to bottom, #fdf1f4, #f8e1e5);
      color: #333;
      min-height: 100vh;
      padding: 20px 12px;
      line-height: 1.5;
    }

    .container {
      max-width: 480px;
      margin: 0 auto;
    }

    header {
      text-align: center;
      margin: 20px 0 40px;
    }

    h1 {
      color: var(--primary);
      font-size: 2.1rem;
      margin-bottom: 8px;
    }

    .tagline {
      color: var(--gray);
      font-size: 1.05rem;
    }

    .card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 6px 24px rgba(0,0,0,0.11);
      padding: 28px 24px;
      margin-bottom: 24px;
    }

    .amount-options {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin: 24px 0 32px;
    }

    .amount-btn {
      padding: 16px 8px;
      border: 2px solid #ddd;
      border-radius: 12px;
      background: #fafafa;
      font-size: 1.15rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.18s;
      text-align: center;
    }

    .amount-btn:hover {
      border-color: var(--primary);
      background: #fff5f7;
    }

    .amount-btn.active {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
      box-shadow: 0 3px 12px rgba(196,30,59,0.25);
    }

    .custom-amount {
      margin: 16px 0 32px;
    }

    .custom-amount label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #444;
    }

    .custom-amount input {
      width: 100%;
      padding: 14px 16px;
      border: 2px solid #ddd;
      border-radius: 10px;
      font-size: 1.25rem;
      font-weight: 600;
      text-align: center;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    label {
      font-weight: 600;
      color: #444;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"] {
      padding: 14px 16px;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      font-size: 1rem;
    }

    input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(196,30,59,0.15);
    }

    button[type="submit"] {
      background: var(--primary);
      color: white;
      border: none;
      padding: 18px;
      font-size: 1.18rem;
      font-weight: 600;
      border-radius: 12px;
      cursor: pointer;
      margin-top: 12px;
      transition: all 0.2s;
    }

    button[type="submit"]:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
    }

    button[type="submit"]:disabled {
      background: #aaa;
      cursor: not-allowed;
    }

    .message {
      padding: 16px;
      border-radius: 10px;
      margin: 20px 0;
      text-align: center;
      font-weight: 500;
    }

    .success  { background: #e6ffed; color: #006d2e; }
    .error    { background: #ffebee; color: #c62828; }
    .info     { background: #e3f2fd; color: #1565c0; }

    .loading {
      display: none;
      text-align: center;
      margin: 30px 0;
      color: var(--primary);
      font-weight: 600;
    }

    .spinner {
      width: 28px;
      height: 28px;
      border: 4px solid #ffb3c1;
      border-top: 4px solid var(--primary);
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin: 0 auto 12px;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    footer {
      text-align: center;
      margin-top: 40px;
      color: #777;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>

<div class="container">

  <header>
    <h1>Singles Talks</h1>
    <div class="tagline">Mchango kwa wiki / mwezi</div>
  </header>

  <div class="card">

    <?php
    $message = '';
    $message_type = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $name      = trim($_POST['name'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $phone     = trim($_POST['phone'] ?? '');
        $amount    = (int)($_POST['amount'] ?? 0);
        $address   = trim($_POST['address'] ?? 'Dar es Salaam');
        $postcode  = trim($_POST['postcode'] ?? '11111');

        // Very basic validation
        $errors = [];
        if (strlen($name) < 3)               $errors[] = "Jina kamili inahitajika";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Barua pepe si sahihi";
        if (!preg_match('/^0[67][1-9]\d{7}$/', $phone)) $errors[] = "Namba ya simu si sahihi (e.g. 0693662424)";
        if ($amount < 500)                   $errors[] = "Kiasi kidogo sana (min 500 TZS)";

        if ($errors) {
            $message = implode("<br>• ", $errors);
            $message = "• " . $message;
            $message_type = 'error';
        } else {
            // Prepare payload
            $transaction_id = 'ST-' . date('ymdHis') . '-' . mt_rand(100,999);

            $payload = [
                "name"           => $name,
                "email"          => $email,
                "phone"          => $phone,
                "amount"         => $amount,
                "transaction_id" => $transaction_id,
                "address"        => $address,
                "postcode"       => $postcode,
                "callback_url"   => "https://your-domain.com/callback.php"   // ← CHANGE THIS
            ];

            $json_payload = json_encode($payload);

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => "https://palmpesa.drmlelwa.co.tz/api/palmpesa/initiate",
                CURLOPT_POST           => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 45,
                CURLOPT_HTTPHEADER     => [
                    "Authorization: Bearer YOUR_API_TOKEN_HERE",          // ← CHANGE THIS
                    "Content-Type: application/json",
                    "Accept: application/json"
                ],
                CURLOPT_POSTFIELDS     => $json_payload,
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($curl_error) {
                $message = "Tatizo la muunganisho: " . $curl_error;
                $message_type = 'error';
            } elseif ($http_code >= 200 && $http_code < 300) {
                $result = json_decode($response, true);

                if (isset($result['success']) && $result['success']) {
                    $message = "Ombi la malipo limetumwa!<br>Tafadhali angalia simu yako kwa namba <b>$phone</b> na uidhinishe malipo ya <b>{$amount} TZS</b>.";
                    $message_type = 'success';
                } else {
                    $msg = $result['message'] ?? 'Jibu halikueleweka';
                    $message = "Malipo hayakufanikiwa: " . $msg;
                    $message_type = 'error';
                }
            } else {
                $message = "Server ilijibu na makosa ($http_code)<br>" . htmlspecialchars(substr($response,0,300));
                $message_type = 'error';
            }
        }
    }
    ?>

    <?php if ($message): ?>
      <div class="message <?= $message_type ?>">
        <?= $message ?>
      </div>
    <?php endif; ?>

    <form method="post" id="contribForm">

      <div class="amount-options">
        <button type="button" class="amount-btn" data-value="1000">1,000 TZS</button>
        <button type="button" class="amount-btn" data-value="2000">2,000 TZS</button>
        <button type="button" class="amount-btn" data-value="5000">5,000 TZS</button>
      </div>

      <div class="custom-amount">
        <label for="amount">Kiasi kingine (TZS):</label>
        <input type="number" name="amount" id="amount" min="500" placeholder="e.g. 3000" required>
      </div>

      <div class="form-group">
        <label for="name">Jina Kamili</label>
        <input type="text" name="name" id="name" required placeholder="e.g. Sara John">
      </div>

      <div class="form-group">
        <label for="phone">Namba ya Simu (M-Pesa/Tigo-Pesa/Airtel)</label>
        <input type="tel" name="phone" id="phone" required placeholder="e.g. 0693662424" pattern="0[67][1-9][0-9]{7}">
      </div>

      <div class="form-group">
        <label for="email">Barua Pepe</label>
        <input type="email" name="email" id="email" required placeholder="example@email.com">
      </div>

      <button type="submit" id="submitBtn">Tuma Mchango</button>

      <div class="loading" id="loading">
        <div class="spinner"></div>
        Inatuma ombi la malipo...
      </div>

    </form>

  </div>

  <footer>
    Singles Talks © <?= date("Y") ?> • Malipo kwa PalmPesa
  </footer>

</div>

<script>
  const amountInput = document.getElementById('amount');
  const amountButtons = document.querySelectorAll('.amount-btn');
  const submitBtn = document.getElementById('submitBtn');
  const loading = document.getElementById('loading');
  const form = document.getElementById('contribForm');

  amountButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      amountButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      amountInput.value = btn.dataset.value;
    });
  });

  // Auto-select first if no custom value
  if (!amountInput.value) {
    amountButtons[0].click();
  }

  form.addEventListener('submit', () => {
    submitBtn.disabled = true;
    loading.style.display = 'block';
  });
</script>

</body>
</html>
