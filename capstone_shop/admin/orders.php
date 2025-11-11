<?php
// admin/orders.php
require_once '../functions.php';
require_once '../db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    //header('Location: ../login.php'); exit;
}

// fetch orders with user name
$stmt = $pdo->query("SELECT o.*, u.name as customer_name FROM orders o LEFT JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC LIMIT 200");
$orders = $stmt->fetchAll();

// statuses
$statuses = ['pending','paid','processing','shipped','completed','cancelled','failed'];
$csrf = csrf_token();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Admin - Orders</title><link rel="stylesheet" href="../assets/style.css">
<style>.orders-table th,td{padding:8px;border-bottom:1px solid #eee}</style>
</head><body>
<div class="container">
  <h1>Orders</h1>
  <a class="btn" href="dashboard.php">Back to Dashboard</a>
  <table class="orders-table" style="width:100%;margin-top:12px">
    <thead><tr><th>ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Created</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach($orders as $o): ?>
      <tr data-order="<?= $o['id'] ?>">
        <td>#<?= $o['id'] ?></td>
        <td><?= htmlspecialchars($o['customer_name'] ?? 'Guest') ?></td>
        <td>₦<?= number_format($o['total_amount'] ?? $o['total'] ?? 0,2) ?></td>
        <td>
          <select class="status-select" data-order="<?= $o['id'] ?>">
            <?php foreach($statuses as $s): ?>
              <option value="<?= $s ?>" <?= ($o['payment_status'] ?? $o['status'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
        </td>
        <td><?= $o['created_at'] ?></td>
        <td><a class="btn small" href="order_view.php?id=<?= $o['id'] ?>">View</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
const csrfToken = <?= json_encode($csrf) ?>;

document.querySelectorAll('.status-select').forEach(sel=>{
  sel.addEventListener('change', async (e)=>{
    const orderId = sel.dataset.order;
    const newStatus = sel.value;
    sel.disabled = true;
    try {
      const res = await fetch('update_order_status.php', {
        method:'POST',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({ order_id: orderId, status: newStatus, csrf_token: csrfToken })
      });
      const j = await res.json();
      if (!j.success) {
        alert('Update failed: ' + (j.message || 'unknown'));
      }
    } catch(err) {
      alert('Network error');
    } finally { sel.disabled = false; }
  });
});
</script>
</body></html>
