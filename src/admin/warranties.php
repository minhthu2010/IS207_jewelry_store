<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/controllers/warrantyController.php';

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

$strict_post_actions = ['create', 'update'];

if (in_array($action, $strict_post_actions) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method for action: " . $action;
    header('Location: warranties.php');
    exit;
}

try {
    $controller = new WarrantyController($conn);
    
    switch ($action) {
        case 'create':
            $controller->create();
            break;
        case 'update':
            $controller->update($id);
            break;
        case 'delete':
            $controller->delete();
            break;
        default:
            $controller->index();
            break;
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = 'System Error: ' . $e->getMessage();
    header('Location: warranties.php');
    exit;
}
?>