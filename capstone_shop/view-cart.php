<?php
require_once 'functions.php';
require_once 'db.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Cart</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
  <h2>Your Cart</h2>
  <div id="cartWrap">Loading...</div>
  <a href="index.php" class="btn">Continue Shopping</a>
</div>

<script>
async function loadCart(){
  const res = await fetch('api/cart.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({action:'list'})});
  const data = await res.json();
  const wrap = document.getElementById('cartWrap');
  if (!data.success) { wrap.innerHTML = 'Failed to load cart'; return; }
  if (data.items.length === 0) { wrap.innerHTML = '<p>Your cart is empty.</p>'; return; }

  let html = '<table class="cart-table"><thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead><tbody>';
  data.items.forEach(it=>{
    html += `<tr data-id="${it.id}"><td>${it.title}</td><td>₦${Number(it.price).toLocaleString()}</td><td>${it.qty}</td><td>₦${Number(it.price*it.qty).toLocaleString()}</td><td><button class="btn remove">Remove</button></td></tr>`;
  });
  html += `</tbody></table><p><strong>Total: ₦${Number(data.total).toLocaleString()}</strong></p><div><a class="btn primary" href="checkout.php">Proceed to Checkout</a></div><br><br>`; 
  wrap.innerHTML = html;

  document.querySelectorAll('.remove').forEach(b=>{
    b.addEventListener('click', async (e)=>{
      const id = e.target.closest('tr').dataset.id;
      await fetch('api/cart.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({action:'remove', id})});
      loadCart();
    });
  });
}


loadCart();
</script>
</body></html>
