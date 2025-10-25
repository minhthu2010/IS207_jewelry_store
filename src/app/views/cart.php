<?php include __DIR__ . '/templates/header.php'; ?>
<link rel="stylesheet" href="../public/assets/css/style.css">


<div class="container py-5 cart-container">
  <div class="row g-5">
    <!-- Giỏ hàng -->
    <div class="col-lg-8">
      <h4 class="mb-4 fw-semibold">My Cart</h4>
      <hr>

      <!-- Sản phẩm trong giỏ -->
      <div class="cart-item d-flex align-items-center justify-content-between border-bottom py-3">
        <!-- Checkbox chọn sản phẩm -->
        <input type="checkbox" class="form-check-input me-3 product-check">

        <div class="d-flex align-items-center flex-grow-1">
          <img src="/jewelry_website/public/assets/images/bag.jpg" alt="Textured Evening Clutch" class="cart-img me-3">

          <div>
            <h6 class="fw-bold mb-1">Textured Evening Clutch</h6>
            <p class="mb-0 text-muted">$70.00</p>
            <small>Color: Navy</small>
          </div>
        </div>

        <!-- Số lượng -->
        <div class="input-group quantity-box">
          <button class="btn btn-outline-secondary btn-sm">−</button>
          <input type="text" class="form-control text-center" value="1">
          <button class="btn btn-outline-secondary btn-sm">+</button>
        </div>

        <!-- Giá -->
        <p class="fw-semibold mb-0 item-price">$70.00</p>

        <!-- Xóa -->
        <button class="btn btn-link text-danger ms-3"><i class="fas fa-trash-alt"></i></button>
      </div>

      
    </div>

    <!-- Tổng đơn hàng -->
    <div class="col-lg-4">
      <h4 class="mb-4 fw-semibold">Order Summary</h4>
      <hr>

      <div class="d-flex justify-content-between mb-2">
        <span>Subtotal</span>
        <span id="subtotal">$70.00</span>
      </div>

      <div class="d-flex justify-content-between mb-2">
        <span>Delivery</span>
        <span>FREE</span>
      </div>

      <div class="d-flex justify-content-between mb-2">
        <span>Ship to</span>
        <a href="#" class="text-decoration-none">Vietnam</a>
      </div>

      <hr>

      <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
        <span>Total</span>
        <span id="total">$70.00</span>
      </div>

      <!-- Nút checkout -->
      <button class="btn btn-dark w-100 py-3" id="checkoutBtn">Checkout</button>
      
    </div>
  </div>
</div>

<script>
  // Tính tổng dựa theo checkbox
  const checkboxes = document.querySelectorAll('.product-check');
  const subtotalElem = document.getElementById('subtotal');
  const totalElem = document.getElementById('total');

  checkboxes.forEach(chk => chk.addEventListener('change', updateTotal));

  function updateTotal() {
    let total = 0;
    checkboxes.forEach(chk => {
      if (chk.checked) total += 70; // tạm giá 70
    });
    subtotalElem.textContent = `$${total.toFixed(2)}`;
    totalElem.textContent = `$${total.toFixed(2)}`;
  }

  document.getElementById('checkoutBtn').addEventListener('click', () => {
    const selected = Array.from(checkboxes).filter(chk => chk.checked);
    if (selected.length === 0) {
      alert('Please select at least one product to checkout!');
    } else {
      alert(`Proceeding to checkout for ${selected.length} product(s).`);
    }
  });
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>
