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

$controller = new CustomerController($conn);
$controller->index();
?>
