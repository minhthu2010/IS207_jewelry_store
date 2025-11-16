<?php
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/sidebar.php';
include __DIR__ . '/templates/topbar.php';

$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/jewelry_website/";
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Sửa sản phẩm: <?= htmlspecialchars($product['name']) ?></h1>

    <div class="card shadow">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#tab1" data-bs-toggle="tab">Thông tin sản phẩm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tab2" data-bs-toggle="tab">Phiên bản sản phẩm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tab3" data-bs-toggle="tab">Ảnh sản phẩm</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <!-- Tab 1: Product Information -->
                <div class="tab-pane fade show active" id="tab1">
                    <form method="POST" action="products.php?action=update&id=<?= $product['pro_id'] ?>" id="mainProductForm">
                        <input type="hidden" name="action" value="update_product">
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Tên sản phẩm *</label>
                                    <input type="text" name="name" class="form-control" required 
                                           value="<?= htmlspecialchars($product['name']) ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mô tả</label>
                                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Danh mục *</label>
                                    <select name="category_id" class="form-control" required>
                                        <option value="">Chọn danh mục</option>
                                        <?php foreach($categories as $category): ?>
                                            <option value="<?= $category['cate_id'] ?>" 
                                                <?= $product['category_id'] == $category['cate_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Bảo hành</label>
                                    <select name="warranty_id" class="form-control">
                                        <option value="">Không bảo hành</option>
                                        <?php foreach($warranties as $warranty): ?>
                                            <option value="<?= $warranty['w_id'] ?>" 
                                                <?= $product['warranty_id'] == $warranty['w_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($warranty['description'] ?? 'Bảo hành ' . ($warranty['period'] ?? '0') . ' tháng') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tags</label>
                                    <?php
                                    $tag_names = array_column($tags, 'tag_name');
                                    $tags_string = implode(', ', $tag_names);
                                    ?>
                                    <input type="text" name="tags" class="form-control" 
                                           value="<?= htmlspecialchars($tags_string) ?>"
                                           placeholder="Nhập tags, phân cách bằng dấu phẩy">
                                    <small class="text-muted">Ví dụ: tag1, tag2, tag3</small>
                                </div>
                                <div class="form-check mb-3">
                                    <input type="checkbox" name="has_size" value="1" class="form-check-input" id="has_size"
                                        <?= ($product['category_has_size'] ?? false) ? 'checked' : '' ?> disabled>
                                    <label class="form-check-label" for="has_size">Sản phẩm có nhiều size</label>
                                    <small class="text-muted d-block">(Xác định bởi danh mục - Không thể thay đổi)</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật thông tin sản phẩm
                            </button>
                            <a href="products.php" class="btn btn-secondary">Quay lại danh sách</a>
                        </div>
                    </form>
                </div>

                <!-- Tab 2: Variants -->
                <div class="tab-pane fade" id="tab2">
                    <?php if (count($variants) > 1 || $product['category_has_size']): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>Quản lý Phiên bản sản phẩm</h5>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVariantModal">
                                <i class="fas fa-plus"></i> Thêm Phiên bản
                            </button>
                        </div>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Size</th>
                                    <th>SKU</th>
                                    <th>Giá</th>
                                    <th>Số lượng kho</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($variants as $variant): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($variant['size'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($variant['sku']) ?></td>
                                        <td class="fw-bold text-success">
                                            <?= number_format($variant['price'], 0, ',', '.') ?>₫
                                        </td>
                                        <td class="<?= $variant['stock_quantity'] > 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= number_format($variant['stock_quantity'], 0, ',', '.') ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editVariantModal"
                                                    data-variant-id="<?= $variant['variant_id'] ?>"
                                                    data-size="<?= htmlspecialchars($variant['size'] ?? '') ?>"
                                                    data-sku="<?= htmlspecialchars($variant['sku']) ?>"
                                                    data-price="<?= $variant['price'] ?>"
                                                    data-stock="<?= $variant['stock_quantity'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" action="products.php?action=delete_variant&id=<?= $variant['variant_id'] ?>" 
                                                  class="d-inline" onsubmit="return confirm('Xóa Phiên bản sản phẩm này?')">
                                                <input type="hidden" name="action" value="delete_variant">
                                                <input type="hidden" name="product_id" value="<?= $product['pro_id'] ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <?php $variant = $variants[0] ?? null; ?>
                        <?php if ($variant): ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">SKU</label>
                                        <input type="text" value="<?= htmlspecialchars($variant['sku']) ?>" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Giá</label>
                                        <input type="text" value="<?= number_format($variant['price'], 0, ',', '.') ?>₫" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Số lượng kho</label>
                                        <input type="text" value="<?= number_format($variant['stock_quantity'], 0, ',', '.') ?>" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">Sản phẩm không có size - Thông tin phiên bản sản phẩm được tạo tự động</small>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Tab 3: Images -->
                <div class="tab-pane fade" id="tab3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Quản lý Ảnh sản phẩm</h5>
                        <div>
                            <button type="button" class="btn btn-success btn-sm" id="saveSortOrderBtn" style="display: none;">
                                <i class="fas fa-save"></i> Lưu thứ tự
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="editSortOrderBtn">
                                <i class="fas fa-sort"></i> Sắp xếp ảnh
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <form method="POST" action="products.php?action=add_image" enctype="multipart/form-data" id="uploadForm">
                            <input type="hidden" name="action" value="add_image">
                            <input type="hidden" name="product_id" value="<?= $product['pro_id'] ?>">
                            <div class="input-group">
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Tải lên
                                </button>
                            </div>
                            <small class="text-muted">Chấp nhận: JPEG, PNG, GIF, WebP (Tối đa 2MB)</small>
                        </form>
                    </div>

                    <!-- Form để lưu thứ tự ảnh -->
                    <form method="POST" action="products.php?action=update_image_sort_orders" id="sortOrderForm" style="display: none;">
                        <input type="hidden" name="action" value="update_image_sort_orders">
                        <input type="hidden" name="product_id" value="<?= $product['pro_id'] ?>">
                        <div id="sortOrderInputs"></div>
                    </form>

                    <div class="row" id="imageSortable">
                        <?php foreach ($images as $index => $image): ?>
                            <div class="col-md-3 mb-3 image-item" data-image-id="<?= $image['image_id'] ?>">
                                <div class="card">
                                    <img src="<?= $base_url . 'public/assets/images/products/' . basename($image['image_url']) ?>" 
                                        class="card-img-top" 
                                        style="height: 200px; object-fit: cover;"
                                        alt="Product Image"
                                        onerror="this.src='<?= $base_url ?>public/assets/images/products/no-image.jpg'">
                                    <div class="card-body text-center">
                                        <!-- Hiển thị thứ tự khi đang chỉnh sửa -->
                                        <div class="sort-order-input mb-2" style="display: none;">
                                            <label class="form-label small">Thứ tự:</label>
                                            <input type="number" class="form-control form-control-sm sort-order" 
                                                value="<?= $image['sort_order'] ?>" min="0"
                                                data-image-id="<?= $image['image_id'] ?>">
                                        </div>
                                        
                                        <!-- Hiển thị thông tin bình thường -->
                                        <div class="normal-display">
                                            <!-- Trong phần hiển thị ảnh, sửa điều kiện hiển thị ảnh chính -->
                                            <?php if ($image['sort_order'] == 0): ?>
                                                <span class="badge bg-success mb-2">
                                                    <i class="fas fa-star"></i> Ảnh chính <br> (Thứ tự: 0)
                                                </span>
                                                <br>
                                                <small class="text-muted">Thứ tự: <?= $image['sort_order'] ?></small>
                                            <?php else: ?>
                                                <small class="text-muted">Thứ tự: <?= $image['sort_order'] ?></small>
                                                <br>
                                                <form method="POST" action="products.php?action=set_main_image&id=<?= $image['image_id'] ?>" class="d-inline">
                                                    <input type="hidden" name="action" value="set_main_image">
                                                    <input type="hidden" name="product_id" value="<?= $product['pro_id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary mt-1">
                                                        <i class="fas fa-star"></i> Đặt làm chính
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="btn-group btn-group-sm mt-2">
                                            <button type="button" class="btn btn-outline-secondary btn-move" title="Di chuyển" style="display: none;">
                                                <i class="fas fa-arrows-alt"></i>
                                            </button>
                                            <form method="POST" action="products.php?action=delete_image&id=<?= $image['image_id'] ?>" 
                                                class="d-inline" onsubmit="return confirm('Xóa ảnh này?')">
                                                <input type="hidden" name="action" value="delete_image">
                                                <input type="hidden" name="product_id" value="<?= $product['pro_id'] ?>">
                                                <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($images)): ?>
                            <div class="col-12 text-center text-muted">
                                <p>Chưa có ảnh nào được tải lên</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Variant Modal -->
<div class="modal fade" id="addVariantModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="products.php?action=add_variant">
                <input type="hidden" name="action" value="add_variant">
                <input type="hidden" name="product_id" value="<?= $product['pro_id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm Phiên bản sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Size *</label>
                        <input type="text" name="size" class="form-control" required placeholder="16, 17, 18...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SKU *</label>
                        <input type="text" name="sku" class="form-control" required placeholder="Mã SKU">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giá *</label>
                        <input type="number" name="price" class="form-control" required min="0" step="1000" placeholder="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số lượng kho *</label>
                        <input type="number" name="stock_quantity" class="form-control" required min="0" placeholder="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Variant Modal -->
<div class="modal fade" id="editVariantModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="products.php?action=update_variant&id=0" id="editVariantForm">
                <input type="hidden" name="action" value="update_variant">
                <input type="hidden" name="product_id" value="<?= $product['pro_id'] ?>">
                <input type="hidden" name="variant_id" id="edit_variant_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa Phiên bản sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Size *</label>
                        <input type="text" name="size" id="edit_size" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SKU *</label>
                        <input type="text" name="sku" id="edit_sku" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giá *</label>
                        <input type="number" name="price" id="edit_price" class="form-control" required min="0" step="1000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số lượng kho *</label>
                        <input type="number" name="stock_quantity" id="edit_stock" class="form-control" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabLinks = document.querySelectorAll('.card-header-tabs .nav-link');
    
    tabLinks.forEach(link => {
        link.addEventListener('click', function() {
            tabLinks.forEach(tab => {
                tab.classList.remove('active');
                tab.classList.remove('text-dark', 'fw-bold');
                tab.classList.add('text-secondary');
            });
            
            this.classList.add('active');
            this.classList.remove('text-secondary');
            this.classList.add('text-dark', 'fw-bold');
        });
    });
    
    const urlHash = window.location.hash;
    if (urlHash) {
        const tabTrigger = document.querySelector(`[href="${urlHash}"]`);
        if (tabTrigger) {
            tabLinks.forEach(tab => {
                tab.classList.remove('active');
                tab.classList.remove('text-dark', 'fw-bold');
                tab.classList.add('text-secondary');
            });
            
            tabTrigger.classList.add('active');
            tabTrigger.classList.remove('text-secondary');
            tabTrigger.classList.add('text-dark', 'fw-bold');
            
            new bootstrap.Tab(tabTrigger).show();
        }
    }

    const editVariantModal = document.getElementById('editVariantModal');
    if (editVariantModal) {
        editVariantModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const variantId = button.getAttribute('data-variant-id');
            const size = button.getAttribute('data-size');
            const sku = button.getAttribute('data-sku');
            const price = button.getAttribute('data-price');
            const stock = button.getAttribute('data-stock');

            document.getElementById('edit_variant_id').value = variantId;
            document.getElementById('edit_size').value = size;
            document.getElementById('edit_sku').value = sku;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_stock').value = stock;

            document.getElementById('editVariantForm').action = `products.php?action=update_variant&id=${variantId}`;
        });
    }

    if (urlHash) {
        const tabTrigger = document.querySelector(`[href="${urlHash}"]`);
        if (tabTrigger) {
            new bootstrap.Tab(tabTrigger).show();
        }
    }
});


