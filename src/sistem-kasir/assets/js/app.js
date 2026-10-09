document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', event => {
      if (!confirm(form.dataset.confirm || 'Yakin ingin melanjutkan?')) event.preventDefault();
    });
  });

  const type = document.getElementById('stock-type');
  const location = document.getElementById('stock-location');
  function updateStockLocation() {
    if (!type || !location) return;
    const transfer = type.value.startsWith('pindah_');
    location.disabled = transfer;
    if (type.value === 'pindah_ke_etalase') location.value = 'gudang';
    if (type.value === 'pindah_ke_gudang') location.value = 'etalase';
  }
  if (type && location) {
    type.addEventListener('change', updateStockLocation);
    updateStockLocation();
  }

  const grid = document.getElementById('product-grid');
  if (!grid) return;
  const search = document.getElementById('product-search');
  const cartContainer = document.getElementById('cart-items');
  const cartData = document.getElementById('cart-data');
  const payment = document.getElementById('payment');
  const checkout = document.getElementById('checkout');
  const cartCount = document.getElementById('cart-count');
  const subtotalNode = document.getElementById('subtotal');
  const totalNode = document.getElementById('total');
  const changeNode = document.getElementById('change');
  const noProducts = document.getElementById('no-products');
  const cart = new Map();
  const money = n => 'Rp ' + Number(n).toLocaleString('id-ID');

  search?.addEventListener('input', () => {
    const q = search.value.toLowerCase().trim();
    let visible = 0;
    grid.querySelectorAll('[data-product]').forEach(card => {
      const text = (card.dataset.code + ' ' + card.dataset.name + ' ' + card.dataset.brand).toLowerCase();
      card.hidden = !text.includes(q);
      if (!card.hidden) visible++;
    });
    noProducts?.classList.toggle('hidden', visible > 0);
  });

  grid.querySelectorAll('[data-product]').forEach(card => {
    card.addEventListener('click', () => {
      const id = Number(card.dataset.id);
      const current = cart.get(id);
      const stock = Number(card.dataset.stock);
      if (current && current.qty >= stock) {
        alert('Jumlah melebihi stok etalase yang tersedia.');
        return;
      }
      cart.set(id, {
        id, code: card.dataset.code, name: card.dataset.name,
        brand: card.dataset.brand, price: Number(card.dataset.price),
        stock, qty: current ? current.qty + 1 : 1
      });
      renderCart();
    });
  });

  function renderCart() {
    const items = [...cart.values()];
    const totalQty = items.reduce((sum, item) => sum + item.qty, 0);
    const total = items.reduce((sum, item) => sum + item.price * item.qty, 0);
    cartCount.textContent = `${totalQty} barang dipilih`;
    subtotalNode.textContent = money(total);
    totalNode.textContent = money(total);
    cartData.value = JSON.stringify(items.map(({id, qty}) => ({id, qty})));
    checkout.disabled = !items.length;
    if (!items.length) {
      cartContainer.innerHTML = '<div class="cart-empty"><div class="cart-empty-icon">🛒</div><strong>Keranjang masih kosong</strong><p>Pilih barang dari daftar untuk memulai transaksi.</p></div>';
    } else {
      cartContainer.innerHTML = items.map(item => `
        <div class="cart-item">
          <div class="cart-item-main"><strong>${escapeHtml(item.name)}</strong><small>${escapeHtml(item.code)} · ${money(item.price)}</small>
            <div class="qty-control"><button type="button" data-minus="${item.id}" aria-label="Kurangi">−</button><span>${item.qty}</span><button type="button" data-plus="${item.id}" aria-label="Tambah">＋</button></div>
          </div>
          <div class="cart-item-side"><strong>${money(item.price * item.qty)}</strong><button type="button" class="remove-item" data-remove="${item.id}">Hapus</button></div>
        </div>`).join('');
    }
    cartContainer.querySelectorAll('[data-minus]').forEach(btn => btn.addEventListener('click', () => {
      const id = Number(btn.dataset.minus), item = cart.get(id);
      if (item.qty <= 1) cart.delete(id); else item.qty--;
      renderCart();
    }));
    cartContainer.querySelectorAll('[data-plus]').forEach(btn => btn.addEventListener('click', () => {
      const id = Number(btn.dataset.plus), item = cart.get(id);
      if (item.qty >= item.stock) return alert('Jumlah melebihi stok etalase.');
      item.qty++; renderCart();
    }));
    cartContainer.querySelectorAll('[data-remove]').forEach(btn => btn.addEventListener('click', () => {
      cart.delete(Number(btn.dataset.remove)); renderCart();
    }));
    updateChange();
  }

  function updateChange() {
    const items = [...cart.values()];
    const total = items.reduce((sum, item) => sum + item.price * item.qty, 0);
    const paid = Number(payment.value || 0);
    changeNode.textContent = money(Math.max(0, paid - total));
  }
  payment?.addEventListener('input', updateChange);
  document.getElementById('clear-cart')?.addEventListener('click', () => {
    cart.clear(); renderCart();
  });
  document.getElementById('checkout-form')?.addEventListener('submit', event => {
    if (!cart.size) { event.preventDefault(); alert('Keranjang masih kosong.'); return; }
    const total = [...cart.values()].reduce((sum, item) => sum + item.price * item.qty, 0);
    if (Number(payment.value) < total) { event.preventDefault(); alert('Uang pembayaran kurang.'); }
    else if (!confirm('Proses transaksi dan kurangi stok etalase?')) event.preventDefault();
  });
  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[ch]));
  }
});
