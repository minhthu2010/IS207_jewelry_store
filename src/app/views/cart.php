<?php 
include __DIR__ . '/templates/header.php';

// Debug
echo "<!-- DEBUG CART ITEMS: " . count($cartItems) . " items -->";
?>

<link rel="stylesheet" href="../public/assets/css/style_cart.css">

<div class="container py-5 cart-container">
  <div class="row g-5">
    <!-- Giỏ hàng -->
    <div class="col-lg-8">
      <h4 class="mb-4 fw-semibold">My Cart</h4>
      <hr>

      <?php if (empty($cartItems)): ?>
        <div class="text-center py-5">
          <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
          <h4 class="fw-semibold mb-3">Your cart is empty</h4>
          <a href="index.php?action=list" class="btn btn-primary">Continue Shopping</a>
        </div>
      <?php else: ?>
        <form id="cartForm" method="POST" action="../app/controllers/cartController.php">
          <input type="hidden" name="action" value="checkout">
          
          <?php 
          $total = 0;
          foreach ($cartItems as $item): 
              $itemTotal = $item['price'] * $item['quantity'];
          ?>
            <div class="cart-item d-flex align-items-center justify-content-between border-bottom py-3" data-item-id="<?= $item['id'] ?>">
              <!-- Checkbox -->
              <div class="me-3">
                <input type="checkbox" 
                       class="form-check-input product-checkbox" 
                       name="selected_items[]" 
                       value="<?= $item['id'] ?>"
                       data-price="<?= $itemTotal ?>"
                       data-item-id="<?= $item['id'] ?>">
              </div>

              <div class="d-flex align-items-center flex-grow-1">
                <!-- Sử dụng ảnh mặc định vì không có cột images trong product_variant -->
                <img src="/jewelry_website/public/assets/images/no-image.jpg" 
                     alt="<?= htmlspecialchars($item['product_name']) ?>" 
                     class="cart-img me-3"
                     style="width: 80px; height: 80px; object-fit: cover;">

                <div>
                  <h6 class="fw-bold mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                  <p class="mb-0 text-muted">$<?= number_format($item['price'], 2) ?></p>
                  <?php if (!empty($item['size'])): ?>
                    <small>Size: <?= htmlspecialchars($item['size']) ?></small>
                  <?php endif; ?>
                  <?php if (!empty($item['sku'])): ?>
                    <small> | SKU: <?= htmlspecialchars($item['sku']) ?></small>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Số lượng -->
              <div class="input-group quantity-box" style="width: 120px;">
                <button type="button" class="btn btn-outline-secondary btn-sm quantity-minus">−</button>
                <input type="text" class="form-control text-center quantity-input" 
                       value="<?= $item['quantity'] ?>"
                       data-item-id="<?= $item['id'] ?>"
                       data-unit-price="<?= $item['price'] ?>">
                <button type="button" class="btn btn-outline-secondary btn-sm quantity-plus">+</button>
              </div>

              <!-- Giá -->
              <p class="fw-semibold mb-0 item-price" id="price-<?= $item['id'] ?>">$<?= number_format($itemTotal, 2) ?></p>

              <!-- Xóa -->
              <button type="button" class="btn btn-link text-danger ms-3 remove-item">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
          <?php endforeach; ?>
        </form>
      <?php endif; ?>
    </div>

    <!-- Tổng đơn hàng -->
    <div class="col-lg-4">
      <h4 class="mb-4 fw-semibold">Order Summary</h4>
      <hr>

      <div class="d-flex justify-content-between mb-2">
        <span>Subtotal</span>
        <span id="subtotal">$0.00</span>
      </div>

      <div class="d-flex justify-content-between mb-2">
        <span>Delivery</span>
        <span>FREE</span>
      </div>

      <hr>

      <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
        <span>Total</span>
        <span id="total">$0.00</span>
      </div>

      <button class="btn btn-dark w-100 py-3" id="checkoutBtn" disabled>Checkout</button>
      <div id="noSelectionMessage" class="text-danger text-center mt-2" style="display: none;">
        Please select at least one item to checkout
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const subtotalElement = document.getElementById('subtotal');
    const totalElement = document.getElementById('total');
    const checkoutBtn = document.getElementById('checkoutBtn');
    const noSelectionMessage = document.getElementById('noSelectionMessage');
    const cartForm = document.getElementById('cartForm');

    // Hàm định dạng số tiền
    function formatCurrency(amount) {
        return '$' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    // Hàm tính tổng tiền
    function calculateTotal() {
        let subtotal = 0;
        let selectedCount = 0;
        
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                subtotal += parseFloat(checkbox.getAttribute('data-price'));
                selectedCount++;
            }
        });
        
        // Cập nhật giao diện
        subtotalElement.textContent = formatCurrency(subtotal);
        totalElement.textContent = formatCurrency(subtotal);
        
        // Kiểm tra nếu có sản phẩm được chọn
        if (selectedCount > 0) {
            checkoutBtn.disabled = false;
            noSelectionMessage.style.display = 'none';
        } else {
            checkoutBtn.disabled = true;
            noSelectionMessage.style.display = 'block';
        }
    }

    // Hàm cập nhật giá khi thay đổi số lượng
    function updateItemPrice(itemId, quantity) {
        const checkbox = document.querySelector(`.product-checkbox[data-item-id="${itemId}"]`);
        const unitPrice = parseFloat(document.querySelector(`.quantity-input[data-item-id="${itemId}"]`).getAttribute('data-unit-price'));
        const totalPrice = unitPrice * quantity;
        
        if (checkbox) {
            checkbox.setAttribute('data-price', totalPrice);
            document.getElementById(`price-${itemId}`).textContent = formatCurrency(totalPrice);
            
            // Nếu checkbox đang được chọn, tính lại tổng
            if (checkbox.checked) {
                calculateTotal();
            }
        }
    }

    // Thêm sự kiện cho checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', calculateTotal);
    });

    // Sự kiện cho nút tăng/giảm số lượng
    document.querySelectorAll('.quantity-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            const itemId = input.getAttribute('data-item-id');
            const newQuantity = parseInt(input.value) + 1;
            input.value = newQuantity;
            updateItemPrice(itemId, newQuantity);
            updateQuantityInDatabase(itemId, newQuantity);
        });
    });

    document.querySelectorAll('.quantity-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            const itemId = input.getAttribute('data-item-id');
            const currentQuantity = parseInt(input.value);
            if (currentQuantity > 1) {
                const newQuantity = currentQuantity - 1;
                input.value = newQuantity;
                updateItemPrice(itemId, newQuantity);
                updateQuantityInDatabase(itemId, newQuantity);
            }
        });
    });

    // Sự kiện thay đổi số lượng trực tiếp
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const itemId = this.getAttribute('data-item-id');
            const newQuantity = parseInt(this.value) || 1;
            this.value = newQuantity;
            updateItemPrice(itemId, newQuantity);
            updateQuantityInDatabase(itemId, newQuantity);
        });
    });

    // Sự kiện xóa sản phẩm
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemId = this.closest('.cart-item').dataset.itemId;
            removeItem(itemId);
        });
    });

    // Sự kiện checkout
    checkoutBtn.addEventListener('click', function() {
        const selectedItems = document.querySelectorAll('.product-checkbox:checked');
        if (selectedItems.length === 0) {
            noSelectionMessage.style.display = 'block';
            return;
        }
        
        // Gửi form
        cartForm.submit();
    });

    // Khởi tạo tính tổng
    calculateTotal();
});

// Các hàm gọi API giữ nguyên
function updateQuantityInDatabase(itemId, newQuantity) {
    fetch('../app/controllers/cartController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update_quantity&item_id=${itemId}&quantity=${newQuantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('Error updating quantity');
        }
    });
}

function removeItem(itemId) {
    if (!confirm('Are you sure you want to remove this item?')) return;

    fetch('../app/controllers/cartController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=remove_item&item_id=${itemId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error removing item');
        }
    });
}
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>
