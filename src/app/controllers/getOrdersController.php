<?php
ini_set('display_errors', 0);
error_reporting(0);
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/order.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['customer'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập']);
    exit;
}

$customerId = $_SESSION['customer']['cus_id'];

try {
    $orderModel = new Order($conn);
    $orders = $orderModel->getOrdersByCustomer($customerId);

    echo json_encode(['success' => true, 'orders' => $orders]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
}
