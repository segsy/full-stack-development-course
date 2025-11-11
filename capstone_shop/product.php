<?php
require_once 'functions.php';
require_once 'db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$p = $stmt->fetch();
if (!$p) { header('Location: index.php'); exit; }
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=htmlspecialchars($p['title'])?></title><link rel="stylesheet" href="assets/style.css"></head>
<body>
  <div class="container">
    <a href="index.php">← Back</a>
    <div class="product-detail">
      <img src="<?=htmlspecialchars($p['img'])?>" alt="">
      <div class="info">
        <h1><?=htmlspecialchars($p['title'])?></h1>
        <p class="price"><?=money($p['price'])?></p>
        <p><?=nl2br(htmlspecialchars($p['description'] ?? 'No description'))?></p>
        <div>
          <button class="btn add-to-cart" data-id="<?= $p['id'] ?>" data-price="<?= $p['price'] ?>">Add to cart</button>
          <span>Stock: <?= intval($p['stock']) ?></span>
        </div>
      </div>
    </div>
  </div>

<script>const API_CART = 'api/cart.php';</script>
<script src="assets/app.js"></script>
</body>
</html>
