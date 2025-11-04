<?php 
// Xác định base URL
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/jewelry_website/public/";

// Kiểm tra xem header có tồn tại không
$headerPath = __DIR__ . '/templates/header.php';
if (file_exists($headerPath)) {
    include $headerPath;
} else {
    // Fallback header giống product.php
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Jewelry Store - Product Detail</title>
        <!-- Local Bootstrap -->
        <link rel="stylesheet" href="<?= $base_url ?>assets/css/bootstrap-5.3.8-dist/css/bootstrap.min.css">
        <!-- Local CSS -->
        <link rel="stylesheet" href="<?= $base_url ?>assets/css/style_product_detail.css">
    </head>
    <body>
    <?php

}

// TỰ ĐỘNG CHỌN VARIANT ĐẦU TIÊN
$defaultVariantId = 0;
$defaultPrice = 0;
$defaultStock = 0;
$hasMultipleSizes = false;

if (!empty($product['variants'])) {
    $defaultVariantId = $product['variants'][0]['variant_id'];
    $defaultPrice = $product['variants'][0]['price'];
    $defaultStock = $product['variants'][0]['stock_quantity'];
    
    // Kiểm tra xem có nhiều size không (bỏ qua các variant có size là NULL)
    $sizes = array_filter(array_column($product['variants'], 'size'));
    $hasMultipleSizes = count($sizes) > 1; // Chỉ hiển thị size options nếu có nhiều hơn 1 size
    
} elseif (isset($product['pro_id'])) {
    // Nếu không có variants, dùng product_id và tìm variant
    $defaultVariantId = $product['pro_id'];
    $defaultPrice = $product['price'] ?? 0;
    $defaultStock = 0;
}

// Hiển thị thông tin variant được chọn
if (!empty($product['variants'])) {
    $selectedVariant = $product['variants'][0];
}
?>

<!-- Breadcrumb - ĐÃ DI CHUYỂN VÀ CHỈNH SỬA VỊ TRÍ -->
<nav aria-label="breadcrumb" class="container breadcrumb-container">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= $base_url ?>index.php" class="text-decoration-none"><i class="fas fa-home me-1"></i>Trang chủ</a></li>
    <li class="breadcrumb-item"><a href="<?= $base_url ?>index.php?action=list" class="text-decoration-none">Tất cả sản phẩm</a></li>
    <li class="breadcrumb-item active text-truncate" style="max-width: 300px;" title="<?= htmlspecialchars($product['name']) ?>">
      <?= htmlspecialchars($product['name']) ?>
    </li>
  </ol>
</nav>

