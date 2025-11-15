<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/models/order.php';

$customerId = $_GET['customer_id'] ?? null;

if (!$customerId) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã khách hàng']);
    exit;
}

try {
    $orderModel = new Order($conn);
    $orders = $orderModel->getOrdersByCustomer($customerId);

    echo json_encode(['success' => true, 'orders' => $orders]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
}
