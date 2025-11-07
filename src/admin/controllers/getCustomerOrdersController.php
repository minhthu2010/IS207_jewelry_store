<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';

$customerId = $_GET['customer_id'] ?? null;

if (!$customerId) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã khách hàng']);
    exit;
}

try {
    $sql = "SELECT 
                o.order_id,
                o.order_date,
                o.total,
                o.status,
                o.payment_method,
                o.payment_status
            FROM orders o
            WHERE o.customer_id = :cid
            ORDER BY o.order_date DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cid', $customerId, PDO::PARAM_INT);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'orders' => $orders]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
}
?>
