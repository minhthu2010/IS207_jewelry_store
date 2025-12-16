<?php 
// Xác định base URL
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/jewelry_website/public/";

include __DIR__ . '/templates/header.php';
?>

<link rel="stylesheet" href="<?= $base_url ?>assets/css/style_product.css">

<main class="product-page py-4">
  <div class="container">
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav mb-4">
      <a href="<?= $base_url ?>index.php" class="breadcrumb-link">Trang chủ</a> &nbsp; › &nbsp;
      <span>Tất cả sản phẩm</span>
    </nav>

    <h2 class="page-title mb-4">Tất cả sản phẩm</h2>

    <div class="row">
      <!-- Sidebar với bộ lọc -->
      <aside class="col-md-3">
        <form id="filter-form" method="GET" action="<?= $base_url ?>index.php">
          <input type="hidden" name="action" value="list">
          
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 filter-header">Bộ lọc</h5>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetFilters()">
              Xóa tất cả
            </button>
          </div>

          <!-- Category Filter -->
          <div class="filter-section mb-4">
            <label class="form-label fw-bold">Loại trang sức</label>
            <div class="category-filters">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="category" id="category_all" value="" 
                      <?= empty($_GET['category']) ? 'checked' : '' ?> onchange="this.form.submit()">
                <label class="form-check-label" for="category_all">Tất cả loại</label>
              </div>
              <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="category" id="category_<?= $category['cate_id'] ?>" 
                          value="<?= $category['cate_id'] ?>" 
                          <?= (!empty($_GET['category']) && $_GET['category'] == $category['cate_id']) ? 'checked' : '' ?> 
                          onchange="this.form.submit()">
                    <label class="form-check-label" for="category_<?= $category['cate_id'] ?>">
                      <?= htmlspecialchars($category['name']) ?>
                    </label>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>

          <!-- Price Filter -->
          <div class="filter-section mb-4">
            <label class="form-label fw-bold">Khoảng giá</label>
            <?php 
            $min_price = $filterOptions['price_range']['min_price'] ?? 0;
            $max_price = $filterOptions['price_range']['max_price'] ?? 1000;
            $current_min = $_GET['min_price'] ?? $min_price;
            $current_max = $_GET['max_price'] ?? $max_price;
            ?>
            
            <!-- Thêm hidden input để xác định khi nào price filter được áp dụng -->
            <input type="hidden" name="price_filter_applied" id="price_filter_applied" value="0">
            
            <div class="price-inputs mb-3">
              <div class="row g-2">
                <div class="col">
                  <input type="number" class="form-control form-control-sm" 
                        name="min_price" id="min_price" 
                        value="<?= $current_min ?>" 
                        min="<?= $min_price ?>" max="<?= $max_price ?>" 
                        placeholder="Tối thiểu">
                </div>
                <div class="col">
                  <input type="number" class="form-control form-control-sm" 
                        name="max_price" id="max_price" 
                        value="<?= $current_max ?>" 
                        min="<?= $min_price ?>" max="<?= $max_price ?>" 
                        placeholder="Tối đa">
                </div>
              </div>
            </div>
            <div class="price-slider-container">
              <input type="range" class="form-range price-slider" 
                    min="<?= $min_price ?>" max="<?= $max_price ?>" 
                    value="<?= $current_max ?>" id="price_slider">
            </div>
            <div class="d-flex justify-content-between price-range-labels">
              <small><?= number_format($min_price, 0) ?>đ</small>
              <small><?= number_format($max_price, 0) ?>đ</small>
            </div>
            <button type="button" id="apply-price-btn" class="btn btn-primary btn-sm w-100 mt-3">
              Áp dụng giá
            </button>
          </div>

          <!-- Tags Filter -->
          <?php if (!empty($filterOptions['popular_tags'])): ?>
          <div class="filter-section mb-4">
            <label class="form-label fw-bold">Chất liệu & Đặc điểm</label>
            <div class="tags-filters">
              <?php 
              $selectedTags = isset($_GET['tags']) ? (is_array($_GET['tags']) ? $_GET['tags'] : [$_GET['tags']]) : [];
              ?>
              <?php foreach ($filterOptions['popular_tags'] as $tag): ?>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="tags[]" 
                        id="tag_<?= md5($tag['tag_name']) ?>" 
                        value="<?= htmlspecialchars($tag['tag_name']) ?>"
                        <?= in_array($tag['tag_name'], $selectedTags) ? 'checked' : '' ?>
                        onchange="this.form.submit()">
                  <label class="form-check-label" for="tag_<?= md5($tag['tag_name']) ?>">
                    <?= htmlspecialchars($tag['tag_name']) ?>
                    <small class="text-muted">(<?= $tag['tag_count'] ?>)</small>
                  </label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>
        </form>
      </aside>

      <!-- Product grid -->
      <section class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-3 search-stats">
          <div class="result-count">
            <strong><?= !empty($products) ? count($products) : 0 ?></strong> sản phẩm
          </div>
          <div class="search-sort">
            <label for="sort" class="form-label me-2">Sắp xếp theo:</label>
            <select id="sort" class="form-select form-select-sm">
              <option value="newest" <?= empty($_GET['sort']) || $_GET['sort'] == 'newest' ? 'selected' : '' ?>>Sản phẩm mới nhất</option>
              <option value="price_low" <?= (!empty($_GET['sort']) && $_GET['sort'] == 'price_low') ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
              <option value="price_high" <?= (!empty($_GET['sort']) && $_GET['sort'] == 'price_high') ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
              <option value="name" <?= (!empty($_GET['sort']) && $_GET['sort'] == 'name') ? 'selected' : '' ?>>Tên: A-Z</option>
            </select>
          </div>
        </div>

        <!-- Active Filters -->
        <?php if (!empty($_GET['category']) || !empty($_GET['tags']) || (!empty($_GET['price_filter_applied']) && $_GET['price_filter_applied'] == '1')): ?>
        <div class="active-filters mb-3">
          <small class="text-muted">Bộ lọc đang áp dụng:</small>
          <?php if (!empty($_GET['category']) && !empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
              <?php if ($cat['cate_id'] == $_GET['category']): ?>
                <span class="badge bg-light text-dark ms-2">
                  Loại: <?= htmlspecialchars($cat['name']) ?>
                  <a href="<?= removeFilter('category') ?>" class="text-muted ms-1">×</a>
                </span>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php endif; ?>
          
          <?php if (!empty($selectedTags)): ?>
            <?php foreach ($selectedTags as $tag): ?>
              <span class="badge bg-light text-dark ms-2">
                <?= htmlspecialchars($tag) ?>
                <a href="<?= removeTagFilter($tag) ?>" class="text-muted ms-1">×</a>
              </span>
            <?php endforeach; ?>
          <?php endif; ?>
          
          <?php if (!empty($_GET['price_filter_applied']) && $_GET['price_filter_applied'] == '1' && !empty($_GET['min_price']) && !empty($_GET['max_price'])): ?>
            <span class="badge bg-light text-dark ms-2">
              Giá: <?= number_format($_GET['min_price'], 0) ?>đ - <?= number_format($_GET['max_price'], 0) ?>đ
              <a href="<?= removePriceFilter() ?>" class="text-muted ms-1">×</a>
            </span>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Hiển thị sản phẩm - 3 sản phẩm mỗi hàng -->
        <div class="product-list-results">
          <div class="row g-4">
            <?php if (!empty($products)): ?>
              <?php foreach ($products as $product): ?>
                <!-- 3 sản phẩm mỗi hàng (col-lg-4) -->
                <div class="col-lg-4 col-md-6">
                  <div class="product-card">
                    <a href="<?= $base_url ?>index.php?action=detail&id=<?= $product['pro_id'] ?>" class="product-link">
                      <div class="product-image">
                        <?php
                        $imageUrl = !empty($product['image_url']) ? 
                          $base_url . 'assets/images/products/' . basename($product['image_url']) : 
                          $base_url . 'assets/images/products/no-image.jpg';
                        ?>
                        <img 
                          src="<?= $imageUrl ?>" 
                          alt="<?= htmlspecialchars($product['name']) ?>" 
                          class="img-fluid"
                          onerror="this.src='<?= $base_url ?>assets/images/products/no-image.jpg'"
                        >
                      </div>
                      <div class="product-info">
                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-price">
                          <?php if (isset($product['min_price']) && $product['min_price'] > 0): ?>
                            <span class="price-amount"><?= number_format($product['min_price'], 0) ?>đ</span>
                          <?php else: ?>
                            <span class="price-na">Liên hệ</span>
                          <?php endif; ?>
                        </p>
                        <p class="product-category">
                          <small><?= htmlspecialchars($product['category_name'] ?? 'Chưa phân loại') ?></small>
                        </p>
                        <?php if ($product['has_size']): ?>
                          <span class="product-badge">Có nhiều kích thước</span>
                        <?php endif; ?>
                      </div>
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="col-12">
                <div class="no-results text-center py-5">
                  <div class="no-results-icon mb-4">
                    <i class="fas fa-search fa-3x text-muted"></i>
                  </div>
                  <h3 class="mb-3">Không tìm thấy sản phẩm phù hợp</h3>
                  <p class="text-muted mb-4">Không tìm thấy sản phẩm nào với bộ lọc hiện tại</p>
                  <div class="mt-4">
                    <a href="<?= $base_url ?>index.php?action=list" class="btn btn-primary">
                      <i class="fas fa-filter me-2"></i>Xóa bộ lọc
                    </a>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </section>
    </div>
  </div>
</main>

<?php 
// Include footer
$footerPath = __DIR__ . '/templates/footer.php';
if (file_exists($footerPath)) {
    include $footerPath;
} else {
    ?>
    <script src="<?= $base_url ?>assets/css/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
}
?>

<?php 
// Helper function để xóa filter
function removeFilter($filterName) {
    $params = $_GET;
    unset($params[$filterName]);
    return 'index.php?' . http_build_query($params);
}

// Helper function để xóa price filter
function removePriceFilter() {
    $params = $_GET;
    unset($params['min_price']);
    unset($params['max_price']);
    unset($params['price_filter_applied']);
    return 'index.php?' . http_build_query($params);
}

// Helper function để xóa tag filter
function removeTagFilter($tagToRemove) {
    $params = $_GET;
    if (isset($params['tags'])) {
        if (is_array($params['tags'])) {
            $params['tags'] = array_filter($params['tags'], function($tag) use ($tagToRemove) {
                return $tag !== $tagToRemove;
            });
            if (empty($params['tags'])) {
                unset($params['tags']);
            }
        } else {
            unset($params['tags']);
        }
    }
    return 'index.php?' . http_build_query($params);
}
?>

<script>
// JavaScript cho sắp xếp
document.getElementById('sort')?.addEventListener('change', function() {
    const sortValue = this.value;
    
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sortValue);
    
    // Xóa price_filter_applied khi sắp xếp (trừ khi đang áp dụng price filter)
    if (!document.getElementById('price_filter_applied')?.value === '1') {
        url.searchParams.delete('price_filter_applied');
        url.searchParams.delete('min_price');
        url.searchParams.delete('max_price');
    }
    
    window.location.href = url.toString();
});

