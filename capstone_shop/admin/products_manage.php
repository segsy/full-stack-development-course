<?php
// admin/products_manage.php
require_once '../functions.php';
require_once '../db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') { 
//  header('Location: ../login.php'); exit;
 }

$products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();
$csrf = csrf_token();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Manage Products</title><link rel="stylesheet" href="../assets/style.css"></head><body>
<div class="container">
  <h1>Products</h1>
  <a class="btn" href="dashboard.php">Back to Dashboard</a>
  <a class="btn" href="products_edit.php">+ Add Product</a>
  <table style="width:100%;margin-top:12px">
    <thead>
    <tr>
    <th>Title</th>
    <th>Products</th>
    <th>Price</th>
    <th>Stock</th>
    <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($products as $p): ?>
      <tr id="prod-<?= $p['id'] ?>">
        <td><?= htmlspecialchars($p['title']) ?></td>
        <td style="width:200px;height:400px;"><img src="../<?=htmlspecialchars($p['img']) ?>"></td>
        <td>₦<?= number_format($p['price'],2) ?></td>
        <td><?= intval($p['stock']) ?></td>
        <td>
          <a class="btn small" href="products_edit.php?id=<?= $p['id'] ?>">Edit</a>
          <button class="btn small danger delete-btn" data-id="<?= $p['id'] ?>">Delete</button>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody></table>
</div>

<script>
const csrfToken = <?= json_encode($csrf) ?>;
document.querySelectorAll('.delete-btn').forEach(btn=>{
  btn.addEventListener('click', async ()=>{
    if (!confirm('Delete this product?')) return;
    const id = btn.dataset.id;
    try {
      const res = await fetch('products_delete.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({ id: id, csrf_token: csrfToken })
      });
      const j = await res.json();
      if (j.success) {
        document.getElementById('prod-' + id).remove();
      } else {
        alert('Delete failed: ' + (j.message || 'unknown'));
      }
    } catch(e){ alert('Network error'); }
  });
});
</script>
</body></html>
