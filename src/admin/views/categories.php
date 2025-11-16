<?php
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/sidebar.php';
include __DIR__ . '/templates/topbar.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý danh mục sản phẩm</h1>

    <!-- Form tìm kiếm -->
    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên danh mục" 
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
        </div>
        <div class="col-md-3">
            <a href="categories.php" class="btn btn-secondary w-100">Làm mới</a>
        </div>
    </form>

    <!-- Form thêm/sửa danh mục -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary" id="formTitle">Thêm danh mục mới</h6>
        </div>
        <div class="card-body">
            <!-- SỬA ACTION THÀNH categories.php?action=save -->
            <form id="categoryForm" method="POST" action="categories.php?action=save">
                <input type="hidden" name="cate_id" id="cate_id" value="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required 
                                value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="has_size" class="form-label">Có kích thước</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="has_size" name="has_size" value="1"
                                    <?= (isset($_POST['has_size']) && $_POST['has_size'] == '1') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="has_size">
                                    Sản phẩm trong danh mục này có kích thước
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="submitBtn">Thêm danh mục</button>
                    <button type="button" class="btn btn-secondary" id="cancelBtn" style="display: none;">Hủy</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách danh mục -->
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover" id="categoriesTable">
                <thead class="table-primary">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="40%">Tên danh mục</th>
                        <th width="20%">Có kích thước</th>
                        <th width="20%">Ngày cập nhật</th>
                        <th width="15%">Chức năng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)) : ?>
                        <?php foreach ($categories as $category) : ?>
                            <tr>
                                <td><?= $category['cate_id'] ?></td>
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td>
                                    <?php if ($category['has_size']) : ?>
                                        <span class="badge bg-success">Có</span>
                                    <?php else : ?>
                                        <span class="badge bg-secondary">Không</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($category['updated_at'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-category" 
                                            data-id="<?= $category['cate_id'] ?>" 
                                            data-name="<?= htmlspecialchars($category['name']) ?>"
                                            data-has-size="<?= $category['has_size'] ?>">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-category" 
                                            data-id="<?= $category['cate_id'] ?>"
                                            data-name="<?= htmlspecialchars($category['name']) ?>">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                <?= !empty($search) ? 'Không tìm thấy danh mục phù hợp' : 'Không có danh mục nào' ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM đã tải xong - JavaScript đang hoạt động");

    // Biến để theo dõi trạng thái form
    let isEditing = false;

    // Xử lý sự kiện submit form
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });

    // Xử lý sự kiện click nút sửa
    document.querySelectorAll('.edit-category').forEach(btn => {
        btn.addEventListener('click', function() {
            const categoryId = this.dataset.id;
            const categoryName = this.dataset.name;
            const hasSize = this.dataset.hasSize === '1';
            
            // Điền dữ liệu vào form
            document.getElementById('cate_id').value = categoryId;
            document.getElementById('name').value = categoryName;
            document.getElementById('has_size').checked = hasSize;
            
            // Thay đổi giao diện form
            document.getElementById('formTitle').textContent = 'Sửa danh mục';
            document.getElementById('submitBtn').textContent = 'Cập nhật danh mục';
            document.getElementById('cancelBtn').style.display = 'block';
            
            isEditing = true;
            
            // Cuộn lên đầu form
            document.getElementById('categoryForm').scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Xử lý sự kiện click nút hủy
    document.getElementById('cancelBtn').addEventListener('click', function() {
        resetForm();
    });

    // Xử lý sự kiện click nút xóa (SỬA LẠI PHẦN NÀY)
    document.querySelectorAll('.delete-category').forEach(btn => {
        btn.addEventListener('click', function() {
            const categoryId = this.dataset.id;
            const categoryName = this.dataset.name;
            
            Swal.fire({
                title: 'Xác nhận xóa',
                text: `Bạn có chắc chắn muốn xóa danh mục "${categoryName}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Gọi API xóa thông qua fetch
                    fetch(`categories.php?action=delete&cate_id=${categoryId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Thành công!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Lỗi!',
                                    text: data.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Lỗi:', error);
                            Swal.fire({
                                title: 'Lỗi!',
                                text: 'Có lỗi xảy ra khi xóa danh mục',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        });
                }
            });
        });
    });

    // Hàm validate form
    function validateForm() {
        const name = document.getElementById('name').value.trim();
        
        if (name === '') {
            Swal.fire({
                title: 'Lỗi!',
                text: 'Vui lòng nhập tên danh mục',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        }
        
        return true;
    }

    // Hàm reset form
    function resetForm() {
        document.getElementById('categoryForm').reset();
        document.getElementById('cate_id').value = '';
        document.getElementById('formTitle').textContent = 'Thêm danh mục mới';
        document.getElementById('submitBtn').textContent = 'Thêm danh mục';
        document.getElementById('cancelBtn').style.display = 'none';
        isEditing = false;
    }

    // Hiển thị thông báo từ server nếu có
    <?php if (isset($_SESSION['form_message'])): ?>
        Swal.fire({
            title: '<?= $_SESSION['form_message']['type'] === 'success' ? 'Thành công!' : 'Lỗi!' ?>',
            text: '<?= $_SESSION['form_message']['text'] ?>',
            icon: '<?= $_SESSION['form_message']['type'] ?>',
            confirmButtonText: 'OK'
        });
        <?php unset($_SESSION['form_message']); ?>
    <?php endif; ?>
});
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>