<?php
require_once 'functions.php';
require_once 'db.php';

$logged = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';

$stmt = $pdo->query("SELECT id, title, price, img, stock FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Mini Shop — Home</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header class="site-header">
    <div class="container header-inner">
      <div class="logo">MiniShop</div>
      <div class="search"><input placeholder="Search products"><button>Search</button></div>
      <div class="header-actions">
        <?php if ($logged): ?>
          <span>Hi, <?=htmlspecialchars($userName)?></span> |
          <a href="order_history.php">Orders</a> |
          <a href="logout.php">Logout</a>
        <?php else: ?>
          <a href="login.php">Login</a> |
          <a href="register.php">Register</a>
        <?php endif; ?>
        <a href="view-cart.php">Cart (<span id="cartCount">0</span>)</a>
      </div>
    </div>
  </header>

  <main class="container">
    <section class="hero">
      <div class="hero-left">
        <h1>Mini E-commerce Capstone</h1>
        <p>DB-driven products, secure auth, checkout & orders</p>
        <a class="btn primary" href="#products">Shop Now</a>
      </div>
      <div class="hero-right">
        <img src="assets/image/largescreentv.jpg" alt="hero">
      </div>
    </section>

    <section id="products" class="products-grid">
      <?php foreach ($products as $p): ?>
        <article class="product-card" data-id="<?= $p['id'] ?>">
          <a href="product.php?id=<?= $p['id'] ?>"><img src="<?= htmlspecialchars($p['img']) ?>" alt=""></a>
          <h3><a href="product.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></a></h3>
          <p class="price"><?= money($p['price']) ?></p>
          <div><button class="btn add-to-cart" data-id="<?= $p['id'] ?>" data-price="<?= $p['price'] ?>">Add to cart</button></div>
        </article>
      <?php endforeach; ?>
    </section>
  </main>

<script>const API_CART = 'api/cart.php';</script>
<script src="assets/app.js"></script>
</body>
</html>
