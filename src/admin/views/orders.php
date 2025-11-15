<?php
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/sidebar.php';
include __DIR__ . '/templates/topbar.php';
?>

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
                        <th>TRẠNG THÁI ĐƠN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)) : ?>
                        <?php foreach ($orders as $order) : ?>
                            <tr>
                                <td><strong>#<?= $order['order_id'] ?></strong></td>
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
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="update_payment_status">
                                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                        <select name="payment_status" class="form-select form-select-sm" 
                                                onchange="this.form.submit()"
                                                style="background-color: <?= 
                                                    $order['payment_status'] == 'success' ? '#d1edff' : 
                                                    ($order['payment_status'] == 'pending' ? '#fff3cd' : '#f8d7da') 
                                                ?>; color: #000; border: 1px solid #dee2e6;">
                                            <option value="pending" <?= $order['payment_status'] == 'pending' ? 'selected' : '' ?>>CHỜ THANH TOÁN</option>
                                            <option value="success" <?= $order['payment_status'] == 'success' ? 'selected' : '' ?>>THÀNH CÔNG</option>
                                            <option value="failed" <?= $order['payment_status'] == 'failed' ? 'selected' : '' ?>>THẤT BẠI</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="update_order_status">
                                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                        <select name="status" class="form-select form-select-sm" 
                                                onchange="this.form.submit()"
                                                style="background-color: <?= 
                                                    $order['status'] == 1 ? '#d1f2eb' : 
                                                    ($order['status'] == 2 ? '#f8d7da' : '#fff3cd') 
                                                ?>; color: #000; border: 1px solid #dee2e6;">
                                            <option value="0" <?= $order['status'] == 0 ? 'selected' : '' ?>>CHỜ XÁC NHẬN</option>
                                            <option value="1" <?= $order['status'] == 1 ? 'selected' : '' ?>>ĐÃ XÁC NHẬN</option>
                                            <option value="2" <?= $order['status'] == 2 ? 'selected' : '' ?>>ĐÃ HỦY</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Không có đơn hàng nào</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>