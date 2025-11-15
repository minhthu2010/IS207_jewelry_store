<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/models/customer.php';

class CustomerController {
    private $customerModel;

    public function __construct($db) {
        $this->customerModel = new Customer($db);
    }

    public function index() {
        $search = $_GET['search'] ?? '';
        $orderCount = $_GET['order_count'] ?? '';

        $customers = $this->customerModel->getAllCustomers($search, $orderCount);

        include __DIR__ . '/../views/customers.php';
    }

    public function toggle_status() {
    if (!isset($_GET['cus_id'])) {
        echo json_encode(['success' => false, 'message' => 'Thiếu mã khách hàng']);
        return;
    }

    $cusId = intval($_GET['cus_id']);

    $cus = $this->customerModel->getCustomerById($cusId);
    if (!$cus) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy khách hàng']);
        return;
    }

    $newStatus = $cus['status'] == 1 ? 0 : 1;

    if ($this->customerModel->updateStatus($cusId, $newStatus)) {
        $msg = $newStatus == 1 ? 'Tài khoản đã được kích hoạt' : 'Tài khoản đã bị khóa';
        echo json_encode(['success' => true, 'message' => $msg]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái']);
    }
}

}

?>
