<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/models/Admin.php';

class AdminLoginController {
    private $admin;

    public function __construct($db) {
        $this->admin = new Admin($db);
    }

    public function login() {
        // ✅ Kiểm tra nếu đã có cookie admin_token
        if (!empty($_COOKIE['admin_token'])) {
            $user = $this->admin->getAdminByToken($_COOKIE['admin_token']);
            if ($user) {
                $_SESSION['admin'] = $user;
                header("Location: index.php");
                exit;
            }
        }

        // ✅ Nếu form gửi POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $emailOrUsername = trim($_POST['email_or_username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $remember = isset($_POST['remember']);

            if (empty($emailOrUsername) || empty($password)) {
                $error = "Vui lòng nhập đầy đủ thông tin!";
                include __DIR__ . '/../views/login.php';
                return;
            }

            if ($this->admin->login($emailOrUsername, $password)) {
                $admin_id = $_SESSION['admin']['admin_id'];

                // Nếu chọn Remember Me
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $this->admin->saveToken($admin_id, $token);
                    setcookie('admin_token', $token, time() + (86400 * 30), "/");
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
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        setcookie("admin_token", "", time() - 3600, "/");

        // Quay về trang login
        header("Location: ../login.php");
        exit();
    }
    

    
}
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $controller = new AdminLoginController($conn);
    $controller->logout();
}
?>
