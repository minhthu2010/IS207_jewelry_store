<?php
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/sidebar.php';
include __DIR__ . '/templates/topbar.php';

// THÊM BASE URL CHO ADMIN
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/jewelry_website/";
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý sản phẩm</h1>
        <!-- Hiển thị thông báo -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <!-- Filter Section -->
    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-3">
            <label class="form-label">Tìm kiếm theo tên</label>
            <input type="text" name="search" class="form-control" 
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                   placeholder="Nhập tên sản phẩm...">
        </div>
        <div class="col-md-2">
            <label class="form-label">Danh mục</label>
            <select name="category_id" class="form-control">
                <option value="">Tất cả danh mục</option>
                <?php foreach($categories as $category): ?>
                    <option value="<?= $category['cate_id'] ?>" 
                        <?= ($_GET['category_id'] ?? '') == $category['cate_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Size</label>
            <select name="has_size" class="form-control">
                <option value="">Tất cả</option>
                <option value="1" <?= ($_GET['has_size'] ?? '') == '1' ? 'selected' : '' ?>>Có size</option>
                <option value="0" <?= ($_GET['has_size'] ?? '') == '0' ? 'selected' : '' ?>>Không có size</option>
            </select>
        </div>
        
        <!-- Thêm bộ lọc trạng thái -->
        <div class="col-md-2">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-control">
                <option value="">Tất cả</option>
                <option value="1" <?= ($_GET['status'] ?? '') == '1' ? 'selected' : '' ?>>Đang bán</option>
                <option value="0" <?= ($_GET['status'] ?? '') == '0' ? 'selected' : '' ?>>Ngừng bán</option>
            </select>
        </div>
        
        <div class="col-md-2">
            <label class="form-label">Tình trạng stock</label>
            <select name="stock_status" class="form-control">
                <option value="">Tất cả</option>
                <option value="in_stock" <?= ($_GET['stock_status'] ?? '') == 'in_stock' ? 'selected' : '' ?>>Còn hàng</option>
                <option value="low_stock" <?= ($_GET['stock_status'] ?? '') == 'low_stock' ? 'selected' : '' ?>>Sắp hết hàng</option>
                <option value="out_of_stock" <?= ($_GET['stock_status'] ?? '') == 'out_of_stock' ? 'selected' : '' ?>>Hết hàng</option>
            </select>
        </div>
        
        <div class="col-md-3">
            <label class="form-label">Khoảng giá</label>
            <div class="row g-2">
                <div class="col-6">
                    <input type="number" name="min_price" class="form-control" 
                           value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>" 
                           placeholder="Giá thấp nhất" min="0">
                </div>
                <div class="col-6">
                    <input type="number" name="max_price" class="form-control" 
                           value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" 
                           placeholder="Giá cao nhất" min="0">
                </div>
            </div>
        </div>
        
        <!-- HIỂN THỊ Ô NHẬP STOCK KHI CHỌN TÙY CHỈNH -->
        <div class="col-md-2" id="custom_stock_section" style="display: <?= ($_GET['stock_status'] ?? '') == 'custom' ? 'block' : 'none' ?>;">
            <label class="form-label">Số lượng stock</label>
            <input type="number" name="custom_stock" class="form-control" 
                   value="<?= htmlspecialchars($_GET['custom_stock'] ?? '') ?>" 
                   placeholder="Nhập số lượng" min="0">
        </div>
        
        <div class="col-md-2">
            <label class="form-label">Sắp xếp</label>
            <select name="sort_by" class="form-control">
                <option value="newest" <?= ($_GET['sort_by'] ?? '') == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                <option value="oldest" <?= ($_GET['sort_by'] ?? '') == 'oldest' ? 'selected' : '' ?>>Cũ nhất</option>
                <option value="name_asc" <?= ($_GET['sort_by'] ?? '') == 'name_asc' ? 'selected' : '' ?>>Tên A-Z</option>
                <option value="name_desc" <?= ($_GET['sort_by'] ?? '') == 'name_desc' ? 'selected' : '' ?>>Tên Z-A</option>
                <option value="price_asc" <?= ($_GET['sort_by'] ?? '') == 'price_asc' ? 'selected' : '' ?>>Giá thấp → cao</option>
                <option value="price_desc" <?= ($_GET['sort_by'] ?? '') == 'price_desc' ? 'selected' : '' ?>>Giá cao → thấp</option>
                <option value="stock_asc" <?= ($_GET['sort_by'] ?? '') == 'stock_asc' ? 'selected' : '' ?>>Stock ít → nhiều</option>
                <option value="stock_desc" <?= ($_GET['sort_by'] ?? '') == 'stock_desc' ? 'selected' : '' ?>>Stock nhiều → ít</option>
            </select>
        </div>
        
        <div class="col-12 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Lọc
            </button>
            <a href="products.php" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Làm mới
            </a>
            <small class="text-muted ms-2">
                <?php if (!empty($products)): ?>
                    Tìm thấy <?= count($products) ?> sản phẩm
                <?php endif; ?>
            </small>
        </div>
    </form>

    <!-- Products Table -->
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách sản phẩm</h6>
            <a href="products.php?action=create" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Thêm sản phẩm
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>Ảnh chính</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Có size?</th>
                        <th>Tổng stock</th>
                        <th>Giá thấp nhất</th>
                        <th>Giá cao nhất</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)) : ?>
                        <?php foreach ($products as $product) : ?>
                            <tr>
                                <td class="text-center">
                                    <?php if ($product['main_image']): ?>
                                        <img src="<?= $base_url . 'public/assets/images/products/' . htmlspecialchars($product['main_image']) ?>" 
                                            alt="<?= htmlspecialchars($product['name']) ?>" 
                                            style="width: 50px; height: 50px; object-fit: cover;" 
                                            class="rounded"
                                            onerror="this.src='<?= $base_url ?>public/assets/images/products/no-image.jpg'">
                                    <?php else: ?>
                                        <span class="text-muted">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($product['name']) ?></div>
                                    <small class="text-muted">ID: <?= $product['pro_id'] ?></small>
                                </td>
                                <td><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="badge <?= ($product['category_has_size'] ?? false) ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ($product['category_has_size'] ?? false) ? 'Có' : 'Không' ?>
                                    </span>
                                </td>
                                <td class="fw-bold <?= $product['total_stock'] > 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= number_format($product['total_stock'] ?? 0, 0, ',', '.') ?>
                                </td>
                                <td class="text-success fw-bold">
                                    <?= number_format($product['min_price'] ?? 0, 0, ',', '.') ?>₫
                                </td>
                                <td class="text-danger fw-bold">
                                    <?= number_format($product['max_price'] ?? 0, 0, ',', '.') ?>₫
                                </td>
                                <td>
                                    <span class="badge <?= $product['status'] == 1 ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $product['status'] == 1 ? 'Đang bán' : 'Ngừng bán' ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- CHỈ GIỮ LẠI NÚT SỬA, BỎ NÚT TOGGLE STATUS -->
                                    <a href="products.php?action=edit&id=<?= $product['pro_id'] ?>" 
                                    class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">Không có sản phẩm nào</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Hiển thị/ẩn ô nhập stock tùy chỉnh
document.addEventListener('DOMContentLoaded', function() {
    const stockStatusSelect = document.querySelector('select[name="stock_status"]');
    const customStockSection = document.getElementById('custom_stock_section');
    
    stockStatusSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customStockSection.style.display = 'block';
        } else {
            customStockSection.style.display = 'none';
        }
    });
});
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>
