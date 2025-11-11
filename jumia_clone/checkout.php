<?php
require_once 'functions.php';
require_once 'db.php';
if (empty($_SESSION['user_id'])) {
    header('Location: login.php?next=checkout.php'); 
    
    exit;
}
$userId = $_SESSION['user_id'];

// Assume cart total is stored in session
$total_amount = $_SESSION['cart_total'] ?? 0.00;

// fetch cart items for user
$st = $pdo->prepare("SELECT ci.product_id as id, p.title, p.price, ci.qty FROM cart_items ci JOIN products p ON p.id = ci.product_id WHERE ci.user_id = :uid");
$st->execute([':uid'=>$userId]);
$items = $st->fetchAll();

if (empty($items)) {
    $error = "Your cart is empty.";
} else {
    // compute total
    $total = 0; foreach($items as $it) $total += $it['price'] * $it['qty'];

    // create order inside transaction
    try {
        $pdo->beginTransaction();
        $ins = $pdo->prepare("INSERT INTO orders (user_id, amount, status) VALUES (:uid, :total, 'pending')");
        $ins->execute([':uid'=>$userId,':total'=>$total]);
        $orderId = $pdo->lastInsertId();

        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, title, price, qty, subtotal) VALUES (:oid,:pid,:title,:price,:qty,:subtotal)");
        foreach ($items as $it) {
            $subtotal = $it['price'] * $it['qty'];
            $stmtItem->execute([
                ':oid'=>$orderId, ':pid'=>$it['id'], ':title'=>$it['title'],
                ':price'=>$it['price'], ':qty'=>$it['qty'], ':subtotal'=>$subtotal
            ]);
        }

        // clear cart items
        $pdo->prepare("DELETE FROM cart_items WHERE user_id = :uid")->execute([':uid'=>$userId]);
        $pdo->commit();

        $success = "Order placed successfully! Order ID: #".$orderId;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Failed to create order: ".$e->getMessage();
    }
}
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="assets/style.css">
  </head>
  <body>
     <h2>Checkout Summary</h2>
  <p>Total Amount: ₦<?= number_format($total, 2) ?></p>

  <form method="POST" action="process_payment.php">
    <label>Select Payment Gateway:</label><br>
    <select name="gateway" required>
      <option value="stripe">Stripe (Simulated)</option>
      <option value="paystack">Paystack (Simulated)</option>
    </select>
    <input type="hidden" name="amount" value="<?= $total_amount ?>">
    <br><br>
    <button type="submit">Pay Now</button>
  </form>
<div class="container"><div style="max-width:900px;margin:30px auto;background:#fff;padding:20px;border-radius:8px">
  <h2>Checkout</h2>
  <?php if(!empty($error)): ?><p class="error"><?=htmlspecialchars($error)?></p><?php endif; ?>
  <?php if(!empty($success)): ?><p class="success"><?=htmlspecialchars($success)?></p>
    <p><a href="order_history.php" class="btn">View Orders</a></p>
  <?php else: ?>
    <h3>Order Summary</h3>
    <?php if(!empty($items)): ?>
      <table style="width:100%"><thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr></thead><tbody>
      <?php foreach($items as $it): ?>
        <tr><td><?=htmlspecialchars($it['title'])?></td><td><?=money($it['price'])?></td><td><?=intval($it['qty'])?></td><td><?=money($it['price']*$it['qty'])?></td></tr>
      <?php endforeach; ?>
      </tbody></table>
      <p><strong>Total: <?=money($total)?></strong></p>
      <form method="post" action="">
        <?= csrf_field() ?>
        <button class="btn primary" type="submit">Place Order (Demo)</button>
      </form>
    <?php endif; ?>
  <?php endif; ?>
</div></div>
</body></html>
