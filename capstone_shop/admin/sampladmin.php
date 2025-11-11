<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - Jumia Clone</title>
  <link rel="stylesheet" href="../assets/style.css">
  <style>
    body { background:#f5f5f5; font-family:Arial, sans-serif; }
    .sidebar { width:220px; background:#111; color:#fff; height:100vh; position:fixed; padding:20px; }
    .sidebar h2 { color:#ff9900; }
    .sidebar a { display:block; color:#ccc; text-decoration:none; margin:8px 0; }
    .sidebar a:hover { color:#fff; }
    .main { margin-left:240px; padding:20px; }
    .card { background:#fff; border-radius:8px; padding:15px; margin-bottom:15px; box-shadow:0 1px 3px rgba(0,0,0,0.1); }
    .stats { display:flex; gap:20px; }
    .stat-box { flex:1; background:#fff; padding:15px; border-radius:6px; text-align:center; }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>Admin Panel</h2>
  <a href="dashboard.php">Dashboard</a>
  <a href="products_manage.php">Manage Products</a>
  <a href="orders.php">View Orders</a>
  <a href="../logout.php">Logout</a>
</div>

<div class="main">
  <h1>Welcome, Admin</h1>

  <div class="stats">
    <div class="stat-box">
      <h3><?= intval($totalProducts) ?></h3>
      <p>Products</p>
    </div>
    <div class="stat-box">
      <h3><?= intval($totalOrders) ?></h3>
      <p>Orders</p>
    </div>
  </div>

  <div class="card">
    <h3>Recent Orders</h3>
    <?php if ($recentOrders): ?>
      <?php foreach($recentOrders as $o): ?>
        <div style="padding:8px;border-bottom:1px solid #eee;">
          <strong>Order #<?= $o['id'] ?></strong> — Status: <?= htmlspecialchars($o['status']) ?> — <?= $o['created_at'] ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No recent orders found.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
