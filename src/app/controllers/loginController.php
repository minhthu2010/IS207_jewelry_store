<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/customer.php';

class LoginController {
    private $customer;

    public function __construct($db) {
        $this->customer = new Customer($db);
    }

    public function login() {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $error = "Vui lòng nhập đầy đủ thông tin!";
            include __DIR__ . '/../views/login.php';
            return;
        }

        if ($this->customer->login($email, $password)) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Email hoặc mật khẩu không đúng!";
            include __DIR__ . '/../views/login.php';
        }
    }
}
?>
