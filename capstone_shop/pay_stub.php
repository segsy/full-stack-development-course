<?php
require_once 'functions.php';
require_once 'db.php';
if (empty($_SESSION['user_id'])) { 
header('Location: login.php?next=checkout.php'); exit; }
$orderId = (int)($_GET['order_id'] ?? 0);
$msg = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    // simulate "processing" — you could add extra checks
    $pdo->prepare("UPDATE orders SET payment_status = 'paid' WHERE id = :id")->execute([':id'=>$orderId]);
    $msg = "Payment successful! Order #{$orderId} marked as paid.";
}
$order = $pdo->prepare("SELECT * FROM orders WHERE id=:id AND user_id=:uid LIMIT 1");
$order->execute([':id'=>$orderId,':uid'=>$_SESSION['user_id']]);
$o = $order->fetch();
if (!$o) { exit("Order not found."); }
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Pay (Demo)</title><link rel="stylesheet" href="assets/style.css"></head><body>
<div class="container">
  <h2>Simulated Payment for Order #<?= $o['id'] ?></h2>
  <?php if($msg): ?><p class="success"><?=htmlspecialchars($msg)?></p><?php else: ?>
    <form method="post">
      <?= csrf_field() ?>
      <label>Card number (demo)</label><input name="card" placeholder="4242 4242 4242 4242" required>
      <label>Name on card</label><input name="name" required>
      <button class="btn primary" type="submit">Pay (Demo)</button>
    </form>
  <?php endif; ?>
</div></body></html>
