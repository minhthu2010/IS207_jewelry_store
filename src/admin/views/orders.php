<?php
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/sidebar.php';
include __DIR__ . '/templates/topbar.php';
?>
<link rel="stylesheet" href="../public/assets/css/style_admin_order.css">
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý đơn hàng</h1>
    <!-- Filter Section -->
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
                        <th>MÃ ĐƠN</th>
                        <th>KHÁCH HÀNG</th>
                        <th>NGÀY ĐẶT</th>
                        <th>TỔNG TIỀN</th>
                        <th>PTTT</th>
                        <th>TRẠNG THÁI TT</th>
                        <th>TRẠNG THÁI ĐƠN HÀNG</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)) : ?>
                        <?php foreach ($orders as $order) : 
                            // Xác định class màu cho select payment status
                            $payment_class = '';
                            switch($order['payment_status']) {
                                case 'pending': $payment_class = 'payment-pending'; break;
                                case 'success': $payment_class = 'payment-success'; break;
                                case 'failed': $payment_class = 'payment-failed'; break;
                            }
                        ?>
                            <tr>
                                <td>
                                    <?= $order['order_id'] ?>
                                </td>
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
                                    <form method="POST" class="d-inline update-payment-form" data-order-id="<?= $order['order_id'] ?>">
                                        <input type="hidden" name="action" value="update_payment_status">
                                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                        <select name="payment_status" class="form-select form-select-sm <?= $payment_class ?> payment-status-select" 
                                                data-current-status="<?= $order['payment_status'] ?>"
                                                data-order-id="<?= $order['order_id'] ?>">
                                            <option value="pending" <?= $order['payment_status'] == 'pending' ? 'selected' : '' ?>>CHỜ THANH TOÁN</option>
                                            <option value="success" <?= $order['payment_status'] == 'success' ? 'selected' : '' ?>>THÀNH CÔNG</option>
                                            <option value="failed" <?= $order['payment_status'] == 'failed' ? 'selected' : '' ?>>THẤT BẠI</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <?php
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
                                    ?>
                                    
                                    <span class="badge <?= $badge_class ?> p-2" style="font-size: 15px; min-width: 100px;">
                                        <?= $status_text ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
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
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý thay đổi trạng thái thanh toán với SweetAlert2
    document.querySelectorAll('.payment-status-select').forEach(select => {
        select.addEventListener('change', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const orderId = this.dataset.orderId;
            const currentStatus = this.dataset.currentStatus;
            const newStatus = this.value;
            
            // Lấy tên trạng thái để hiển thị
            const statusNames = {
                'pending': 'CHỜ THANH TOÁN',
                'success': 'THÀNH CÔNG',
                'failed': 'THẤT BẠI'
            };
            
            const currentStatusName = statusNames[currentStatus];
            const newStatusName = statusNames[newStatus];
            
            // Xác định trạng thái đơn hàng tương ứng
            let orderStatus = 0;
            switch(newStatus) {
                case 'success':
                    orderStatus = 1; // ĐÃ XÁC NHẬN
                    break;
                case 'failed':
                    orderStatus = 2; // ĐÃ HỦY
                    break;
                case 'pending':
                    orderStatus = 0; // CHỜ XÁC NHẬN
                    break;
            }
            
            const orderStatusNames = {
                0: 'CHỜ XÁC NHẬN',
                1: 'ĐÃ XÁC NHẬN', 
                2: 'ĐÃ HỦY'
            };
            const newOrderStatusName = orderStatusNames[orderStatus];
            
            Swal.fire({
                title: 'Xác nhận thay đổi trạng thái',
                html: `<div style="text-align: left;">
                        <p><strong>Đơn hàng:</strong> ${orderId}</p>
                        <p><strong>Trạng thái thanh toán:<span class="${newStatus === 'success' ? 'text-success' : (newStatus === 'failed' ? 'text-danger' : 'text-warning')}">${newStatusName}</span></p>
                        <p><strong>Trạng thái đơn hàng:</strong> Tự động chuyển sang <span class="${orderStatus === 1 ? 'text-success' : (orderStatus === 2 ? 'text-danger' : 'text-warning')}">${newOrderStatusName}</span></p>
                       </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2f8b1fff',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Đồng ý thay đổi',
                cancelButtonText: 'Hủy bỏ',
                reverseButtons: true,
                width: 500
            }).then((result) => {
                if (result.isConfirmed) {
                    // Gửi form
                    const form = this.closest('form');
                    form.submit();
                } else {
                    // Khôi phục giá trị cũ
                    this.value = currentStatus;
                }
            });
        });
    });
    
    // Tắt mặc định confirm của browser
    document.querySelectorAll('.update-payment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
        });
    });
});
</script>
<?php include __DIR__ . '/templates/footer.php'; ?>
