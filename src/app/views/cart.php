<?php 
include __DIR__ . '/templates/header.php';

// Hiển thị thông báo lỗi nếu có
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            ' . $_SESSION['error'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>';
    unset($_SESSION['error']);
}
?>

<link rel="stylesheet" href="../public/assets/css/style_cart.css">


<div class="container py-5 cart-container">
  <div class="row g-5">
    <!-- Giỏ hàng -->
    <div class="col-lg-8">
      <h4 class="mb-4 fw-semibold">Giỏ Hàng Của Tôi</h4>
      <hr>

      <?php if (empty($cartItems)): ?>
        <div class="text-center py-5">
          <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
          <h4 class="fw-semibold mb-3">Giỏ hàng của bạn đang trống</h4>
          <a href="index.php?action=list" class="btn btn-primary">Tiếp Tục Mua Sắm</a>
        </div>
      <?php else: ?>
        <form id="cartForm" method="POST" action="index.php?action=process_checkout">
          <?php 
          $total = 0;
          foreach ($cartItems as $item): 
              $itemTotal = $item['price'] * $item['quantity'];
              $total += $itemTotal;
              
              // Lấy ảnh sản phẩm - SỬA ĐƯỜNG DẪN ẢNH
              $productImage = !empty($item['product_image']) ? 
                  $base_url . 'assets/images/products/' . basename($item['product_image']) : 
                  $base_url . 'assets/images/no-image.jpg';
          ?>
            <div class="cart-item d-flex align-items-center justify-content-between border-bottom py-3" data-item-id="<?= $item['id'] ?>"
              style="background-color: transparent !important; transition: none !important;">
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
                <img src="<?= htmlspecialchars($productImage) ?>" 
                     alt="<?= htmlspecialchars($item['product_name']) ?>" 
                     class="cart-img me-3"
                     style="width: 80px; height: 80px; object-fit: cover;"
                     onerror="this.src='<?= $base_url ?>assets/images/no-image.jpg'">

                <div>
                  <h6 class="fw-bold mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                  <p class="mb-0 text-muted"><?= number_format($item['price'], 0, ',', '.') ?>₫</p>
                  <?php if (!empty($item['size'])): ?>
                    <small>Kích thước: <?= htmlspecialchars($item['size']) ?></small>
                  <?php endif; ?>
                  <?php if (!empty($item['sku'])): ?>
                    <small> | Mã: <?= htmlspecialchars($item['sku']) ?></small>
                  <?php endif; ?>
                  
                  <!-- HIỂN THỊ THÔNG TIN TỒN KHO -->
                  <div class="stock-info mt-1">
                    <small class="<?= $item['stock_quantity'] > 0 ? 'text-success' : 'text-danger' ?>">
                      <?= $item['stock_quantity'] > 0 ? 'Còn ' . $item['stock_quantity'] . ' sản phẩm' : 'Hết hàng' ?>
                    </small>
                  </div>
                </div>
              </div>

              <!-- Số lượng -->
              <div class="input-group quantity-box" style="width: 120px;">
                <button type="button" class="btn btn-outline-secondary btn-sm quantity-minus">−</button>
                <input type="text" class="form-control text-center quantity-input" 
                       value="<?= $item['quantity'] ?>"
                       data-item-id="<?= $item['id'] ?>"
                       data-unit-price="<?= $item['price'] ?>"
                       max="<?= $item['stock_quantity'] ?>">
                <button type="button" class="btn btn-outline-secondary btn-sm quantity-plus">+</button>
              </div>

              <!-- Giá -->
              <p class="fw-semibold mb-0 item-price" id="price-<?= $item['id'] ?>"><?= number_format($itemTotal, 0, ',', '.') ?>₫</p>

              <!-- Xóa -->
              <button type="button" class="btn btn-link text-danger ms-3 remove-item" onclick="removeItem(<?= $item['id'] ?>)">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
          <?php endforeach; ?>
        </form>
      <?php endif; ?>
    </div>

    <!-- Tổng đơn hàng -->
    <div class="col-lg-4">
      <h4 class="mb-4 fw-semibold">Tóm Tắt Đơn Hàng</h4>
      <hr>

      <div class="d-flex justify-content-between mb-2">
        <span>Tạm tính</span>
        <span id="subtotal">0₫</span>
      </div>

      <div class="d-flex justify-content-between mb-2">
        <span>Phí vận chuyển</span>
        <span>MIỄN PHÍ</span>
      </div>

      <hr>

      <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
        <span>Tổng cộng</span>
        <span id="total">0₫</span>
      </div>

      <button class="btn btn-dark w-100 py-3" id="checkoutBtn" disabled>Thanh Toán</button>
      <div id="noSelectionMessage" class="text-danger text-center mt-2" style="display: none;">
        Vui lòng chọn ít nhất một sản phẩm để thanh toán
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

    // Hàm định dạng số tiền VNĐ
    function formatCurrency(amount) {
        return amount.toLocaleString('vi-VN') + '₫';
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

    // Thêm sự kiện cho checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', calculateTotal);
    });

    // Sự kiện cho nút tăng/giảm số lượng
    document.querySelectorAll('.quantity-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            const itemId = input.getAttribute('data-item-id');
            const maxQuantity = parseInt(input.getAttribute('max'));
            const currentQuantity = parseInt(input.value);
            
            if (currentQuantity < maxQuantity) {
                const newQuantity = currentQuantity + 1;
                input.value = newQuantity;
                updateItemPrice(itemId, newQuantity);
                updateQuantityInDatabase(itemId, newQuantity);
            } else {
                alert('Số lượng tồn kho không đủ! Chỉ còn ' + maxQuantity + ' sản phẩm.');
            }
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

    // Sự kiện checkout
    checkoutBtn.addEventListener('click', function() {
        const selectedItems = document.querySelectorAll('.product-checkbox:checked');
        if (selectedItems.length === 0) {
            noSelectionMessage.style.display = 'block';
            return;
        }
        
        // Gửi form đến index.php?action=process_checkout
        cartForm.submit();
    });

    // Khởi tạo tính tổng
    calculateTotal();
});

// Các hàm Ajax cũng gọi qua index.php
function updateQuantityInDatabase(itemId, newQuantity) {
    fetch('index.php?action=update_cart_quantity', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `item_id=${itemId}&quantity=${newQuantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('Lỗi cập nhật số lượng');
        }
    });
}

function removeItem(itemId) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) return;

    fetch('index.php?action=remove_cart_item', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `item_id=${itemId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Lỗi xóa sản phẩm');
        }
    });
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

// Định dạng số tiền VNĐ
function formatCurrency(amount) {
    return amount.toLocaleString('vi-VN') + '₫';
}
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>
