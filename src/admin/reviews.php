<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/controllers/reviewController.php';

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
    $controller = new ReviewController($conn);
    $controller->delete();
    exit;
} else {
    $controller = new ReviewController($conn);
    $controller->index();
}
?>
