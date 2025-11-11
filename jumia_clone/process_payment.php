<?php
require 'db.php';
require 'functions.php';

// ✅ Start session if not active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ✅ Load cart safely
$cart = $_SESSION['cart'] ?? [];

if (empty($cart) || !is_array($cart)) {
    echo "<div style='font-family:sans-serif;text-align:center;margin-top:40px'>
            <h3>⚠️ Your cart is empty.</h3>
            <p><a href='index.php'>Return to shop</a></p>
          </div>";
    exit;
}

// ✅ Calculate total
$total_amount = 0;
foreach ($cart as $item) {
    $total_amount += $item['price'] * $item['qty'];
}

// ✅ Insert order
$stmt = $pdo->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'pending')");
$stmt->execute([$_SESSION['user_id'], $total_amount]);
$order_id = $pdo->lastInsertId();

// ✅ Insert order items
$stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, title, price, qty, subtotal)
                           VALUES (?, ?, ?, ?, ?, ?)");

foreach ($cart as $item) {
    $subtotal = $item['price'] * $item['qty'];
    $stmtItem->execute([
        $order_id,
        $item['id'] ?? null,
        $item['title'] ?? 'Unknown Product',
        $item['price'],
        $item['qty'],
        $subtotal
    ]);
}

// ✅ Clear cart after successful order
unset($_SESSION['cart']);

// ✅ Show success message
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Successful</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body{font-family:Arial, sans-serif;background:#f8f9fa;text-align:center;padding:60px;}
    .success-box{background:#fff;padding:40px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);display:inline-block;}
    h2{color:#28a745;}
    a.btn{background:#007bff;color:#fff;padding:10px 20px;border-radius:4px;text-decoration:none;}
    a.btn:hover{background:#0056b3;}
  </style>
</head>
<body>
  <div class="success-box">
    <h2>✅ Payment Simulated Successfully!</h2>
    <p>Your order has been placed successfully.</p>
    <p><strong>Order ID:</strong> #<?= htmlspecialchars($order_id) ?></p>
    <p><strong>Total Amount:</strong> ₦<?= number_format($total_amount, 2) ?></p>
    <br>
    <a href="orders.php" class="btn">View Your Orders</a>
    <a href="index.php" class="btn" style="background:#28a745;">Continue Shopping</a>
  </div>
</body>
</html>
