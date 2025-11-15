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
                        Doanh thu <?= $statisticsTitle ?>
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
                        Tổng đơn hàng <?= $statisticsTitle ?>
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
                    <form method="GET" class="row g-3" id="filterForm">
                        <div class="col-md-3">
                            <label class="form-label">Loại lọc</label>
                            <select name="filter_type" class="form-control" id="filterType">
                                <option value="">-- Chọn loại lọc --</option>
                                <option value="month" <?= isset($_GET['filter_type']) && $_GET['filter_type'] == 'month' ? 'selected' : '' ?>>Theo tháng</option>
                                <option value="year" <?= isset($_GET['filter_type']) && $_GET['filter_type'] == 'year' ? 'selected' : '' ?>>Theo năm</option>
                                <option value="month_year" <?= isset($_GET['filter_type']) && $_GET['filter_type'] == 'month_year' ? 'selected' : '' ?>>Theo tháng và năm</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tháng</label>
                            <select name="month" class="form-control" id="monthSelect" disabled>
                                <option value="">Chọn tháng</option>
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
                            <select name="year" class="form-control" id="yearSelect" disabled>
                                <option value="">Chọn năm</option>
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
                            <button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled>
                                <i class="fas fa-filter"></i> Lọc
                            </button>
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
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <?= $recentOrdersTitle ?>
                    </h6>
                    <span class="badge badge-primary">
                        Tổng: <?= count($recentOrders) ?> đơn hàng
                    </span>
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
                                            <td>
                                                <strong>#<?= $order['order_id'] ?></strong>
                                                <?php if ($filterType === 'month') : ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        Năm: <?= date('Y', strtotime($order['order_date'])) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></td>
                                            <td>
                                                <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?>
                                                <?php if ($filterType === 'month') : ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        Năm: <?= date('Y', strtotime($order['order_date'])) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="price">
                                                <?= number_format((float)$order['total'], 0, ',', '.') ?>₫
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    <?= $order['payment_method'] == 'cod' ? 'badge-warning' : 'badge-success' ?>"
                                                    style="color: #000 !important;">
                                                    <?= strtoupper(htmlspecialchars($order['payment_method'] ?? 'N/A')) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    <?= $order['payment_status'] == 'success' ? 'badge-success' : 
                                                        ($order['payment_status'] == 'pending' ? 'badge-warning' : 'badge-danger') ?>"
                                                    style="color: #000 !important;">
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
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                            Không có đơn hàng nào trong khoảng thời gian này
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Hiển thị thông tin bộ lọc hiện tại -->
                    <?php if (!empty($recentOrders)) : ?>
                        <div class="mt-3 p-3 bg-light rounded">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Đang hiển thị 
                                <?php
                                switch($filterType) {
                                    case 'month':
                                        echo "<strong>tất cả đơn hàng trong tháng $filterMonth</strong> qua các năm";
                                        break;
                                    case 'year':
                                        echo "<strong>tất cả đơn hàng trong năm $filterYear</strong>";
                                        break;
                                    case 'month_year':
                                        echo "<strong>tất cả đơn hàng trong tháng $filterMonth năm $filterYear</strong>";
                                        break;
                                    default:
                                        echo "<strong>tất cả đơn hàng trong tháng $currentMonth năm $currentYear</strong>";
                                        break;
                                }
                                ?>
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterType = document.getElementById('filterType');
    const monthSelect = document.getElementById('monthSelect');
    const yearSelect = document.getElementById('yearSelect');
    const submitBtn = document.getElementById('submitBtn');
    const filterForm = document.getElementById('filterForm');

    // Xử lý khi thay đổi loại lọc
    filterType.addEventListener('change', function() {
        const selectedType = this.value;
        
        // Reset và disable tất cả
        monthSelect.disabled = true;
        yearSelect.disabled = true;
        monthSelect.value = '';
        yearSelect.value = '';
        submitBtn.disabled = true;

        // Kích hoạt các trường tương ứng
        switch(selectedType) {
            case 'month':
                monthSelect.disabled = false;
                break;
            case 'year':
                yearSelect.disabled = false;
                break;
            case 'month_year':
                monthSelect.disabled = false;
                yearSelect.disabled = false;
                break;
        }

        // Kích hoạt nút submit nếu có chọn loại lọc
        if (selectedType) {
            submitBtn.disabled = false;
        }
    });

    // Xử lý khi thay đổi giá trị trong select
    function validateForm() {
        const filterTypeValue = filterType.value;
        const monthValue = monthSelect.value;
        const yearValue = yearSelect.value;
        
        let isValid = true;
        
        switch(filterTypeValue) {
            case 'month':
                isValid = monthValue !== '';
                break;
            case 'year':
                isValid = yearValue !== '';
                break;
            case 'month_year':
                isValid = monthValue !== '' && yearValue !== '';
                break;
            default:
                isValid = false;
        }
        
        submitBtn.disabled = !isValid;
    }

    monthSelect.addEventListener('change', validateForm);
    yearSelect.addEventListener('change', validateForm);

    // Khởi tạo trạng thái ban đầu dựa trên giá trị hiện tại
    function initializeForm() {
        const currentFilterType = '<?= isset($_GET['filter_type']) ? $_GET['filter_type'] : '' ?>';
        const currentMonth = '<?= $filterMonth ?>';
        const currentYear = '<?= $filterYear ?>';
        
        if (currentFilterType) {
            filterType.value = currentFilterType;
            
            // Kích hoạt các trường tương ứng
            switch(currentFilterType) {
                case 'month':
                    monthSelect.disabled = false;
                    if (currentMonth) monthSelect.value = currentMonth;
                    break;
                case 'year':
                    yearSelect.disabled = false;
                    if (currentYear) yearSelect.value = currentYear;
                    break;
                case 'month_year':
                    monthSelect.disabled = false;
                    yearSelect.disabled = false;
                    if (currentMonth) monthSelect.value = currentMonth;
                    if (currentYear) yearSelect.value = currentYear;
                    break;
            }
            
            validateForm();
        }
    }

    initializeForm();

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
