<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/controllers/categoryController.php';
require_once __DIR__ . '/controllers/categoryFormController.php'; // THÊM DÒNG NÀY

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$action = $_GET['action'] ?? 'index';

// THÊM ACTION 'save' ĐỂ XỬ LÝ FORM
if ($action === 'delete') {
    $controller = new CategoryController($conn);
    $controller->delete();
    exit;
} elseif ($action === 'save') {
    $formController = new CategoryFormController($conn);
    $formController->handleForm();
    exit;
} else {
    $controller = new CategoryController($conn);
    $controller->index();
}
?>