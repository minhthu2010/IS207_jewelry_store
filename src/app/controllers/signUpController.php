<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/customer.php';

class SignUpController {
    public function showForm() {
        include __DIR__ . '/../views/sign_up.php';
    }

    public function register() {
        global $conn; 

        $fullname = $_POST['fullname'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($fullname) || empty($email) || empty($password)) {
            $error = "Vui lòng nhập đầy đủ thông tin!";
            include __DIR__ . '/../views/sign_up.php';
            return;
        }

        if ($password !== $confirm_password) {
            $error = "Password và Confirm Password không khớp!";
            include __DIR__ . '/../views/sign_up.php';
            return;
        }
        

        $customer = new Customer($conn);
        $customer->fullname = $fullname;
        $customer->email = $email;
        $customer->password = $password;

        if ($customer->emailExists()) {
            $error = "Email đã tồn tại!";
            include __DIR__ . '/../views/sign_up.php';
            return;
        }

       
        if ($customer->create()) {
            header('Location: login.php');
            exit;
        } else {
            $error = "Đăng ký thất bại, vui lòng thử lại!";
            include __DIR__ . '/../views/sign_up.php';
        }
    }
}
?>
