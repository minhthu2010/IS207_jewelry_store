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

    // Chức năng xóa khách hàng
    public function delete() {
        if (!isset($_GET['cus_id'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu mã khách hàng']);
            return;
        }

        $cusId = intval($_GET['cus_id']);

        if ($this->customerModel->deleteCustomer($cusId)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể xóa khách hàng']);
        }
    }
}

?>
