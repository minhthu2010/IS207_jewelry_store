<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
session_unset();
session_destroy();
setcookie("admin_token", "", time() - 3600, "/");

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/models/Admin.php';
require_once __DIR__ . '/controllers/adminLoginController.php';

// Nếu admin đã đăng nhập → chuyển đến dashboard
if (isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Nếu có cookie remember → tự động đăng nhập
if (isset($_COOKIE['admin_token'])) {
    $admin = new Admin($conn);
    $user = $admin->getAdminByToken($_COOKIE['admin_token']);
    if ($user) {
        $_SESSION['admin'] = $user;
        header("Location: index.php");
        exit();
    }
}

$controller = new AdminLoginController($conn);

// Nếu form POST → xử lý login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->login();
} else {
    include __DIR__ . '/views/login.php';
}
?>
