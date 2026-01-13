<!DOCTYPE html>
<html>
<head>
    <title>Singles Talks – Contribution</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>💬 Singles Talks</h2>
    <p>Support our social group by contributing 💖</p>

    <form action="process_payment.php" method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="text" name="phone" placeholder="Phone (07XXXXXXXX)" required>
        <input type="number" name="amount" placeholder="Amount (TZS)" required>
        <button type="submit">Contribute Now</button>
    </form>
</div>

</body>
</html>
