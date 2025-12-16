<?php 
// Kiểm tra và khởi động session nếu chưa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xác định base URL
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/jewelry_website/public/";
$searchQuery = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Xác định số lượng kết quả
$resultCount = !empty($searchResults) ? count($searchResults) : 0;

include __DIR__ . '/templates/header.php';
?>

<link rel="stylesheet" href="<?= $base_url ?>assets/css/search.css">

<main class="search-page py-4">
  <div class="container">
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav mb-4">
      <a href="<?= $base_url ?>index.php" class="breadcrumb-link">Trang chủ</a> &nbsp; › &nbsp;
      <span>Tìm kiếm</span>
    </nav>

    <!-- Search Header -->
    <div class="search-header mb-4">
      <h1 class="page-title mb-3">
        <?php if (!empty($searchQuery)): ?>
          Kết quả tìm kiếm cho: "<span class="search-query"><?= $searchQuery ?></span>"
        <?php else: ?>
          Tìm kiếm sản phẩm
        <?php endif; ?>
      </h1>
      
      <!-- Search Stats -->
      <div class="search-stats d-flex justify-content-between align-items-center mb-4">
        <div class="result-count">
          <?php if (!empty($searchQuery)): ?>
            <strong><?= $resultCount ?></strong> sản phẩm được tìm thấy
          <?php else: ?>
            <strong>Nhập từ khóa để tìm kiếm</strong>
          <?php endif; ?>
        </div>
        
        <!-- Dropdown sắp xếp (chỉ hiện khi có kết quả) -->
        <?php if ($resultCount > 0): ?>
        <div class="search-sort">
          <label for="sort" class="form-label me-2">Sắp xếp theo:</label>
          <select id="sort" class="form-select form-select-sm d-inline-block w-auto">
            <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Sản phẩm mới nhất</option>
            <option value="price_low" <?= $sort == 'price_low' ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
            <option value="price_high" <?= $sort == 'price_high' ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
            <option value="name" <?= $sort == 'name' ? 'selected' : '' ?>>Tên: A-Z</option>
          </select>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Search Results Section -->
    <section class="col-12">
      <?php if (!empty($searchQuery)): ?>
        <?php if ($resultCount > 0): ?>
          <!-- Có kết quả tìm kiếm - Hiển thị 4 sản phẩm mỗi hàng -->
          <div class="search-results">
            <div class="row g-4">
              <?php foreach ($searchResults as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
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
                            <span class="price-na">Đang cập nhật giá</span>
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
            </div>
          </div>
        <?php else: ?>
          <!-- Không có kết quả - Chỉ hiển thị thông báo và nút -->
          <div class="no-results text-center py-5">
            <div class="no-results-icon mb-4">
              <i class="fas fa-search fa-3x text-muted"></i>
            </div>
            <h3 class="mb-3">Không tìm thấy sản phẩm phù hợp</h3>
            <p class="text-muted mb-4">Không tìm thấy sản phẩm nào với từ khóa "<strong><?= $searchQuery ?></strong>"</p>
            
            <!-- Nút quay lại xem tất cả sản phẩm -->
            <div class="mt-4">
              <a href="<?= $base_url ?>index.php?action=list" class="btn btn-primary">
                <i class="fas fa-store me-2"></i>Xem tất cả sản phẩm
              </a>
            </div>
          </div>
        <?php endif; ?>
      <?php else: ?>
        <!-- Chưa nhập từ khóa -->
        <div class="empty-search text-center py-5">
          <div class="empty-search-icon mb-4">
            <i class="fas fa-search fa-3x text-muted"></i>
          </div>
          <h3 class="mb-3">Bạn muốn tìm gì?</h3>
          <p class="text-muted mb-4">Nhập từ khóa vào ô tìm kiếm để tìm sản phẩm bạn quan tâm</p>
        </div>
      <?php endif; ?>
    </section>
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

<script>
// JavaScript cho sắp xếp
document.getElementById('sort')?.addEventListener('change', function() {
    const sortValue = this.value;
    const url = new URL(window.location.href);
    
    // Thêm hoặc cập nhật tham số sort
    url.searchParams.set('sort', sortValue);
    
    // Chuyển hướng với sort mới
    window.location.href = url.toString();
});
</script>