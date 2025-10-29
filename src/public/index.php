<?php
// public/index.php

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/../config/config.php';

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        require_once __DIR__ . '/../app/controllers/productController.php';
        $controller = new productController($conn);
        $controller->list();
        break;
        
    case 'detail':
        require_once __DIR__ . '/../app/controllers/productController.php';
        $controller = new productController($conn);
        $id = $_GET['id'] ?? 0;
        $controller->detail($id);
        break;
        
    case 'cart':  // THÊM CASE NÀY
        require_once __DIR__ . '/../app/controllers/cartController.php';
        $controller = new CartController($conn);
        $controller->viewCart();
        break;
        
    case 'login':
        require_once __DIR__ . '/../app/controllers/loginController.php';
        $controller = new LoginController($conn);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            include __DIR__ . '/../app/views/login.php';
        }
        break;
        
    default:
        require_once __DIR__ . '/../app/controllers/productController.php';
        $controller = new productController($conn);
        $controller->list();
        break;
}
?>
