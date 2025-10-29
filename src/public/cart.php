<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/controllers/cartController.php';
require_once __DIR__ . '/../app/views/cart.php';

$controller = new CartController($conn);
$cartItems = $controller->getCart();

include __DIR__ . '/templates/header.php';
echo CartView::renderCartPage($cartItems);
include __DIR__ . '/templates/footer.php';
?>
