<?php 
include __DIR__ . '/templates/header.php';

// TỰ ĐỘNG CHỌN VARIANT ĐẦU TIÊN
$defaultVariantId = 0;
$defaultPrice = 0;

if (!empty($product['variants'])) {
    $defaultVariantId = $product['variants'][0]['variant_id'];
    $defaultPrice = $product['variants'][0]['price'];
} elseif (isset($product['pro_id'])) {
    // Nếu không có variants, dùng product_id và tìm variant
    $defaultVariantId = $product['pro_id'];
    $defaultPrice = $product['price'] ?? 0;
}

// Hiển thị thông tin variant được chọn
if (!empty($product['variants'])) {
    $selectedVariant = $product['variants'][0];
    echo "<!-- AUTO-SELECTED VARIANT: ID=" . $selectedVariant['variant_id'] . ", Size=" . ($selectedVariant['size'] ?? 'Default') . ", Price=$" . $selectedVariant['price'] . " -->";
}
?>

<link rel="stylesheet" href="/jewelry_website/public/assets/css/style.css">

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
          src="/jewelry_website/public/assets/images/no-image.jpg" 
          class="img-fluid border product-image" 
          alt="No image"
          style="max-height: 400px; object-fit: cover;"
        >
      <?php endif; ?>
    </div>

    <!-- Thông tin sản phẩm -->
    <div class="col-md-6 product-info">
      <p class="mb-2">
        <a href="/jewelry_website/public/index.php">Home</a> /
        <a href="/jewelry_website/public/index.php?action=list">All Products</a> /
        <span><?= htmlspecialchars($product['name']) ?></span>
      </p>

      <h3 class="mb-3"><?= htmlspecialchars($product['name']) ?></h3>

      <h4 class="text-primary mb-3 product-price">
        $<?= number_format($defaultPrice, 2) ?>
      </h4>

      <p class="mb-4"><?= nl2br(htmlspecialchars($product['description'] ?? '')) ?></p>

      <!-- HIỂN THỊ THÔNG TIN VARIANT ĐƯỢC CHỌN (TẠM THỜI) -->
      <?php if (!empty($product['variants'])): ?>
        <div class="mb-3 p-3 border rounded bg-light">
          <p class="mb-1"><strong>Selected Variant:</strong></p>
          <p class="mb-1">
            <?php
            $variantInfo = [];
            if (!empty($selectedVariant['size'])) $variantInfo[] = "Size: " . $selectedVariant['size'];
            if (!empty($selectedVariant['color'])) $variantInfo[] = "Color: " . $selectedVariant['color'];
            if (!empty($selectedVariant['material'])) $variantInfo[] = "Material: " . $selectedVariant['material'];
            
            echo implode(' | ', $variantInfo) ?: 'Standard';
            ?>
          </p>
          <small class="text-muted">(Size selection coming soon)</small>
        </div>
      <?php endif; ?>

      <!-- Chọn số lượng -->
      <div class="mb-4">
        <label class="form-label fw-bold">Quantity:</label>
        <div class="input-group quantity-box" style="width: 150px;">
          <button type="button" class="btn btn-outline-secondary quantity-minus">−</button>
          <input type="text" class="form-control text-center quantity-input" value="1" id="quantity">
          <button type="button" class="btn btn-outline-secondary quantity-plus">+</button>
        </div>
      </div>

      <!-- Nút Add to Cart -->
      <button type="button" 
              class="btn btn-outline-dark px-4 py-2 mt-3 add-to-cart-btn" 
              onclick="addToCart()"
              style="min-width: 150px;">
          <i class="fas fa-shopping-cart me-2"></i> Add to Cart
      </button>

      <!-- Thông báo kết quả -->
      <div id="cart-message" class="mt-2"></div>
    </div>
  </div>
</div>

<script>
// Biến toàn cục - LUÔN DÙNG VARIANT ĐẦU TIÊN
let currentVariantId = <?= $defaultVariantId ?>;
let currentQuantity = 1;

console.log('🛒 Auto-selected variant:', {
    variantId: currentVariantId,
    price: <?= $defaultPrice ?>
});

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

// Hàm thêm vào giỏ hàng
function addToCart() {
    console.log('Adding to cart with variant:', currentVariantId);
    
    const button = document.querySelector('.add-to-cart-btn');
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    button.disabled = true;

    fetch('../app/controllers/cartController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add_to_cart&variant_id=${currentVariantId}&quantity=${currentQuantity}`
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
        showCartMessage('Connection error: ' + error.message, false);
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function showCartMessage(message, isSuccess) {
    const messageDiv = document.getElementById('cart-message');
    if (messageDiv) {
        messageDiv.innerHTML = isSuccess ? '✅ ' + message : '❌ ' + message;
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
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>
