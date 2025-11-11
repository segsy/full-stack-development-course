<?php
require_once 'functions.php';
require_once 'db.php';
if (empty($_SESSION['user_id'])) { header('Location: login.php?next=order_history.php'); exit; }
$uid = $_SESSION['user_id'];

$orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC");
$orders->execute([':uid'=>$uid]);
$orders = $orders->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container"><div style="max-width:900px;margin:30px auto">
  <h2>Your Orders</h2>
  <?php if(empty($orders)): ?><p>No orders yet.</p><?php else: ?>
    <?php foreach($orders as $o): ?>
      <div style="background:#fff;padding:12px;border-radius:8px;margin-bottom:12px">
        <h3>Order #<?= $o['id'] ?> — <?=htmlspecialchars($o['status'])?> — <?= $o['created_at'] ?></h3>
        <?php
           $items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :oid");
           $items->execute([':oid'=>$o['id']]);
           $items = $items->fetchAll();
        ?>
        <ul>
          <?php foreach($items as $it): ?>
            <li><?=htmlspecialchars($it['title'])?> — <?=intval($it['qty'])?> x <?=money($it['price'])?> = <?=money($it['subtotal'])?></li>
          <?php endforeach; ?>
        </ul>
        <p><strong>Total: <?=money($o['total'])?></strong></p>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div></div>
</body></html>
