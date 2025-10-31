<?php
ini_set('display_errors', 0);
error_reporting(0);
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';


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
    // Lấy thông tin đơn hàng
    $stmt = $conn->prepare("
        SELECT 
            o.order_id,
            o.order_date,
            o.total,
            o.status,
            o.payment_method,
            o.payment_status,
            o.shipping_address,
            o.shipping_fullname,
            o.shipping_phone,
            o.shipping_fee,
            o.notes
        FROM orders o
        WHERE o.order_id = :oid
    ");
    $stmt->bindParam(':oid', $orderId, PDO::PARAM_INT);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng']);
        exit;
    }

    // Lấy chi tiết sản phẩm trong đơn
    $stmt2 = $conn->prepare("
        SELECT 
            p.name AS product_name,
            pv.size,
            od.quantity,
            od.price_at_purchase,
            (od.quantity * od.price_at_purchase) AS total_item
        FROM order_detail od
        JOIN product_variant pv ON od.variant_id = pv.variant_id
        JOIN product p ON pv.product_id = p.pro_id
        WHERE od.order_id = :oid
    ");
    $stmt2->bindParam(':oid', $orderId, PDO::PARAM_INT);
    $stmt2->execute();
    $items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'order' => $order, 'items' => $items]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
}
