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
    
    case 'cart': 
        require_once __DIR__ . '/../app/controllers/cartController.php';
        $controller = new CartController($conn);
        $controller->viewCart();
        break;
    
    case 'process_checkout':
        require_once __DIR__ . '/../app/controllers/cartController.php';
        $controller = new CartController($conn);
        $controller->processCheckout();
        break;
        
    case 'checkout':
        require_once __DIR__ . '/../app/controllers/checkoutController.php';
        $controller = new CheckoutController($conn);
        $controller->index();
        break;
        
    case 'checkout/process':
        require_once __DIR__ . '/../app/controllers/checkoutController.php';
        $controller = new CheckoutController($conn);
        $controller->processOrder();
        break;
    
    case 'update_cart_quantity':
        require_once __DIR__ . '/../app/controllers/cartController.php';
        $controller = new CartController($conn);
        $item_id = $_POST['item_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;
        if ($item_id) {
            $result = $controller->updateCartItem($item_id, $quantity);
            echo json_encode(['success' => $result]);
        }
        break;
    case 'remove_cart_item':
        require_once __DIR__ . '/../app/controllers/cartController.php';
        $controller = new CartController($conn);
        $item_id = $_POST['item_id'] ?? null;
        if ($item_id) {
            $result = $controller->removeCartItem($item_id);
            echo json_encode(['success' => $result]);
        }
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