function resetFilters() {
    window.location.href = '<?= $base_url ?>index.php?action=list';
}

// Price slider functionality
const priceSlider = document.getElementById('price_slider');
const minPriceInput = document.getElementById('min_price');
const maxPriceInput = document.getElementById('max_price');

if (priceSlider && maxPriceInput) {
    priceSlider.addEventListener('input', function() {
        maxPriceInput.value = this.value;
    });
    
    maxPriceInput.addEventListener('change', function() {
        priceSlider.value = this.value;
    });
    
    minPriceInput.addEventListener('change', function() {
        // Cập nhật slider khi min price thay đổi (tùy chọn)
        if (parseInt(minPriceInput.value) > parseInt(priceSlider.value)) {
            priceSlider.value = minPriceInput.value;
            maxPriceInput.value = minPriceInput.value;
        }
    });
}

// Xử lý form submit
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filter-form');
    const priceFilterApplied = document.getElementById('price_filter_applied');
    const applyPriceBtn = document.getElementById('apply-price-btn');
    
    // 1. Xử lý khi click category filter
    document.querySelectorAll('.category-filters input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function(e) {
            // Đặt price_filter_applied = 0 khi filter category
            if (priceFilterApplied) {
                priceFilterApplied.value = '0';
            }
            
            // Xóa min_price và max_price từ URL khi filter category
            const url = new URL(form.action);
            const params = new URLSearchParams();
            
            // Thêm category
            params.set('action', 'list');
            params.set('category', this.value);
            
            // Thêm tags nếu có
            document.querySelectorAll('.tags-filters input[type="checkbox"]:checked').forEach(checkbox => {
                params.append('tags[]', checkbox.value);
            });
            
            // Thêm sort nếu có
            const sortSelect = document.getElementById('sort');
            if (sortSelect && sortSelect.value) {
                params.set('sort', sortSelect.value);
            }
            
            window.location.href = url.pathname + '?' + params.toString();
        });
    });
    
    // 2. Xử lý khi click tag filter
    document.querySelectorAll('.tags-filters input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function(e) {
            // Đặt price_filter_applied = 0 khi filter tags
            if (priceFilterApplied) {
                priceFilterApplied.value = '0';
            }
            
            // Xóa min_price và max_price từ URL khi filter tags
            const url = new URL(form.action);
            const params = new URLSearchParams();
            
            // Thêm action
            params.set('action', 'list');
            
            // Thêm category nếu có
            const selectedCategory = document.querySelector('.category-filters input[type="radio"]:checked');
            if (selectedCategory && selectedCategory.value) {
                params.set('category', selectedCategory.value);
            }
            
            // Thêm tất cả tags đang được chọn
            document.querySelectorAll('.tags-filters input[type="checkbox"]:checked').forEach(cb => {
                params.append('tags[]', cb.value);
            });
            
            // Thêm sort nếu có
            const sortSelect = document.getElementById('sort');
            if (sortSelect && sortSelect.value) {
                params.set('sort', sortSelect.value);
            }
            
            window.location.href = url.pathname + '?' + params.toString();
        });
    });
    
    // 3. Xử lý khi click nút "Áp dụng giá"
    if (applyPriceBtn) {
        applyPriceBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Đặt price_filter_applied = 1 khi áp dụng price filter
            if (priceFilterApplied) {
                priceFilterApplied.value = '1';
            }
            
            // Submit form với tất cả filter
            const url = new URL(form.action);
            const params = new URLSearchParams();
            
            // Thêm action
            params.set('action', 'list');
            
            // Thêm category nếu có
            const selectedCategory = document.querySelector('.category-filters input[type="radio"]:checked');
            if (selectedCategory && selectedCategory.value) {
                params.set('category', selectedCategory.value);
            }
            
            // Thêm tags nếu có
            document.querySelectorAll('.tags-filters input[type="checkbox"]:checked').forEach(checkbox => {
                params.append('tags[]', checkbox.value);
            });
            
            // Thêm price filter (chỉ khi có giá trị hợp lệ)
            const minPrice = document.getElementById('min_price').value;
            const maxPrice = document.getElementById('max_price').value;
            if (minPrice && maxPrice) {
                params.set('min_price', minPrice);
                params.set('max_price', maxPrice);
                params.set('price_filter_applied', '1');
            }
            
            // Thêm sort nếu có
            const sortSelect = document.getElementById('sort');
            if (sortSelect && sortSelect.value) {
                params.set('sort', sortSelect.value);
            }
            
            window.location.href = url.pathname + '?' + params.toString();
        });
    }
    
    // 4. Xử lý khi enter trong price input
    if (minPriceInput) {
        minPriceInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyPriceBtn.click();
            }
        });
    }
    
    if (maxPriceInput) {
        maxPriceInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyPriceBtn.click();
            }
        });
    }
});
</script>
