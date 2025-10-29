<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/models/customer.php';
require_once __DIR__ . '/../app/controllers/LoginController.php';

// Nếu người dùng đã đăng nhập thì chuyển thẳng sang trang chủ
if (isset($_SESSION['customer'])) {
    header("Location: index.php");
    exit();
}

// Nếu có cookie remember_token thì tự động đăng nhập
if (isset($_COOKIE['remember_token'])) {
    $customer = new Customer($conn);
    $user = $customer->getCustomerByToken($_COOKIE['remember_token']);

    if ($user) {
        $_SESSION['customer'] = [
            'cus_id' => $user['cus_id'],
            'fullname' => $user['fullname'],
            'email' => $user['email']
        ];
        header("Location: index.php");
        exit();
    }
}

$controller = new LoginController($conn);

// Nếu form được gửi (POST), xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->login();
} else {
    // Hiển thị trang login
    include __DIR__ . '/../app/views/login.php';
}
?>
