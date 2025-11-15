<?php
// public/index.php

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/../config/config.php';

$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'home':
        require_once __DIR__ . '/../app/controllers/HomeController.php';
        $controller = new HomeController($conn);
        $data = $controller->index();
        extract($data);
        include __DIR__ . '/../app/views/home.php';
        break;

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
            $result = $controller->handleUpdateQuantity($item_id, $quantity);
            header('Content-Type: application/json');
            echo json_encode($result);
        }
        break;

    case 'remove_cart_item':
        require_once __DIR__ . '/../app/controllers/cartController.php';
        $controller = new CartController($conn);
        $item_id = $_POST['item_id'] ?? null;
        if ($item_id) {
            $result = $controller->handleRemoveItem($item_id);
            header('Content-Type: application/json');
            echo json_encode($result);
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

    case 'logout':
        require_once __DIR__ . '/../public/logout.php';
        break;

    case 'sign_up':
        require_once __DIR__ . '/../app/controllers/signUpController.php';
        $controller = new SignUpController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register();
        } else {
            $controller->showForm();
        }
        break;

    case 'account':
        // Chỉ cho phép user đã login
        if (!isset($_SESSION['customer'])) {
            header("Location: {$base_url}/public/index.php?action=login");
            exit;
        }

        $accountAction = $_GET['sub_action'] ?? 'view';
        switch ($accountAction) {
            case 'update':
                require_once __DIR__ . '/../app/controllers/updateCustomerController.php';
                $controller = new UpdateCustomerController($conn);
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->update();
                } else {
                    include __DIR__ . '/../app/views/account.php';
                }
                break;

            case 'change_password':
                require_once __DIR__ . '/../app/controllers/changePasswordController.php';
                $controller = new ChangePasswordController($conn);
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->changePassword();
                } else {
                    include __DIR__ . '/../app/views/account.php';
                }
                break;

            case 'view':
            default:
                include __DIR__ . '/../app/views/account.php';
                break;
        }
        break;

    case 'forgot_password':
        require_once __DIR__ . '/../app/controllers/forgotPasswordController.php';
        $controller = new ForgotPasswordController($conn);
        $controller->handleRequest();
        break;

    default:
        // fallback về home
        require_once __DIR__ . '/../app/controllers/homeController.php';
        $controller = new HomeController($conn);
        $data = $controller->index();
        extract($data);
        include __DIR__ . '/../app/views/home.php';
        break;
}
?>
