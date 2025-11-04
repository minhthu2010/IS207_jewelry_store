<?php 
// Xác định base URL
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/jewelry_website/public/";


include __DIR__ . '/templates/header.php';
?>

<link rel="stylesheet" href="../public/assets/css/style.css">


<main class="product-page py-4">
  <div class="container">
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav mb-4">
      <a href="<?= $base_url ?>index.php" class="breadcrumb-link">Trang chủ</a> &nbsp; › &nbsp;
      <span>Tất cả sản phẩm</span>
    </nav>

    <h2 class="mb-4">Tất cả sản phẩm</h2>

    <div class="row">
<!-- Sidebar với bộ lọc mới -->
<!-- Sidebar với bộ lọc tiếng Việt -->
<aside class="col-md-3">
  <form id="filter-form" method="GET" action="<?= $base_url ?>index.php">
    <input type="hidden" name="action" value="list">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">Bộ lọc</h5>
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
      <div class="price-inputs mb-2">
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
      <input type="range" class="form-range price-slider" 
             min="<?= $min_price ?>" max="<?= $max_price ?>" 
             value="<?= $current_max ?>" id="price_slider">
      <div class="d-flex justify-content-between">
        <small>$<?= number_format($min_price, 0) ?></small>
        <small>$<?= number_format($max_price, 0) ?></small>
      </div>
      <button type="submit" class="btn btn-primary btn-sm w-100 mt-2">Áp dụng giá</button>
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
  <div class="d-flex justify-content-between align-items-center mb-3">
    <span><?= !empty($products) ? count($products) : 0 ?> sản phẩm</span>
    <div>
      <label for="sort" class="form-label me-2">Sắp xếp theo:</label>
      <!-- SELECT BOX RIÊNG, KHÔNG NẰM TRONG FORM FILTER -->
      <select id="sort" class="form-select form-select-sm d-inline-block w-auto">
        <option value="newest" <?= empty($_GET['sort']) || $_GET['sort'] == 'newest' ? 'selected' : '' ?>>Sản phẩm mới nhất</option>
        <option value="price_low" <?= (!empty($_GET['sort']) && $_GET['sort'] == 'price_low') ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
        <option value="price_high" <?= (!empty($_GET['sort']) && $_GET['sort'] == 'price_high') ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
        <option value="name" <?= (!empty($_GET['sort']) && $_GET['sort'] == 'name') ? 'selected' : '' ?>>Tên: A-Z</option>
      </select>
    </div>
  </div>

  <!-- Active Filters - Cập nhật tiếng Việt -->
  <?php if (!empty($_GET['category']) || !empty($_GET['tags']) || !empty($_GET['min_price']) || !empty($_GET['max_price'])): ?>
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
    
    <?php if (!empty($_GET['min_price']) && !empty($_GET['max_price'])): ?>
      <span class="badge bg-light text-dark ms-2">
        Giá: $<?= number_format($_GET['min_price'], 0) ?> - $<?= number_format($_GET['max_price'], 0) ?>
        <a href="<?= removePriceFilter() ?>" class="text-muted ms-1">×</a>
      </span>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <div class="row g-4">
    <?php if (!empty($products)): ?>
      <?php foreach ($products as $product): ?>
        <div class="col-md-4">
          <div class="card border-0 bg-transparent">
            <a href="<?= $base_url ?>index.php?action=detail&id=<?= $product['pro_id'] ?>">
              <img 
                src="<?= !empty($product['image_url']) ? $base_url . 'assets/images/products/' . basename($product['image_url']) : $base_url . 'assets/images/products/no-image.jpg' ?>" 
                class="card-img-top" 
                alt="<?= htmlspecialchars($product['name']) ?>" 
                style="height: 300px; object-fit: cover;"
                onerror="this.src='<?= $base_url ?>assets/images/products/no-image.jpg'"
              >
            </a>
            <div class="card-body text-center">
              <p class="mb-1 fw-bold"><?= htmlspecialchars($product['name']) ?></p>
              <!-- HIỂN THỊ GIÁ THẤP NHẤT -->
              <p class="text-muted mb-1">
                <?php if (isset($product['min_price'])): ?>
                  Từ $<?= number_format($product['min_price'], 2) ?>
                <?php else: ?>
                  Đang cập nhật giá
                <?php endif; ?>
              </p>
              <small class="text-muted">
                <?= htmlspecialchars($product['category_name'] ?? '') ?>
              </small>
              <?php if ($product['has_size']): ?>
                <br><small class="text-muted">Có nhiều kích thước</small>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
    <div class="alert alert-info text-center">
      <p class="mb-0">Không tìm thấy sản phẩm nào phù hợp với bộ lọc.</p>
      <a href="<?= $base_url ?>index.php?action=list" class="btn btn-outline-primary btn-sm mt-2">
        Xóa bộ lọc
      </a>
    </div>
  </div>
    <?php endif; ?>
  </div>
</section>
    </div>
  </div>
</main>

<?php 
// Kiểm tra xem footer có tồn tại không
$footerPath = __DIR__ . '/templates/footer.php';
if (file_exists($footerPath)) {
    include $footerPath;
} else {
    // Fallback footer với Bootstrap JS
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
// JavaScript cho sắp xếp - TÁCH RIÊNG KHỎI FORM FILTER
document.getElementById('sort').addEventListener('change', function() {
    const sortValue = this.value;
    
    // Tạo URL mới, giữ nguyên tất cả parameters hiện tại và chỉ cập nhật sort
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sortValue);
    
    // Chuyển hướng
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
}
</script>
