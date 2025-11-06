<?php
require_once '../config/config.php';
include __DIR__ . '/views/templates/header.php';
include __DIR__ . '/views/templates/sidebar.php';
include __DIR__ . '/views/templates/topbar.php';
?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

    <div class="row">
        <!-- Card doanh thu -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Doanh thu (Tháng)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">152,400,000₫</div>
                </div>
            </div>
        </div>
        <!-- Các card khác -->
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tổng quan doanh thu</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/views/templates/footer.php'; ?>
