<?php
// public/account.php

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/../config/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['customer'])) {
    header("Location: {$base_url}/public/login.php");
    exit;
}

$action = $_GET['action'] ?? 'view';

// Xử lý theo action
switch ($action) {
    case 'update': // Gọi cập nhật thông tin cá nhân
        require_once __DIR__ . '/../app/controllers/updateCustomerController.php';
        $controller = new UpdateCustomerController($conn);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->update();
        } else {
            include __DIR__ . '/../app/views/account.php';
        }
        break;

    case 'change_password': // Gọi đổi mật khẩu
        require_once __DIR__ . '/../app/controllers/changePasswordController.php';
        $controller = new ChangePasswordController($conn);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->changePassword();
        } else {
            include __DIR__ . '/../app/views/account.php';
        }
        break;

    case 'view': // Mặc định: chỉ hiển thị giao diện tài khoản
    default:
        include __DIR__ . '/../app/views/account.php';
        break;
}
