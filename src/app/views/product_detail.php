<?php 
include __DIR__ . '/templates/header.php';

// TỰ ĐỘNG CHỌN VARIANT ĐẦU TIÊN
$defaultVariantId = 0;
$defaultPrice = 0;
$hasMultipleSizes = false;

if (!empty($product['variants'])) {
    $defaultVariantId = $product['variants'][0]['variant_id'];
    $defaultPrice = $product['variants'][0]['price'];
    
    // Kiểm tra xem có nhiều size không (bỏ qua các variant có size là NULL)
    $sizes = array_filter(array_column($product['variants'], 'size'));
    $hasMultipleSizes = count($sizes) > 0;
    
} elseif (isset($product['pro_id'])) {
    // Nếu không có variants, dùng product_id và tìm variant
    $defaultVariantId = $product['pro_id'];
    $defaultPrice = $product['price'] ?? 0;
}

// Hiển thị thông tin variant được chọn
if (!empty($product['variants'])) {
    $selectedVariant = $product['variants'][0];
}
?>

<link rel="stylesheet" href="/Web_vscode/public/assets/css/style.css">

<div class="container py-5 product-container">
  <div class="row g-5">
    <!-- Ảnh sản phẩm -->
    <div class="col-md-6 text-center">
      <?php if (!empty($product['images'])): ?>
        <img 
          src="<?= htmlspecialchars($product['images'][0]) ?>" 
          class="img-fluid border product-image mb-3" 
          alt="<?= htmlspecialchars($product['name']) ?>"
          style="max-height: 400px; object-fit: cover;"
        >
      <?php else: ?>
        <img 
          src="/Web_vscode/public/assets/images/no-image.jpg" 
          class="img-fluid border product-image" 
          alt="No image"
          style="max-height: 400px; object-fit: cover;"
        >
      <?php endif; ?>
    </div>

    <!-- Thông tin sản phẩm -->
    <div class="col-md-6 product-info">
      <p class="mb-2">
        <a href="/Web_vscode/public/index.php">Home</a> /
        <a href="/Web_vscode/public/index.php?action=list">All Products</a> /
        <span><?= htmlspecialchars($product['name']) ?></span>
      </p>

      <h3 class="mb-3"><?= htmlspecialchars($product['name']) ?></h3>

      <h4 class="text-primary mb-3 product-price">
        <?= number_format($defaultPrice, 0, ',', '.') ?>₫
      </h4>

      <p class="mb-4"><?= nl2br(htmlspecialchars($product['description'] ?? '')) ?></p>

      <!-- HIỂN THỊ THÔNG TIN SIZE NẾU CÓ -->
      <?php if ($hasMultipleSizes && !empty($product['variants'])): ?>
        <div class="mb-4">
          <label class="form-label fw-bold">Kích thước:</label>
          <div class="size-options">
            <?php foreach ($product['variants'] as $variant): ?>
              <?php if (!empty($variant['size'])): ?>
                <button type="button" 
                        class="btn btn-outline-secondary size-option <?= $variant['variant_id'] == $defaultVariantId ? 'active' : '' ?>"
                        data-variant-id="<?= $variant['variant_id'] ?>"
                        data-price="<?= $variant['price'] ?>"
                        onclick="selectSize(this)">
                  <?= htmlspecialchars($variant['size']) ?>
                </button>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Chọn số lượng -->
      <div class="mb-4">
        <label class="form-label fw-bold">Số lượng:</label>
        <div class="input-group quantity-box" style="width: 150px;">
          <button type="button" class="btn btn-outline-secondary quantity-minus">−</button>
          <input type="text" class="form-control text-center quantity-input" value="1" id="quantity">
          <button type="button" class="btn btn-outline-secondary quantity-plus">+</button>
        </div>
        <!-- Hiển thị thông tin tồn kho -->
        <div id="stock-info" class="mt-2"></div>
      </div>

      <!-- Nút Add to Cart -->
      <button type="button" 
              class="btn btn-outline-dark px-4 py-2 mt-3 add-to-cart-btn" 
              onclick="addToCart()"
              style="min-width: 150px;">
          <i class="fas fa-shopping-cart me-2"></i> Thêm vào giỏ hàng
      </button>

      <!-- Thông báo kết quả -->
      <div id="cart-message" class="mt-2"></div>
    </div>
  </div>
  <!-- Reviews -->
  <hr class="my-5">
  <div class="row">
    <div class="col-md-8 mx-auto">
      <h4 class="mb-4 text-center">Customer Reviews</h4>
      <?php if (!empty($product['reviews'])): ?>
        <?php foreach ($product['reviews'] as $r): ?>
          <div class="review-box mb-4 p-3 border rounded">
            <strong><?= htmlspecialchars($r['fullname']) ?></strong>
            <small class="text-muted float-end"><?= date("M d, Y", strtotime($r['created_at'])) ?></small>
            <div class="text-warning mb-2">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <i class="<?= $i <= $r['rating'] ? 'fas' : 'far' ?> fa-star"></i>
              <?php endfor; ?>
            </div>
            <p><?= htmlspecialchars($r['comment']) ?></p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-muted text-center">Chưa có đánh giá nào cho sản phẩm này.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
