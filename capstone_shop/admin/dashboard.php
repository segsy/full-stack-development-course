<?php
require_once '../functions.php';
require_once '../db.php';

// Start session only if not active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Restrict access to admins only
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    //header('Location: ../login.php');
    //exit;
    //echo "Access not allowed";
}

$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 10")->fetchAll();

// Stats
$totalProducts = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalSales  = (float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE payment_status = 'paid'")->fetchColumn();

// Prepare sales by day (last 7 days) for Chart.js
$stmt = $pdo->prepare("
    SELECT DATE(created_at) as day, COALESCE(SUM(total),0) as sales
    FROM orders
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at) ASC
");
$stmt->execute();
$rows = $stmt->fetchAll();

$dates = [];
$sales = [];
$map = [];
foreach ($rows as $r) { $map[$r['day']] = (float)$r['sales']; }
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-{$i} days"));
    $dates[] = $d;
    $sales[] = isset($map[$d]) ? $map[$d] : 0;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - Capstone</title>
  <link rel="stylesheet" href="../assets/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    .stat-grid{display:flex;gap:12px;margin-bottom:18px}
    .stat{flex:1;background:#fff;padding:14px;border-radius:8px;text-align:center}
    .chart-card{background:#fff;padding:18px;border-radius:8px}
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
          <strong>Order #<?= $o['id'] ?></strong> — Status: <?= htmlspecialchars($o['payment_status']) ?> — <?= $o['created_at'] ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No recent orders found.</p>
    <?php endif; ?>
  </div>
</div>
<div class="container">
  <h1>Admin Dashboard</h1>
  <div class="stat-grid">
    <div class="stat"><h2><?= $totalProducts ?></h2><p>Products</p></div>
    <div class="stat"><h2><?= $totalOrders ?></h2><p>Orders</p></div>
    <div class="stat"><h2>₦<?= number_format($totalSales,2) ?></h2><p>Sales (paid)</p></div>
  </div>

  <div class="chart-card">
    <h3>Sales — Last 7 days</h3>
    <canvas id="salesChart" width="800" height="250"></canvas>
  </div>

  <div style="margin-top:18px">
    <a class="btn" href="products_manage.php">Manage Products</a>
    <a class="btn" href="orders.php">View Orders</a>
  </div>
</div>
<script>
  const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($dates) ?>,
    datasets: [{
      label: 'Sales (NGN)',
      data: <?= json_encode($sales) ?>,
      borderWidth: 1,
      backgroundColor: 'rgba(54,162,235,0.6)'
    }]
  },
  options: {
    scales: {
      y: { beginAtZero: true }
    }
  }
});
</script>

</body>
</html>
