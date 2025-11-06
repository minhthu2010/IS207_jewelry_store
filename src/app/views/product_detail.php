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
        
        <!-- Phần viết đánh giá -->
        <div id="writeReviewSection" class="write-review-section" style="display: none;">
            <h5>Viết đánh giá của bạn</h5>
            <form id="reviewForm">
                <input type="hidden" id="reviewProductId" value="<?= $product['pro_id'] ?>">
                
                <div class="form-group">
                    <label class="fw-bold">Đánh giá của bạn:</label>
                    <div class="rating-stars" id="ratingStars">
                        <span class="rating-star" data-rating="1">★</span>
                        <span class="rating-star" data-rating="2">★</span>
                        <span class="rating-star" data-rating="3">★</span>
                        <span class="rating-star" data-rating="4">★</span>
                        <span class="rating-star" data-rating="5">★</span>
                    </div>
                    <input type="hidden" id="selectedRating" name="rating" required>
                </div>
                
                <div class="form-group">
                    <label for="reviewComment" class="fw-bold">Nhận xét:</label>
                    <textarea id="reviewComment" name="comment" class="review-textarea" 
                              placeholder="Chia sẻ cảm nhận của bạn về sản phẩm... (không bắt buộc)"></textarea>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn write-review-btn" id="submitReviewBtn">
                        Gửi đánh giá
                    </button>
                    <button type="button" class="btn btn-secondary" id="cancelReviewBtn">
                        Hủy
                    </button>
                </div>
            </form>
        </div>

        <!-- Hiển thị đánh giá của người dùng nếu đã review -->
        <div id="userReviewSection" style="display: none;">
            <h5>Đánh giá của bạn</h5>
            <div id="userReviewContent"></div>
        </div>

        <!-- Danh sách đánh giá -->
        <div id="reviewsContainer">
            <?php if (!empty($product['reviews'])): ?>
                <?php foreach ($product['reviews'] as $review): ?>
                    <div class="review-box mb-4 p-4 border rounded">
                        <div class="review-header">
                            <div>
                                <strong class="review-author d-block">
                                    <?= htmlspecialchars($review['fullname']) ?>
                                    <?php if ($review['customer_id'] == ($_SESSION['customer']['cus_id'] ?? 0)): ?>
                                        <span class="user-review-badge">Đánh giá của bạn</span>
                                    <?php endif; ?>
                                </strong>
                                <div class="review-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="<?= $i <= $review['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <small class="review-date"><?= date("d/m/Y H:i", strtotime($review['created_at'])) ?></small>
                        </div>
                        <p class="review-comment mb-0"><?= nl2br(htmlspecialchars($review['comment'] ?? '')) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-reviews">
                    <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Nút viết đánh giá -->
        <div class="text-center mt-4" id="reviewActionSection">
            <?php if (isset($_SESSION['customer'])): ?>
                <button id="checkReviewBtn" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Viết đánh giá
                </button>
            <?php else: ?>
                <div class="review-login-prompt">
                    <p class="mb-3">Đăng nhập để viết đánh giá sản phẩm</p>
                    <a href="<?= $base_url ?>login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>

<script>
// Biến toàn cục
let currentVariantId = <?= $defaultVariantId ?>;
let currentQuantity = 1;
let maxStock = <?= $defaultStock ?>;

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
            showCartMessage(data.message, true);
            if (data.itemCount !== undefined) {
                updateCartCount(data.itemCount);
            }
        } else {
            showCartMessage(data.message, false);
        }
        
    } catch (error) {
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

document.addEventListener('DOMContentLoaded', function() {
    // Thêm event listener cho nút kiểm tra review
    const checkReviewBtn = document.getElementById('checkReviewBtn');
    if (checkReviewBtn) {
        checkReviewBtn.addEventListener('click', checkReviewEligibility);
    }
    
    // Thêm event listener cho nút hủy review
    const cancelReviewBtn = document.getElementById('cancelReviewBtn');
    if (cancelReviewBtn) {
        cancelReviewBtn.addEventListener('click', function() {
            document.getElementById('writeReviewSection').style.display = 'none';
            document.getElementById('reviewActionSection').style.display = 'block';
            document.getElementById('userReviewSection').style.display = 'none';
        });
    }
    
    // Thêm event listener cho form review
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', handleReviewSubmit);
    }
    
    // Khởi tạo rating stars
    initRatingStars();
});

/* ========== REVIEW FUNCTIONALITY ========== */
// Hàm khởi tạo rating stars
function initRatingStars() {
    document.querySelectorAll('.rating-star').forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            document.getElementById('selectedRating').value = rating;
            
            // Update stars display
            document.querySelectorAll('.rating-star').forEach(s => {
                const starRating = parseInt(s.dataset.rating);
                s.classList.toggle('active', starRating <= rating);
                s.style.color = starRating <= rating ? '#ffc107' : '#ddd';
            });
        });
    });
}

