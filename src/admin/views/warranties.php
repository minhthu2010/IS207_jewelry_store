<?php
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/sidebar.php';
include __DIR__ . '/templates/topbar.php';

?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý bảo hành</h1>

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

    <!-- Form filter -->
    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-3">
            <label class="form-label">Thời hạn (tháng)</label>
            <input type="number" name="period" class="form-control" placeholder="Tìm theo thời hạn" 
                   value="<?= htmlspecialchars($_GET['period'] ?? '') ?>" min="1">
        </div>
        <div class="col-md-3">
            <label class="form-label">Mô tả</label>
            <input type="text" name="description" class="form-control" placeholder="Tìm theo mô tả" 
                   value="<?= htmlspecialchars($_GET['description'] ?? '') ?>">
        </div>
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-filter"></i> Lọc bảo hành
            </button>
            <a href="warranties.php" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Làm mới
            </a>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addWarrantyModal">
                <i class="fas fa-plus"></i> Thêm bảo hành
            </button>
        </div>
    </form>

    <!-- Bảng warranties -->
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover" id="warrantiesTable">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Thời hạn (tháng)</th>
                        <th>Mô tả</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($warranties)) : ?>
                        <?php foreach ($warranties as $warranty) : ?>
                            <tr>
                                <td><?= $warranty['w_id'] ?></td>
                                <td class="fw-bold"><?= $warranty['period'] ?> tháng</td>
                                <td><?= htmlspecialchars($warranty['description']) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-sm btn-warning edit-warranty" 
                                                data-id="<?= $warranty['w_id'] ?>"
                                                data-period="<?= $warranty['period'] ?>"
                                                data-description="<?= htmlspecialchars($warranty['description']) ?>">
                                            <i class="fas fa-edit"></i> Sửa
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-warranty" 
                                                data-id="<?= $warranty['w_id'] ?>" 
                                                data-period="<?= $warranty['period'] ?>">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Không có bảo hành nào</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Thêm bảo hành -->
<div class="modal fade" id="addWarrantyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm bảo hành mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="warranties.php?action=create">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_warranty">
                    <div class="mb-3">
                        <label class="form-label">Thời hạn (tháng) *</label>
                        <input type="number" name="period" class="form-control" 
                               placeholder="Nhập số tháng" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả *</label>
                        <textarea name="description" class="form-control" 
                                  placeholder="Nhập mô tả bảo hành" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm bảo hành</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa bảo hành -->
<div class="modal fade" id="editWarrantyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa bảo hành</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editWarrantyForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_warranty">
                    <input type="hidden" name="w_id" id="edit_warranty_id">
                    <div class="mb-3">
                        <label class="form-label">Thời hạn (tháng) *</label>
                        <input type="number" name="period" id="edit_period" class="form-control" 
                               placeholder="Nhập số tháng" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả *</label>
                        <textarea name="description" id="edit_description" class="form-control" 
                                  placeholder="Nhập mô tả bảo hành" rows="3" required></textarea>
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
document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM đã tải xong - JavaScript đang hoạt động");

    // Xóa bảo hành
    document.querySelectorAll('.delete-warranty').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            
            const warrantyId = this.dataset.id;
            const warrantyPeriod = this.dataset.period;
            
            Swal.fire({
                title: 'Xác nhận xóa',
                text: `Bạn có chắc chắn muốn xóa bảo hành ${warrantyPeriod} tháng không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('warranties.php?action=delete&w_id=' + warrantyId, { 
                        method: "GET" 
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Thành công!',
                                text: 'Đã xóa bảo hành thành công',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Lỗi!',
                                text: 'Không thể xóa: ' + data.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(err => {
                        console.error("Lỗi:", err);
                        Swal.fire({
                            title: 'Lỗi!',
                            text: 'Có lỗi xảy ra khi xóa bảo hành',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        });
    });

    // Sửa bảo hành
    document.querySelectorAll('.edit-warranty').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            
            const warrantyId = this.dataset.id;
            const warrantyPeriod = this.dataset.period;
            const warrantyDescription = this.dataset.description;
            
            // Điền dữ liệu vào form
            document.getElementById('edit_warranty_id').value = warrantyId;
            document.getElementById('edit_period').value = warrantyPeriod;
            document.getElementById('edit_description').value = warrantyDescription;
            
            // Cập nhật action của form
            document.getElementById('editWarrantyForm').action = 'warranties.php?action=update&id=' + warrantyId;
            
            // Hiển thị modal
            const editModal = new bootstrap.Modal(document.getElementById('editWarrantyModal'));
            editModal.show();
        });
    });
});
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>