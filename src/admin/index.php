<?php
session_start();


// Kiểm tra xem admin đã đăng nhập chưa
if (!isset($_SESSION['admin'])) {
    // Chưa đăng nhập -> chuyển sang trang login
    header("Location: login.php");
    exit;
}

// Nếu đã đăng nhập -> load dashboard
require_once 'controllers/dashboardController.php';
$controller = new DashboardController($conn);
$controller->index();
?>
