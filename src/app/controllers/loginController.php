<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/customer.php';

class LoginController {
    private $customer;

    public function __construct($db) {
        $this->customer = new Customer($db);
    }

    public function login() {
        
        // Tự động đăng nhập nếu đã có cookie token
        if (!empty($_COOKIE['token'])) {
            $user = $this->customer->getCustomerByToken($_COOKIE['token']);
            if ($user) {
                $_SESSION['customer'] = $user;
                header("Location: index.php");
                exit;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $remember = isset($_POST['remember']); // checkbox

            if (empty($email) || empty($password)) {
                $error = "Vui lòng nhập đầy đủ thông tin!";
                include __DIR__ . '/../views/login.php';
                return;
            }

            if ($this->customer->login($email, $password)) {
                $cus_id = $_SESSION['customer']['cus_id'];

                // Nếu user tick Remember Me
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $this->customer->saveToken($cus_id, $token);
                    setcookie('token', $token, time() + (86400 * 30), "/"); // 30 ngày
                }

                header("Location: index.php");
                exit;
            } else {
                $error = "Email hoặc mật khẩu không đúng!";
                include __DIR__ . '/../views/login.php';
            }
        } else {
            include __DIR__ . '/../views/login.php';
        }
    }
}
?>
