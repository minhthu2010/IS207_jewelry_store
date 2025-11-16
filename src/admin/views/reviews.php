<?php
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/sidebar.php';
include __DIR__ . '/templates/topbar.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý đánh giá sản phẩm</h1>

    <!-- Form filter -->
    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-3">
            <label class="form-label">Sản phẩm</label>
            <input type="text" name="product_name" class="form-control" placeholder="Tìm theo tên sản phẩm" 
                   value="<?= htmlspecialchars($_GET['product_name'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Khách hàng</label>
            <input type="text" name="customer" class="form-control" placeholder="Tìm theo tên/email khách hàng" 
                   value="<?= htmlspecialchars($_GET['customer'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Đánh giá</label>
            <select name="rating" class="form-control">
                <option value="">Tất cả rating</option>
                <option value="5" <?= (isset($_GET['rating']) && $_GET['rating'] == '5') ? 'selected' : '' ?>>⭐ 5 sao</option>
                <option value="4" <?= (isset($_GET['rating']) && $_GET['rating'] == '4') ? 'selected' : '' ?>>⭐ 4 sao</option>
                <option value="3" <?= (isset($_GET['rating']) && $_GET['rating'] == '3') ? 'selected' : '' ?>>⭐ 3 sao</option>
                <option value="2" <?= (isset($_GET['rating']) && $_GET['rating'] == '2') ? 'selected' : '' ?>>⭐ 2 sao</option>
                <option value="1" <?= (isset($_GET['rating']) && $_GET['rating'] == '1') ? 'selected' : '' ?>>⭐ 1 sao</option>
            </select>
        </div>
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-filter"></i> Lọc đánh giá
            </button>
            <a href="reviews.php" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Làm mới
            </a>
        </div>
    </form>

    <!-- Bảng reviews -->
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover" id="reviewsTable">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Sản phẩm</th>
                        <th>Khách hàng</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reviews)) : ?>
                        <?php foreach ($reviews as $review) : ?>
                            <tr>
                                <td><?= $review['review_id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($review['product_name']) ?></strong>
                                    <br><small class="text-muted">ID: <?= $review['product_id'] ?></small>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($review['customer_name']) ?></strong>
                                    <br><small class="text-muted"><?= htmlspecialchars($review['customer_email']) ?></small>
                                </td>
                                <td>
                                    <?php 
                                    $rating = $review['rating'];
                                    echo str_repeat('⭐', $rating) . " ($rating/5)";
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $comment = $review['comment'];
                                    if (strlen($comment) > 100) {
                                        echo htmlspecialchars(substr($comment, 0, 100)) . '...';
                                    } else {
                                        echo htmlspecialchars($comment);
                                    }
                                    ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($review['created_at'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-danger delete-review" 
                                            data-id="<?= $review['review_id'] ?>" 
                                            data-product="<?= htmlspecialchars($review['product_name']) ?>">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Không có đánh giá nào</td>
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

    // Xóa review
    document.querySelectorAll('.delete-review').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            
            const reviewId = this.dataset.id;
            const productName = this.dataset.product;
            
            Swal.fire({
                title: 'Xác nhận xóa',
                text: `Bạn có chắc chắn muốn xóa đánh giá của sản phẩm "${productName}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('reviews.php?action=delete&review_id=' + reviewId, { 
                        method: "GET" 
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Thành công!',
                                text: 'Đã xóa đánh giá thành công',
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
                            text: 'Có lỗi xảy ra khi xóa đánh giá',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        });
    });
});
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>