// Biến toàn cục
let currentVariantId = <?= $defaultVariantId ?>;
let currentQuantity = 1;

console.log('Auto-selected variant:', {
    variantId: currentVariantId,
    price: <?= $defaultPrice ?>
});

// Hàm chọn size
function selectSize(element) {
    // Xóa active class từ tất cả các nút size
    document.querySelectorAll('.size-option').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Thêm active class cho nút được chọn
    element.classList.add('active');
    
    // Cập nhật variant ID và giá
    currentVariantId = element.getAttribute('data-variant-id');
    const newPrice = element.getAttribute('data-price');
    
    // Cập nhật giá hiển thị
    document.querySelector('.product-price').textContent = 
        parseInt(newPrice).toLocaleString('vi-VN') + '₫';
    
    console.log('Selected size:', {
        variantId: currentVariantId,
        price: newPrice
    });
    
    // Cập nhật thông tin tồn kho cho variant mới
    updateMaxQuantity();
}

// Xử lý số lượng
document.querySelector('.quantity-minus')?.addEventListener('click', function() {
    const quantityInput = document.getElementById('quantity');
    if (parseInt(quantityInput.value) > 1) {
        quantityInput.value = parseInt(quantityInput.value) - 1;
        currentQuantity = quantityInput.value;
    }
});

document.querySelector('.quantity-plus')?.addEventListener('click', function() {
    const quantityInput = document.getElementById('quantity');
    quantityInput.value = parseInt(quantityInput.value) + 1;
    currentQuantity = quantityInput.value;
});

document.getElementById('quantity')?.addEventListener('change', function() {
    if (parseInt(this.value) < 1) this.value = 1;
    currentQuantity = this.value;
});

// Hàm kiểm tra tồn kho
async function checkStock(variantId, quantity) {
    try {
        const response = await fetch('../app/controllers/cartController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=check_stock&variant_id=${variantId}&quantity=${quantity}`
        });
        
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Lỗi kiểm tra tồn kho:', error);
        return { success: false, available: false };
    }
}

// Hàm thêm vào giỏ hàng
async function addToCart() {
    console.log('Adding to cart with variant:', currentVariantId);
    
    const button = document.querySelector('.add-to-cart-btn');
    const originalText = button.innerHTML;
    const quantity = parseInt(document.getElementById('quantity').value);
    
    // Kiểm tra tồn kho trước khi thêm
    const stockCheck = await checkStock(currentVariantId, quantity);
    if (!stockCheck.available) {
        showCartMessage('Số lượng tồn kho không đủ! Chỉ còn ' + stockCheck.current_stock + ' sản phẩm.', false);
        return;
    }
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';
    button.disabled = true;

    fetch('../app/controllers/cartController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add_to_cart&variant_id=${currentVariantId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response:', data);
        showCartMessage(data.message, data.success);
        
        if (data.success && data.itemCount !== undefined) {
            updateCartCount(data.itemCount);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showCartMessage('Lỗi kết nối: ' + error.message, false);
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function showCartMessage(message, isSuccess) {
    const messageDiv = document.getElementById('cart-message');
    if (messageDiv) {
        messageDiv.innerHTML = isSuccess ? '' + message : '' + message;
        messageDiv.className = `alert ${isSuccess ? 'alert-success' : 'alert-danger'} mt-2`;
        messageDiv.style.display = 'block';
        setTimeout(() => messageDiv.style.display = 'none', 5000);
    }
}

function updateCartCount(count) {
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        cartCount.textContent = count;
        cartCount.style.display = count > 0 ? 'flex' : 'none';
    }
}

// Cập nhật số lượng tối đa có thể mua
async function updateMaxQuantity() {
    try {
        const stockCheck = await checkStock(currentVariantId, 1);
        if (stockCheck.success) {
            const maxQuantity = stockCheck.current_stock;
            const quantityInput = document.getElementById('quantity');
            
            // Hiển thị thông tin tồn kho
            const stockInfo = document.getElementById('stock-info');
            if (stockInfo) {
                stockInfo.innerHTML = 
                    `<small class="${maxQuantity > 0 ? 'text-success' : 'text-danger'}">
                        ${maxQuantity > 0 ? `Còn ${maxQuantity} sản phẩm` : 'Hết hàng'}
                    </small>`;
                
                // Disable nút thêm nếu hết hàng
                if (maxQuantity === 0) {
                    document.querySelector('.add-to-cart-btn').disabled = true;
                    quantityInput.disabled = true;
                } else {
                    document.querySelector('.add-to-cart-btn').disabled = false;
                    quantityInput.disabled = false;
                    
                    // Giới hạn số lượng nhập
                    quantityInput.setAttribute('max', maxQuantity);
                    if (parseInt(quantityInput.value) > maxQuantity) {
                        quantityInput.value = maxQuantity;
                        currentQuantity = maxQuantity;
                    }
                }
            }
        }
    } catch (error) {
        console.error('Lỗi lấy thông tin tồn kho:', error);
    }
}

// Gọi hàm khi trang load
document.addEventListener('DOMContentLoaded', function() {
    updateMaxQuantity();
});
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>
