<?php
require_once 'functions.php';
require_once 'db.php';
if (empty($_SESSION['user_id'])) { header('Location: login.php?next=checkout.php'); exit; }
$uid = $_SESSION['user_id'];
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf_token'] ?? '')) { $error = 'Invalid CSRF token.'; }
    $address = trim($_POST['address'] ?? '');
    if ($address === '') $error = 'Please provide shipping address.';

    if (!$error) {
        // fetch cart items
        $st = $pdo->prepare("SELECT ci.product_id as id, p.title, p.price, ci.qty FROM cart_items ci JOIN products p ON p.id = ci.product_id WHERE ci.user_id = :uid");
        $st->execute([':uid'=>$uid]);
        $items = $st->fetchAll();
        if (empty($items)) { $error = 'Cart is empty.'; }
    }

    if (!$error) {
        $total = 0; foreach ($items as $it) $total += $it['price'] * $it['qty'];

        try {
            $pdo->beginTransaction();
            $ins = $pdo->prepare("INSERT INTO orders (user_id, total, shipping_address) VALUES (:uid,:total,:addr)");
            $ins->execute([':uid'=>$uid,':total'=>$total,':addr'=>$address]);
            $orderId = $pdo->lastInsertId();

            $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, title, price, qty, subtotal) VALUES (:oid,:pid,:title,:price,:qty,:subtotal)");
            foreach ($items as $it) {
                $subtotal = $it['price'] * $it['qty'];
                $stmtItem->execute([':oid'=>$orderId,':pid'=>$it['id'],':title'=>$it['title'],':price'=>$it['price'],':qty'=>$it['qty'],':subtotal'=>$subtotal]);
            }

            // clear cart
            $pdo->prepare("DELETE FROM cart_items WHERE user_id = :uid")->execute([':uid'=>$uid]);
            $pdo->commit();
            //$success = "Order placed successfully. Order ID: #".$orderId;
            header("Location: pay_stub.php?order_id={$orderId}");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to create order: " . $e->getMessage();
        }
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
<div class="container">
  <h2>Checkout</h2>
  <?php if ($error): ?><p class="error"><?=htmlspecialchars($error)?></p><?php endif; ?>
  <?php if ($success): ?><p class="success"><?=htmlspecialchars($success)?></p><p><a href="order_history.php" class="btn">View Orders</a></p><?php else: ?>
    <form method="post" action="">
      <?= csrf_field() ?>
      <label>Shipping Address</label>
      <textarea name="address" rows="4" required></textarea><br><br>
      <button class="btn primary" type="submit">Place Order</button>
    </form>
  <?php endif; ?>
</div></body></html>
