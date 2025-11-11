<?php
require_once 'functions.php';
require_once 'db.php';

$loggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';

// fetch products
$stmt = $pdo->query("SELECT id, sku, title, price, img, stock FROM products ORDER BY created_at DESC LIMIT 50");
$products = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ShopCart - DB Products</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <div class="logo">Jumia<span>Shop</span></div>
    <div class="search">
      <input id="searchInput" placeholder="Search products, brands and categories" />
      <button id="searchBtn">Search</button>
    </div>
    <div class="header-actions">
      <?php if($loggedIn): ?>
        <span>Hi, <?=htmlspecialchars($userName)?></span> |
        <a href="order_history.php">Orders</a> |
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a> |
        <a href="register.php">Register</a>
      <?php endif; ?>
      <a href="view-cart.php" class="cart-btn">Cart (<span id="cartCount">0</span>)</a>
    </div>
  </div>
</header>

<main class="container">
  <section class="hero">
    <div class="hero-left">
      <h1>Full-Stack Jumia clone E-commerce </h1>
      <p>DB-driven products, secure auth, orders & checkout demo</p>
      <a class="btn primary" href="#products">Shop Now</a>
    </div>
    <div class="hero-right">
      <img src="assets/image/blackfriday.gif" alt="hero">
    </div>
  </section>

  <section id="products" class="products-grid">
    <?php foreach($products as $p): ?>
      <article class="product-card" data-id="<?= $p['id'] ?>">
        <img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
        <h3><?= htmlspecialchars($p['title']) ?></h3>
        <p class="price"><?= money($p['price']) ?></p>
        <div class="card-actions">
          <button class="btn add-to-cart" data-id="<?= $p['id'] ?>" data-title="<?= htmlspecialchars($p['title']) ?>" data-price="<?= $p['price'] ?>">Add to cart</button>
          <button class="btn secondary">View</button>
        </div>
      </article>
    <?php endforeach; ?>
  </section>
</main>

<footer class="site-footer"><div class="container"><p>© <?= date('Y') ?> ShopCart Demo</p></div></footer>

<script>const API_CART = 'api/cart.php';</script>
<script src="assets/app.js"></script>
</body>
</html>