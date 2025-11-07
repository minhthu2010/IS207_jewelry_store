<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/models/user.php';

class CustomerController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function index() {
        $search = $_GET['search'] ?? '';
        $orderCount = $_GET['order_count'] ?? '';

        $customers = $this->userModel->getAllCustomers($search, $orderCount);

        include __DIR__ . '/../views/customers.php';
    }
}
?>