<div class="container py-5 product-container">
  <div class="row g-5">
    <!-- Ảnh sản phẩm - SLIDER -->
    <div class="col-md-6">
      <?php if (!empty($product['images'])): ?>
        <!-- Ảnh chính -->
        <div class="text-center mb-3">
          <img 
            id="main-image"
            src="<?= $base_url . 'assets/images/products/' . basename($product['images'][0]['image_url']) ?>" 
            class="img-fluid border product-image" 
            alt="<?= htmlspecialchars($product['name']) ?>"
            style="max-height: 400px; object-fit: cover; width: 100%;"
          >
        </div>
        
        <!-- Ảnh phụ -->
        <?php if (count($product['images']) > 1): ?>
        <div class="d-flex justify-content-center gap-2">
          <?php foreach ($product['images'] as $index => $image): ?>
            <img 
              src="<?= $base_url . 'assets/images/products/' . basename($image['image_url']) ?>" 
              class="img-thumbnail thumb-image <?= $index === 0 ? 'active' : '' ?>" 
              alt="<?= htmlspecialchars($product['name']) ?> - <?= $index + 1 ?>"
              style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
              onclick="changeMainImage(this, '<?= $base_url . 'assets/images/products/' . basename($image['image_url']) ?>')"
            >
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      <?php else: ?>
        <img 
          src="<?= $base_url ?>assets/images/products/no-image.jpg" 
          class="img-fluid border product-image" 
          alt="No image"
          style="max-height: 400px; object-fit: cover; width: 100%;"
        >
      <?php endif; ?>
    </div>

    <!-- Thông tin sản phẩm -->
    <div class="col-md-6 product-info">
      <!-- ĐÃ XÓA BREADCRUMB Ở ĐÂY -->

      <h1 class="mb-3"><?= htmlspecialchars($product['name']) ?></h1>

      <!-- Rating -->
      <?php if ($product['review_count'] > 0): ?>
      <div class="d-flex align-items-center mb-3">
        <div class="text-warning me-2">
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <i class="<?= $i <= $product['average_rating'] ? 'fas' : ($i - 0.5 <= $product['average_rating'] ? 'fas fa-star-half-alt' : 'far') ?> fa-star"></i>
          <?php endfor; ?>
        </div>
        <span class="text-muted">(<?= $product['review_count'] ?> đánh giá)</span>
      </div>
      <?php endif; ?>

      <!-- Giá -->
      <h3 class="text-primary mb-3 product-price">
        <?= number_format($defaultPrice, 0, ',', '.') ?>₫
      </h3>

      <!-- Mô tả -->
      <p class="mb-4"><?= nl2br(htmlspecialchars($product['description'] ?? '')) ?></p>

      <!-- HIỂN THỊ THÔNG TIN SIZE NẾU CÓ NHIỀU SIZE -->
      <?php if ($hasMultipleSizes && !empty($product['variants'])): ?>
        <div class="mb-4">
          <label class="form-label fw-bold">Kích thước:</label>
          <div class="size-options d-flex flex-wrap gap-2">
            <?php foreach ($product['variants'] as $variant): ?>
              <?php if (!empty($variant['size'])): ?>
                <button type="button" 
                        class="btn btn-outline-dark size-option <?= $variant['variant_id'] == $defaultVariantId ? 'active' : '' ?>"
                        data-variant-id="<?= $variant['variant_id'] ?>"
                        data-price="<?= $variant['price'] ?>"
                        data-stock="<?= $variant['stock_quantity'] ?>"
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
          <input type="number" class="form-control text-center quantity-input" value="1" min="1" max="<?= $defaultStock ?>" id="quantity">
          <button type="button" class="btn btn-outline-secondary quantity-plus">+</button>
        </div>
        <!-- Hiển thị thông tin tồn kho -->
        <div id="stock-info" class="mt-2">
          <small class="<?= $defaultStock > 0 ? 'text-success' : 'text-danger' ?>">
            <?= $defaultStock > 0 ? "Còn {$defaultStock} sản phẩm" : 'Tạm hết hàng' ?>
          </small>
        </div>
      </div>

      <!-- Nút Add to Cart -->
      <button type="button" 
              class="btn btn-dark px-4 py-2 mt-3 add-to-cart-btn" 
              onclick="addToCart()"
              style="min-width: 150px;"
              <?= $defaultStock === 0 ? 'disabled' : '' ?>>
          <i class="fas fa-shopping-cart me-2"></i> Thêm vào giỏ hàng
      </button>

      <!-- Thông báo kết quả -->
      <div id="cart-message" class="mt-3"></div>

      <!-- Thông tin bảo hành -->
      <?php if (!empty($product['warranty_period'])): ?>
      <div class="mt-4 p-3 bg-light rounded">
        <h6 class="fw-bold">🛡️ Bảo hành</h6>
        <p class="mb-0"><?= $product['warranty_period'] ?> tháng - <?= $product['warranty_description'] ?? 'Bảo hành chính hãng' ?></p>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Reviews Section -->
  <hr class="my-5">
  <div class="row">
    <div class="col-12">
      <h4 class="mb-4">Đánh giá sản phẩm</h4>
      
      <?php if (!empty($product['reviews'])): ?>
        <?php foreach ($product['reviews'] as $review): ?>
          <div class="review-box mb-4 p-4 border rounded">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <strong class="d-block"><?= htmlspecialchars($review['fullname']) ?></strong>
                <div class="text-warning mb-2">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="<?= $i <= $review['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                  <?php endfor; ?>
                </div>
              </div>
              <small class="text-muted"><?= date("d/m/Y H:i", strtotime($review['created_at'])) ?></small>
            </div>
            <p class="mb-0"><?= nl2br(htmlspecialchars($review['comment'] ?? '')) ?></p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="text-center py-4">
          <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
          <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
// Biến toàn cục
let currentVariantId = <?= $defaultVariantId ?>;
let currentQuantity = 1;
let maxStock = <?= $defaultStock ?>;

console.log('Auto-selected variant:', {
    variantId: currentVariantId,
    price: <?= $defaultPrice ?>,
    stock: <?= $defaultStock ?>
});

// Hàm thay đổi ảnh chính
function changeMainImage(thumbElement, imageUrl) {
    // Cập nhật ảnh chính
    document.getElementById('main-image').src = imageUrl;
    
    // Cập nhật active state
    document.querySelectorAll('.thumb-image').forEach(thumb => {
        thumb.classList.remove('active', 'border-primary');
        thumb.classList.add('border-secondary');
    });
    thumbElement.classList.add('active', 'border-primary');
    thumbElement.classList.remove('border-secondary');
}

