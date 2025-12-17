<?php
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/sidebar.php';
include __DIR__ . '/templates/topbar.php';
?>
<link rel="stylesheet" href="../public/assets/css/style_admin_order.css">
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý đơn hàng</h1>
    
    <!-- Filter Section (giữ nguyên) -->
    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-2">
            <label class="form-label">Từ ngày</label>
            <input type="date" name="start_date" class="form-control" 
                   value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label">Đến ngày</label>
            <input type="date" name="end_date" class="form-control" 
                   value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-control">
                <option value="">Tất cả trạng thái</option>
                <?php foreach($statusLabels as $key => $label): ?>
                    <option value="<?= $key ?>" 
                        <?= ($_GET['status'] ?? '') == $key ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">PTTT</label>
            <select name="payment_method" class="form-control">
                <option value="">Tất cả PTTT</option>
                <?php foreach($paymentMethods as $method): ?>
                    <option value="<?= htmlspecialchars($method['payment_method']) ?>" 
                        <?= ($_GET['payment_method'] ?? '') == $method['payment_method'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($method['payment_method']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Tổng tiền từ</label>
            <input type="number" name="min_total" class="form-control" 
                   placeholder="0" value="<?= htmlspecialchars($_GET['min_total'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label">Tổng tiền đến</label>
            <input type="number" name="max_total" class="form-control" 
                   placeholder="100000000" value="<?= htmlspecialchars($_GET['max_total'] ?? '') ?>">
        </div>
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-filter"></i> Lọc đơn hàng
            </button>
            <a href="orders.php" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Làm mới
            </a>
        </div>
    </form>

    <!-- Orders Table -->
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>PTTT</th>
                        <th>Trạng thái TT</th>
                        <th>Trạng thái đơn hàng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)) : ?>
                        <?php foreach ($orders as $order) : 
                            // CHỈ HIỂN THỊ, KHÔNG THAY ĐỔI LOGIC
                            $payment_status = $order['payment_status']; // Giữ nguyên
                            
                            // Xác định class màu cho trạng thái thanh toán
                            $payment_class = '';
                            $payment_text = '';
                            
                            // LOGIC HIỂN THỊ CỦA BẠN - KHÔNG SỬA
                            switch($order['payment_status']) {
                                case 'pending': 
                                    $payment_class = 'payment-pending'; 
                                    $payment_text = 'CHỜ THANH TOÁN';
                                    break;
                                case 'success': 
                                    $payment_class = 'payment-success'; 
                                    $payment_text = 'ĐÃ THANH TOÁN';
                                    break;
                                case 'failed': 
                                    $payment_class = 'payment-failed'; 
                                    $payment_text = 'THẤT BẠI';
                                    break;
                            }
                            
                            // Xác định class màu cho trạng thái đơn hàng
                            $badge_class = '';
                            $status_text = '';
                            switch($order['status']) {
                                case 1:
                                    $badge_class = 'badge-success-custom';
                                    $status_text = 'ĐÃ XÁC NHẬN';
                                    break;
                                case 2:
                                    $badge_class = 'badge-danger-custom';
                                    $status_text = 'ĐÃ HỦY';
                                    break;
                                default:
                                    $badge_class = 'badge-warning-custom';
                                    $status_text = 'CHỜ XÁC NHẬN';
                                    break;
                            }
                            
                            // Kiểm tra có hiển thị nút "Xác nhận đã thu tiền COD" không
                            $show_cod_button = ($order['payment_method'] == 'cod' && 
                                                $order['payment_status'] == 'pending' && 
                                                $order['status'] == 1);
                        ?>
                            <tr>
                                <td><?= $order['order_id'] ?></td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($order['customer_email'] ?? '') ?></small>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                <td class="fw-bold text-success">
                                    <?= number_format((float)$order['total'], 0, ',', '.') ?>₫
                                </td>
                                <td><?= strtoupper(htmlspecialchars($order['payment_method'] ?? 'N/A')) ?></td>
                                <td>
                                    <!-- HIỂN THỊ trạng thái thanh toán, KHÔNG cho chỉnh -->
                                    <span class="badge <?= $payment_class ?> p-2">
                                        <?= $payment_text ?>
                                    </span>
                                    
                                    <!-- GHI CHÚ KHI ĐƠN ĐÃ HỦY -->
                                    <?php if ($order['status'] == 2): ?>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            <?php 
                                            // HIỂN THỊ TRẠNG THÁI THANH TOÁN TRƯỚC ĐÓ
                                            if ($order['payment_status'] == 'pending') {
                                                echo 'COD - chưa thu tiền trước khi hủy';
                                            } elseif ($order['payment_status'] == 'success') {
                                                echo 'Đã thanh toán trước khi hủy';
                                            } elseif ($order['payment_status'] == 'failed') {
                                                echo 'Thanh toán thất bại trước khi hủy';
                                            }
                                            ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <form method="POST" class="update-order-form">
                                        <input type="hidden" name="action" value="update_order_status">
                                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                        
                                        <?php if ($order['status'] == 0): // CHỜ XÁC NHẬN ?>
                                            <select name="order_status" class="form-select form-select-sm" 
                                                    onchange="confirmStatusChange(this, <?= $order['order_id'] ?>)">
                                                <option value="0" selected>CHỜ XÁC NHẬN</option>
                                                <option value="1">ĐÃ XÁC NHẬN</option>
                                                <option value="2">ĐÃ HỦY</option>
                                            </select>
                                            
                                        <?php elseif ($order['status'] == 1): // ĐÃ XÁC NHẬN ?>
                                            <select name="order_status" class="form-select form-select-sm" 
                                                    onchange="confirmStatusChange(this, <?= $order['order_id'] ?>)">
                                                <option value="1" selected>ĐÃ XÁC NHẬN</option>
                                                <option value="2">ĐÃ HỦY</option>
                                                <!-- KHÔNG CÓ option quay về CHỜ XÁC NHẬN -->
                                            </select>
                                            
                                        <?php elseif ($order['status'] == 2): // ĐÃ HỦY ?>
                                            <!-- CHỈ HIỂN THỊ, KHÔNG CHO CHỌN -->
                                            <span class="badge badge-danger-custom p-2">
                                                ĐÃ HỦY <i class="fas fa-lock ms-1"></i>
                                            </span>
                                            <input type="hidden" name="order_status" value="2">
                                        <?php endif; ?>
                                    </form>
                                </td>
                                <td>
                                    <!-- NÚT XÁC NHẬN THU TIỀN COD -->
                                    <?php if ($show_cod_button): ?>
                                        <form method="POST" class="d-inline cod-payment-form">
                                            <input type="hidden" name="action" value="confirm_cod_payment">
                                            <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-check-circle"></i> Xác nhận đã thu tiền
                                            </button>
                                        </form>
                                    <?php elseif ($order['payment_method'] == 'cod' && $order['payment_status'] == 'pending'): ?>
                                        <button class="btn btn-secondary btn-sm" disabled title="Chỉ xác nhận khi đơn ở trạng thái ĐÃ XÁC NHẬN">
                                            <i class="fas fa-lock"></i> Chờ xác nhận đơn
                                        </button>
                                    <?php endif; ?>
                                </td>                                
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                Không có đơn hàng nào
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmStatusChange(select, orderId) {
    const newStatus = select.value;
    const currentStatus = select.closest('form').querySelector('option[selected]')?.value || '0';
    
    // KHÔNG cho hủy nếu đang ở trạng thái hủy
    if (currentStatus == '2') {
        select.value = '2'; // Giữ nguyên
        Swal.fire({
            title: 'Không thể thay đổi!',
            text: 'Đơn hàng đã hủy không thể thay đổi trạng thái.',
            icon: 'warning'
        });
        return;
    }
    
    // KHÔNG cho quay về "Chờ xác nhận" nếu đang "Đã xác nhận"
    if (currentStatus == '1' && newStatus == '0') {
        select.value = '1'; // Quay về "Đã xác nhận"
        Swal.fire({
            title: 'Không thể quay lại!',
            text: 'Không thể chuyển từ "Đã xác nhận" về "Chờ xác nhận".',
            icon: 'warning'
        });
        return;
    }
    
    const statusNames = {
        '0': 'CHỜ XÁC NHẬN',
        '1': 'ĐÃ XÁC NHẬN', 
        '2': 'ĐÃ HỦY'
    };
    
    Swal.fire({
        title: 'Xác nhận thay đổi?',
        html: `<p>Đơn hàng <strong>#${orderId}</strong></p>
               <p>Từ: <span class="text-warning">${statusNames[currentStatus]}</span></p>
               <p>Thành: <span class="${newStatus == 1 ? 'text-success' : 'text-danger'}">${statusNames[newStatus]}</span></p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#298a40',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Xác nhận',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            select.closest('form').submit();
        } else {
            select.value = currentStatus; // Khôi phục giá trị cũ
        }
    });
}

// Xử lý xác nhận thu tiền COD (AJAX)
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cod-payment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const orderId = formData.get('order_id');
            
            Swal.fire({
                title: 'Xác nhận đã thu tiền COD?',
                html: `<p>Đơn hàng: <strong>#${orderId}</strong></p>
                       <p class="text-success">Trạng thái thanh toán sẽ chuyển thành <strong>ĐÃ THANH TOÁN</strong></p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#298a40',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Gửi AJAX
                    fetch('orders.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Thành công!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#298a40'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Lỗi!',
                                text: data.message,
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Lỗi kết nối!',
                            text: 'Không thể kết nối đến server',
                            icon: 'error'
                        });
                    });
                }
            });
        });
    });
});
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>
