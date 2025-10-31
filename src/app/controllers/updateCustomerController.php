<?php
ini_set('display_errors', 0);
error_reporting(0);
session_start();
header('Content-Type: application/json');

// Nếu chưa đăng nhập thì trả về lỗi
if (!isset($_SESSION['customer'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
    exit;
}

// Gọi file kết nối DB và model
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/customer.php';

// Tạo kết nối DB
$db = $conn;

// Lấy dữ liệu từ AJAX
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Không có dữ liệu gửi lên!']);
    exit;
}

$customer = new Customer($db);
$customer->cus_id = $_SESSION['customer']['cus_id'];
$customer->fullname = $data['fullname'] ?? '';
$customer->phone = $data['phone'] ?? '';
$customer->email = $data['email'] ?? '';
$customer->address = $data['address'] ?? '';

// Thực hiện cập nhật
if ($customer->updateInfo()) {
    // Cập nhật lại session
    $_SESSION['customer']['fullname'] = $customer->fullname;
    $_SESSION['customer']['phone'] = $customer->phone;
    $_SESSION['customer']['email'] = $customer->email;
    $_SESSION['customer']['address'] = $customer->address;

    echo json_encode(['success' => true, 'message' => 'Cập nhật thành công!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Không thể cập nhật dữ liệu!']);
}