// Hàm chọn size
function selectSize(element) {
    // Xóa active class từ tất cả các nút size
    document.querySelectorAll('.size-option').forEach(btn => {
        btn.classList.remove('active', 'btn-dark');
        btn.classList.add('btn-outline-dark');
    });
    
    // Thêm active class cho nút được chọn
    element.classList.add('active', 'btn-dark');
    element.classList.remove('btn-outline-dark');
    
    // Cập nhật variant ID, giá và stock
    currentVariantId = element.getAttribute('data-variant-id');
    const newPrice = element.getAttribute('data-price');
    maxStock = parseInt(element.getAttribute('data-stock'));
    
    // Cập nhật giá hiển thị
    document.querySelector('.product-price').textContent = 
        parseInt(newPrice).toLocaleString('vi-VN') + '₫';
    
    // Cập nhật số lượng tối đa
    document.getElementById('quantity').max = maxStock;
    
    // Cập nhật thông tin tồn kho
    const stockInfo = document.getElementById('stock-info');
    if (stockInfo) {
        stockInfo.innerHTML = 
            `<small class="${maxStock > 0 ? 'text-success' : 'text-danger'}">
                ${maxStock > 0 ? `Còn ${maxStock} sản phẩm` : 'Tạm hết hàng'}
            </small>`;
    }
    
    // Enable/disable nút thêm vào giỏ
    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    addToCartBtn.disabled = maxStock === 0;
    
    // Reset quantity về 1 nếu vượt quá stock
    if (currentQuantity > maxStock) {
        document.getElementById('quantity').value = maxStock > 0 ? 1 : 0;
        currentQuantity = maxStock > 0 ? 1 : 0;
    }
    
    console.log('Selected size:', {
        variantId: currentVariantId,
        price: newPrice,
        stock: maxStock
    });
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
    const currentValue = parseInt(quantityInput.value);
    
    if (currentValue < maxStock) {
        quantityInput.value = currentValue + 1;
        currentQuantity = quantityInput.value;
    } else {
        alert('Số lượng tồn kho không đủ! Chỉ còn ' + maxStock + ' sản phẩm.');
    }
});

document.getElementById('quantity')?.addEventListener('change', function() {
    let value = parseInt(this.value);
    
    if (isNaN(value) || value < 1) {
        value = 1;
        this.value = 1;
    }
    
    if (value > maxStock) {
        alert('Số lượng tồn kho không đủ! Chỉ còn ' + maxStock + ' sản phẩm.');
        this.value = maxStock;
        value = maxStock;
    }
    
    this.value = value;
    currentQuantity = value;
});

// Hàm thêm vào giỏ hàng
// Hàm thêm vào giỏ hàng
async function addToCart() {
    const button = document.querySelector('.add-to-cart-btn');
    const originalText = button.innerHTML;
    const quantity = parseInt(document.getElementById('quantity').value);
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';
    button.disabled = true;

    try {
        const baseUrl = window.location.origin;
        const controllerUrl = baseUrl + '/jewelry_website/app/controllers/cartController.php';
        
        const formData = new URLSearchParams();
        formData.append('action', 'add_to_cart');
        formData.append('variant_id', currentVariantId);
        formData.append('quantity', quantity);

        const response = await fetch(controllerUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData
        });

        const contentType = response.headers.get('content-type');
        
        if (!contentType || !contentType.includes('application/json')) {
            const textResponse = await response.text();
            throw new Error('Lỗi server: không nhận được phản hồi hợp lệ');
        }
        
        const data = await response.json();
        
        if (data.success) {
            // SỬA: Thay showMessage bằng showCartMessage
            showCartMessage(data.message, true);
            if (data.itemCount !== undefined) {
                updateCartCount(data.itemCount);
            }
        } else {
            // SỬA: Thay showMessage bằng showCartMessage
            showCartMessage(data.message, false);
        }
        
    } catch (error) {
        // SỬA: Thay showMessage bằng showCartMessage
        showCartMessage('Lỗi: ' + error.message, false);
    } finally {
        button.innerHTML = originalText;
        button.disabled = false;
    }
}

function showCartMessage(message, isSuccess) {
    const messageDiv = document.getElementById('cart-message');
    if (messageDiv) {
        messageDiv.innerHTML = `<div class="alert ${isSuccess ? 'alert-success' : 'alert-danger'} alert-dismissible fade show">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
        
        // Tự động ẩn thông báo sau 5 giây
        setTimeout(() => {
            const alert = messageDiv.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
}

function updateCartCount(count) {
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        cartCount.textContent = count;
        cartCount.style.display = count > 0 ? 'flex' : 'none';
    }
}

// Khởi tạo khi trang load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Product detail page loaded');
});
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>

