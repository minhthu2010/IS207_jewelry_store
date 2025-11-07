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

if ($action === 'delete') {
    $controller = new CustomerController($conn);
    $controller->delete();
    exit; // Kết thúc sau khi xử lý API
} else {
    $controller = new CustomerController($conn);
    $controller->index();
}

?>
