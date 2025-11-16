<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/controllers/productController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

$strict_post_actions = ['store', 'add_variant', 'update_variant', 'delete_variant', 'add_image', 'set_main_image', 'delete_image', 'update_image_sort_orders'];

if (in_array($action, $strict_post_actions) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method for action: " . $action;
    header('Location: products.php');
    exit;
}

try {
    $controller = new ProductController_Admin($conn);
    
    switch ($action) {
        case 'create':
            $controller->create();
            break;
        case 'edit':
            $controller->edit($id);
            break;
        case 'store':
            $controller->store();
            break;
        case 'update':
            $controller->update($id);
            break;
        case 'delete':
            $controller->delete($id);
            break;
        case 'add_variant':
            $controller->addVariant();
            break;
        case 'update_variant':
            $controller->updateVariant($id);
            break;
        case 'delete_variant':
            $controller->deleteVariant($id);
            break;
        case 'add_image':
            $controller->addImage();
            break;
        case 'set_main_image':
            $controller->setMainImage($id);
            break;
        case 'delete_image':
            $controller->deleteImage($id);
            break;
        case 'update_image_sort_orders':
            $controller->updateImageSortOrders();
            break;
        default:
            $controller->index();
            break;
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = 'System Error: ' . $e->getMessage();
    header('Location: products.php');
    exit;
}
?>
