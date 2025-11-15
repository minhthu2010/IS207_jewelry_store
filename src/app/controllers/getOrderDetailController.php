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

$orderId = $_GET['order_id'] ?? null;
if (!$orderId) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã đơn hàng']);
    exit;
}

try {
    $orderModel = new Order($conn);
    $result = $orderModel->getOrderDetail($orderId);

    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng']);
        exit;
    }

    echo json_encode(['success' => true, 'order' => $result['order'], 'items' => $result['items']]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
}
