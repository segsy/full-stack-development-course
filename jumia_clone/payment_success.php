<?php
// payment_success.php
require_once 'functions.php';
require_once 'db.php';
$ref = $_GET['ref'] ?? 'N/A';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment Success</title>
  <style>
    body { font-family: Arial; text-align: center; margin-top: 50px; }
  </style>
</head>
<body>
  <h2>Payment Successful 🎉</h2>
  <p>Transaction Reference: <strong><?= htmlspecialchars($ref) ?></strong></p>
  <p>Thank you for shopping with Jumia Clone!</p>
  <a href="orders.php">View Your Orders</a>
</body>
</html>
