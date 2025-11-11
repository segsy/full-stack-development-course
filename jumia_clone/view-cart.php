<?php
session_start();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Your Cart</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    .cart-wrap{max-width:900px;margin:28px auto;padding:18px;background:#fff;border-radius:8px}
    table{width:100%;border-collapse:collapse}
    th,td{padding:10px;border-bottom:1px solid #eee;text-align:left}
    .qty{width:60px}
    .text-right{text-align:right}
  </style>
</head>
<body>
  <div class="container">
    <h2>Your Shopping Cart</h2>
    <div class="cart-wrap" id="cartWrap">
      Loading cart...
    </div>
    <a href="index.php" class="btn">Continue Shopping</a>
  </div>

  <script>
    const API_CART = 'api/cart.php';

    async function loadCart(){
      const res = await fetch(API_CART, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({action:'list'})
      });
      const data = await res.json();
      const wrap = document.getElementById('cartWrap');
      if (!data.success) { wrap.innerHTML = 'Failed to load cart'; return; }

      if (data.items.length === 0) {
        wrap.innerHTML = '<p>Your cart is empty.</p>';
        return;
      }

      let html = `<table><thead><tr><th>Product</th><th>Price</th><th>Qty</th><th class="text-right">Subtotal</th><th></th></tr></thead><tbody>`;
      data.items.forEach(it=>{
        html += `<tr data-id="${it.id}">
          <td>${it.title}</td>
          <td>${formatPrice(it.price)}</td>
          <td>${it.qty}</td>
          <td class="text-right">${formatPrice(it.price * it.qty)}</td>
          <td><button class="btn remove">Remove</button></td>
        </tr>`;
      });
      html += `</tbody><tfoot><tr><td colspan="3"><strong>Total</strong></td><td class="text-right"><strong>${formatPrice(data.total)}</strong></td><td></td></tr></tfoot></table>
      <div style="margin-top:12px"><button id="clearCart" class="btn secondary">Clear Cart</button> <button class="btn primary" onclick="checkout()">Proceed to Checkout</button></div>`;
      wrap.innerHTML = html;

      document.querySelectorAll('.remove').forEach(b=>{
        b.addEventListener('click', async (e)=>{
          const id = e.target.closest('tr').dataset.id;
          await action('remove',{id});
          loadCart();
        });
      });

      document.getElementById('clearCart').addEventListener('click', async ()=>{
        await action('clear', {});
        loadCart();
      });
    }

    function formatPrice(n){ return '₦'+Number(n).toLocaleString(); }

    async function action(act, payload){
      payload.action = act;
      const res = await fetch(API_CART, {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)});
      return await res.json();
    }

    function checkout(){
 window.location.href = 'checkout.php';
    }

    loadCart();
  </script>
</body>
</html>