// Sort order functionality
let isEditingSortOrder = false;

document.getElementById('editSortOrderBtn').addEventListener('click', function() {
    isEditingSortOrder = !isEditingSortOrder;
    toggleSortOrderEdit();
});

document.getElementById('saveSortOrderBtn').addEventListener('click', function() {
    saveSortOrders();
});

function toggleSortOrderEdit() {
    const sortInputs = document.querySelectorAll('.sort-order-input');
    const normalDisplays = document.querySelectorAll('.normal-display');
    const moveButtons = document.querySelectorAll('.btn-move');
    const saveBtn = document.getElementById('saveSortOrderBtn');
    const editBtn = document.getElementById('editSortOrderBtn');
    
    if (isEditingSortOrder) {
        // Enter edit mode
        sortInputs.forEach(input => input.style.display = 'block');
        normalDisplays.forEach(display => display.style.display = 'none');
        moveButtons.forEach(btn => btn.style.display = 'inline-block');
        saveBtn.style.display = 'inline-block';
        editBtn.innerHTML = '<i class="fas fa-times"></i> Hủy sắp xếp';
        editBtn.classList.remove('btn-secondary');
        editBtn.classList.add('btn-warning');
        
        // Initialize sortable
        initSortable();
    } else {
        // Exit edit mode
        sortInputs.forEach(input => input.style.display = 'none');
        normalDisplays.forEach(display => display.style.display = 'block');
        moveButtons.forEach(btn => btn.style.display = 'none');
        saveBtn.style.display = 'none';
        editBtn.innerHTML = '<i class="fas fa-sort"></i> Sắp xếp ảnh';
        editBtn.classList.remove('btn-warning');
        editBtn.classList.add('btn-secondary');
        
        // Destroy sortable
        destroySortable();
    }
}

