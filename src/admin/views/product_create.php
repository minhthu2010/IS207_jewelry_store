e<?php
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/sidebar.php';
include __DIR__ . '/templates/topbar.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Thêm sản phẩm mới</h1>

    <form method="POST" action="products.php?action=store" id="productForm">
        <input type="hidden" name="action" value="create_product">

        <div class="card shadow">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#tab1" data-bs-toggle="tab">Thông tin sản phẩm</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Tab 1: Product Information -->
                    <div class="tab-pane fade show active" id="tab1">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Tên sản phẩm *</label>
                                    <input type="text" name="name" class="form-control" required 
                                           placeholder="Nhập tên sản phẩm">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mô tả</label>
                                    <textarea name="description" class="form-control" rows="3" 
                                              placeholder="Nhập mô tả sản phẩm"></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Danh mục *</label>
                                    <select name="category_id" class="form-control" required id="category_id">
                                        <option value="">Chọn danh mục</option>
                                        <?php foreach($categories as $category): ?>
                                            <option value="<?= $category['cate_id'] ?>" 
                                                    data-has-size="<?= $category['has_size'] ? '1' : '0' ?>">
                                                <?= htmlspecialchars($category['name']) ?>
                                                <?= $category['has_size'] ? ' (Có size)' : ' (Không size)' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Bảo hành</label>
                                    <select name="warranty_id" class="form-control">
                                        <option value="">Không bảo hành</option>
                                        <?php foreach($warranties as $warranty): ?>
                                            <option value="<?= $warranty['w_id'] ?>">
                                                <?= htmlspecialchars($warranty['description'] ?? 'Bảo hành ' . ($warranty['period'] ?? '0') . ' tháng') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tags</label>
                                    <input type="text" name="tags" class="form-control" 
                                           placeholder="Nhập tags, phân cách bằng dấu phẩy">
                                    <small class="text-muted">Ví dụ: tag1, tag2, tag3</small>
                                </div>
                                
                                <!-- Hiển thị thông tin về loại sản phẩm -->
                                <div class="alert alert-info" id="productTypeInfo">
                                    <small>
                                        <i class="fas fa-info-circle"></i>
                                        <span id="typeMessage">Vui lòng chọn danh mục để xem thông tin</span>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Fields for initial variant -->
                        <div class="border-top pt-3 mt-3">
                            <h6>Thông tin size đầu tiên</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">SKU *</label>
                                        <input type="text" name="sku" class="form-control" 
                                               placeholder="Mã SKU sản phẩm" required>
                                    </div>
                                </div>
                                <div class="col-md-3" id="sizeFieldContainer">
                                    <div class="mb-3">
                                        <label class="form-label">Size</label>
                                        <input type="text" name="size" class="form-control" 
                                               placeholder="Size (nếu có)" id="sizeField">
                                        <small class="text-muted" id="sizeHelp">Chỉ dành cho sản phẩm có size</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Giá *</label>
                                        <input type="number" name="price" class="form-control" 
                                               placeholder="0" min="0" step="1000" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Số lượng kho *</label>
                                        <input type="number" name="stock_quantity" class="form-control" 
                                               placeholder="0" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-warning" id="variantWarning">
                                <small>
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span id="warningMessage">Sản phẩm không có size - chỉ có thể có 1 biến thể duy nhất</span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Tạo sản phẩm
                </button>
                <a href="products.php" class="btn btn-secondary">Hủy</a>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    const sizeFieldContainer = document.getElementById('sizeFieldContainer');
    const sizeField = document.getElementById('sizeField');
    const sizeHelp = document.getElementById('sizeHelp');
    const productTypeInfo = document.getElementById('productTypeInfo');
    const typeMessage = document.getElementById('typeMessage');
    const variantWarning = document.getElementById('variantWarning');
    const warningMessage = document.getElementById('warningMessage');

    function updateProductType() {
        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        const hasSize = selectedOption.getAttribute('data-has-size') === '1';
        
        if (categorySelect.value === '') {
            productTypeInfo.style.display = 'block';
            variantWarning.style.display = 'none';
            typeMessage.textContent = 'Vui lòng chọn danh mục để xem thông tin';
            sizeFieldContainer.style.display = 'block';
            sizeField.required = false;
            return;
        }

        if (hasSize) {
            // Sản phẩm có size
            productTypeInfo.className = 'alert alert-success';
            typeMessage.textContent = 'Sản phẩm CÓ nhiều size - Có thể thêm nhiều biến thể với các size khác nhau';
            variantWarning.style.display = 'none';
            sizeFieldContainer.style.display = 'block';
            sizeField.required = true;
            sizeHelp.textContent = 'Nhập size';
        } else {
            // Sản phẩm không có size
            productTypeInfo.className = 'alert alert-info';
            typeMessage.textContent = 'Sản phẩm KHÔNG có size - Chỉ có 1 biến thể duy nhất';
            variantWarning.style.display = 'block';
            warningMessage.textContent = 'Sản phẩm không có size - chỉ có thể có 1 biến thể duy nhất';
            sizeFieldContainer.style.display = 'none';
            sizeField.required = false;
            sizeField.value = ''; // Clear size field
        }
    }

    categorySelect.addEventListener('change', updateProductType);
    
    // Initial update
    updateProductType();
});
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>