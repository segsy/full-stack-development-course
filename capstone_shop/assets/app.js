document.addEventListener('DOMContentLoaded', ()=>{
  document.querySelectorAll('.add-to-cart').forEach(btn=>{
    btn.addEventListener('click', async ()=>{
      const id = btn.dataset.id;
      const price = btn.dataset.price || 0;
      try {
        const res = await fetch(API_CART, {
          method:'POST',
          headers:{'Content-Type':'application/json'},
          body: JSON.stringify({action:'add', id:id, price:price})
        });
        const j = await res.json();
        if (j.success) {
          updateCartCount(j.count);
          btn.innerText = 'Added ✓';
          setTimeout(()=>btn.innerText='Add to cart', 900);
        } else {
          alert(j.message || 'Could not add item');
        }
      } catch (e) {
        console.error(e); alert('Network error');
      }
    });
  });

  // initial cart count
  (async ()=>{
    try {
      const res = await fetch(API_CART, {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({action:'count'})});
      const j = await res.json();
      if (j.success) updateCartCount(j.count);
    } catch(e){}
  })();
});

function updateCartCount(n){
  const el = document.getElementById('cartCount');
  if (el) el.innerText = n || 0;
}