function initSortable() {
    const sortable = document.getElementById('imageSortable');
    
    sortable.addEventListener('dragstart', function(e) {
        if (e.target.classList.contains('image-item')) {
            e.target.classList.add('dragging');
        }
    });
    
    sortable.addEventListener('dragend', function(e) {
        if (e.target.classList.contains('image-item')) {
            e.target.classList.remove('dragging');
            updateSortOrderFromPosition();
        }
    });
    
    // Add drag and drop functionality
    const items = document.querySelectorAll('.image-item');
    items.forEach(item => {
        item.setAttribute('draggable', 'true');
        
        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            const dragging = document.querySelector('.dragging');
            if (dragging && dragging !== this) {
                const rect = this.getBoundingClientRect();
                const midY = rect.top + rect.height / 2;
                
                if (e.clientY < midY) {
                    this.parentNode.insertBefore(dragging, this);
                } else {
                    this.parentNode.insertBefore(dragging, this.nextSibling);
                }
            }
        });
    });
}

function destroySortable() {
    const items = document.querySelectorAll('.image-item');
    items.forEach(item => {
        item.setAttribute('draggable', 'false');
        item.classList.remove('dragging');
    });
}

function updateSortOrderFromPosition() {
    const items = document.querySelectorAll('.image-item');
    
    // Tìm ảnh chính hiện tại
    let mainImageFound = false;
    
    items.forEach((item, index) => {
        const input = item.querySelector('.sort-order');
        if (input) {
            const currentValue = parseInt(input.value);
            
            // Nếu là ảnh đầu tiên trong danh sách và chưa có ảnh chính
            if (index === 0 && !mainImageFound) {
                input.value = 0; // Đặt làm ảnh chính
                mainImageFound = true;
            } 
            // Nếu đã có ảnh chính và ảnh này trước đó là ảnh chính
            else if (currentValue === 0 && mainImageFound) {
                input.value = index; // Chuyển thành ảnh phụ
            }
            // Nếu đã có ảnh chính và ảnh này là ảnh phụ
            else if (mainImageFound) {
                input.value = index;
            }
            // Nếu chưa có ảnh chính và ảnh này không phải là đầu tiên
            else if (!mainImageFound && currentValue === 0) {
                // Giữ nguyên là ảnh chính
                mainImageFound = true;
            }
            // Các trường hợp còn lại
            else {
                input.value = index;
            }
        }
    });
}

function saveSortOrders() {
    const sortInputs = document.querySelectorAll('.sort-order');
    const sortOrderInputs = document.getElementById('sortOrderInputs');
    
    // Clear previous inputs
    sortOrderInputs.innerHTML = '';
    
    // Create hidden inputs for each image's sort order
    sortInputs.forEach(input => {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = `sort_orders[${input.dataset.imageId}]`;
        hiddenInput.value = input.value;
        sortOrderInputs.appendChild(hiddenInput);
    });
    
    // Submit the form
    document.getElementById('sortOrderForm').submit();
}

// Add CSS for drag and drop
const style = document.createElement('style');
style.textContent = `
    .image-item.dragging {
        opacity: 0.5;
        border: 2px dashed #007bff;
    }
    .image-item {
        transition: all 0.3s ease;
        cursor: move;
    }
    .sort-order-input input {
        max-width: 80px;
        margin: 0 auto;
    }
`;
document.head.appendChild(style);
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>