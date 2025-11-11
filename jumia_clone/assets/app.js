// assets/app.js
document.addEventListener('DOMContentLoaded', () => {
    // attach add-to-cart buttons
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const id = btn.dataset.id;
            const title = btn.dataset.title;
            const price = btn.dataset.price;

            try {
                const res = await fetch(API_CART, {
                    method: 'POST',
                    headers: {'Content-Type':'application/json'},
                    body: JSON.stringify({action:'add', id, title, price})
                });
                const data = await res.json();
                if (data.success) {
                    updateCartCount(data.count);
                    btn.innerText = 'Added ✓';
                    setTimeout(()=> btn.innerText = 'Add to cart', 900);
                } else {
                    alert(data.message || 'Could not add item');
                }
            } catch(err){
                console.error(err);
                alert('Network error');
            }
        });
    });

    // initialize cart count on page load
    fetch(API_CART, {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({action:'count'})})
        .then(r=>r.json()).then(d => { if(d.count!==undefined) updateCartCount(d.count); }).catch(()=>{});
});

function updateCartCount(n){
    const el = document.getElementById('cartCount');
    el.innerText = n || 0;
}
