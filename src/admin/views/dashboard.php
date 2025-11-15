<?php
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/sidebar.php';
include __DIR__ . '/templates/topbar.php';
?>
<link rel="stylesheet" href="../public/assets/css/style_dashboard.css">

<div class="container-fluid dashboard-container">
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1> 

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Doanh thu -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Doanh thu <?= $filterMonth ? "Tháng $filterMonth" : '' ?> <?= $filterYear ?>
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?= number_format($revenue, 0, ',', '.') ?>₫
                    </div>
                </div>
            </div>
        </div>

        <!-- Tổng đơn hàng -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Tổng đơn hàng <?= $filterMonth ? "Tháng $filterMonth" : '' ?> <?= $filterYear ?>
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalOrders ?></div>
                </div>
            </div>
        </div>

        <!-- Tổng sản phẩm -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tổng sản phẩm</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalProducts ?></div>
                </div>
            </div>
        </div>

        <!-- Tổng khách hàng -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tổng khách hàng</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalCustomers ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4 filter-section">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tháng</label>
                            <select name="month" class="form-control">
                                <option value="">Tất cả tháng</option>
                                <?php for($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= sprintf('%02d', $i) ?>" 
                                        <?= $filterMonth == sprintf('%02d', $i) ? 'selected' : '' ?>>
                                        Tháng <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Năm</label>
                            <select name="year" class="form-control">
                                <option value="">Tất cả năm</option>
                                <?php foreach($years as $year): ?>
                                    <option value="<?= $year['year'] ?>" 
                                        <?= $filterYear == $year['year'] ? 'selected' : '' ?>>
                                        <?= $year['year'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Lọc đơn hàng
                            </button>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <a href="index.php" class="btn btn-secondary w-100"> <i class="fas fa-redo"></i> Làm mới</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <?= $chartTitle ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Danh sách đơn hàng <?= $filterMonth ? "tháng $filterMonth" : '' ?> <?= $filterYear ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>MÃ ĐƠN</th>
                                    <th>KHÁCH HÀNG</th>
                                    <th>NGÀY ĐẶT</th>
                                    <th>TỔNG TIỀN</th>
                                    <th>PTTT</th>
                                    <th>TRẠNG THÁI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentOrders)) : ?>
                                    <?php foreach ($recentOrders as $order) : ?>
                                        <tr>
                                            <td><strong>#<?= $order['order_id'] ?></strong></td>
                                            <td><?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                            <td class="price">
                                                <?= number_format((float)$order['total'], 0, ',', '.') ?>₫
                                            </td>
                                            <td><?= strtoupper(htmlspecialchars($order['payment_method'] ?? 'N/A')) ?></td>
                                            <td>
                                                <span class="status-text">
                                                    <?= 
                                                    $order['payment_status'] == 'success' ? 'THÀNH CÔNG' :
                                                    ($order['payment_status'] == 'pending' ? 'CHỜ THANH TOÁN' : 'THẤT BẠI')
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Không có đơn hàng nào</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = <?= json_encode(array_values($revenueChart)) ?>;
    const chartLabels = <?= json_encode($chartLabels) ?>;
    
    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Doanh thu (VND)',
                data: revenueData,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(value);
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>