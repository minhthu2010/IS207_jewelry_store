<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/Order.php';

if (!isset($_SESSION['customer'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Chưa đăng nhập'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$orderId = $data['order_id'] ?? null;
$customerId = $_SESSION['customer']['cus_id'] ?? null;

if (!$orderId || !$customerId) {
    echo json_encode([
        'success' => false,
        'message' => 'Dữ liệu không hợp lệ'
    ]);
    exit;
}

try {
    $orderModel = new Order($conn);
    $result = $orderModel->cancelOrderByCustomer($orderId, $customerId);

    echo json_encode($result);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống'
    ]);
}
