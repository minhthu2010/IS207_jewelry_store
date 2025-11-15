<?php 
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/controllers/customerController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$action = $_GET['action'] ?? 'index';

$controller = new CustomerController($conn);

// Xử lý toggle_status
if ($action === 'toggle_status') {
    $controller->toggle_status();
    exit; // Kết thúc sau khi xuất JSON
} else {
    $controller->index();
}
