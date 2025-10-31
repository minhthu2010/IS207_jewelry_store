<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['customer'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/customer.php';

$db = $conn;

$data = json_decode(file_get_contents('php://input'), true);
$currentPassword = $data['currentPassword'] ?? '';
$newPassword = $data['newPassword'] ?? '';

if (!$currentPassword || !$newPassword) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin!']);
    exit;
}

$customer = new Customer($db);
$customer->cus_id = $_SESSION['customer']['cus_id'];

// Lấy thông tin khách hàng từ DB
$query = "SELECT password FROM customer WHERE cus_id = :cus_id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':cus_id', $customer->cus_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Người dùng không tồn tại!']);
    exit;
}

// Kiểm tra mật khẩu hiện tại
if (!password_verify($currentPassword, $row['password'])) {
    echo json_encode(['success' => false, 'message' => 'Mật khẩu hiện tại không đúng!']);
    exit;
}

// Hash mật khẩu mới và cập nhật
$newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
$updateQuery = "UPDATE customer SET password = :password, updated_at = NOW() WHERE cus_id = :cus_id";
$updateStmt = $db->prepare($updateQuery);
$updateStmt->bindParam(':password', $newHashedPassword);
$updateStmt->bindParam(':cus_id', $customer->cus_id);

if ($updateStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Đổi mật khẩu thành công!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại.']);
}