// Hàm xử lý submit review
async function handleReviewSubmit(e) {
    e.preventDefault();
    
    const productId = document.getElementById('reviewProductId').value;
    const rating = document.getElementById('selectedRating').value;
    const comment = document.getElementById('reviewComment').value.trim();
    
    if (!rating) {
        showReviewMessage('Vui lòng chọn số sao đánh giá', 'error');
        return;
    }
    
    const submitBtn = document.getElementById('submitReviewBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('../app/controllers/reviewController.php?action=submit', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ 
                product_id: parseInt(productId), 
                rating: parseInt(rating), 
                comment: comment 
            })
        });
        
        // Kiểm tra response
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            showReviewMessage(data.message, 'success');
            // Reload trang để hiển thị review mới
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showReviewMessage(data.message, 'error');
        }
    } catch (error) {
        showReviewMessage('Lỗi kết nối khi gửi đánh giá: ' + error.message, 'error');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

// Hàm kiểm tra quyền viết đánh giá
async function checkReviewEligibility() {
    const productId = <?= $product['pro_id'] ?>;
    const checkReviewBtn = document.getElementById('checkReviewBtn');
    
    // Hiển thị loading
    const originalText = checkReviewBtn.innerHTML;
    checkReviewBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang kiểm tra...';
    checkReviewBtn.disabled = true;
    
    try {
        const response = await fetch(`../app/controllers/reviewController.php?action=check_eligibility&product_id=${productId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            if (data.hasReviewed) {
                showUserReview(data.userReview);
                if (data.message) {
                    showReviewMessage(data.message, 'info');
                }
            } else if (data.canReview) {
                showReviewForm();
                if (data.message) {
                    showReviewMessage('Bạn có thể viết đánh giá cho sản phẩm này', 'success');
                }
            } else {
                showReviewMessage(data.message || 'Bạn cần mua hàng để viết đánh giá', 'info');
            }
        } else {
            showReviewMessage(data.message, 'error');
        }
    } catch (error) {
        showReviewMessage('Lỗi kiểm tra quyền đánh giá: ' + error.message, 'error');
    } finally {
        checkReviewBtn.innerHTML = originalText;
        checkReviewBtn.disabled = false;
    }
}

// Hàm hiển thị form viết đánh giá
function showReviewForm() {
    document.getElementById('writeReviewSection').style.display = 'block';
    document.getElementById('reviewActionSection').style.display = 'none';
    document.getElementById('userReviewSection').style.display = 'none';
    
    // Reset form
    document.getElementById('selectedRating').value = '';
    document.getElementById('reviewComment').value = '';
    document.querySelectorAll('.rating-star').forEach(star => {
        star.classList.remove('active');
        star.style.color = '#ddd';
    });
}

// Hàm hiển thị đánh giá của người dùng
function showUserReview(review) {
    const userReviewSection = document.getElementById('userReviewSection');
    const userReviewContent = document.getElementById('userReviewContent');
    
    if (!review) {
        userReviewContent.innerHTML = '<p class="text-muted">Không tìm thấy đánh giá</p>';
    } else {
        userReviewContent.innerHTML = `
            <div class="review-box p-4 border rounded">
                <div class="review-header d-flex justify-content-between align-items-start">
                    <div>
                        <strong class="review-author d-block">
                            <?= $_SESSION['customer']['fullname'] ?? 'Bạn' ?>
                            <span class="user-review-badge">Đánh giá của bạn</span>
                        </strong>
                        <div class="review-rating text-warning">
                            ${'★'.repeat(review.rating)}${'☆'.repeat(5-review.rating)}
                        </div>
                    </div>
                    <small class="review-date text-muted">
                        ${new Date(review.created_at).toLocaleDateString('vi-VN')}
                    </small>
                </div>
                <p class="review-comment mb-0 mt-2">${review.comment || 'Không có nhận xét'}</p>
            </div>
        `;
    }
    
    userReviewSection.style.display = 'block';
    document.getElementById('reviewActionSection').style.display = 'none';
    document.getElementById('writeReviewSection').style.display = 'none';
}

// Hiển thị thông báo
function showReviewMessage(message, type) {
    // Tạo hoặc cập nhật message element
    let messageDiv = document.getElementById('reviewMessage');
    if (!messageDiv) {
        messageDiv = document.createElement('div');
        messageDiv.id = 'reviewMessage';
        document.getElementById('writeReviewSection').prepend(messageDiv);
    }
    
    messageDiv.className = `review-message ${type}`;
    messageDiv.textContent = message;
    messageDiv.style.display = 'block';
    
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>
