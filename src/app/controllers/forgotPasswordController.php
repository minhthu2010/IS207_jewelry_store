<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../PHPMailer/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/../../PHPMailer/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../../PHPMailer/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ForgotPasswordController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? '';
        switch ($action) {
            case 'send_reset_link':
                $this->sendResetLink();
                break;
            case 'verify_otp':
                $this->verifyOtp();
                break;
            case 'reset_password':
                $this->resetPassword();
                break;
            default:
                include __DIR__ . '/../views/forgot_password.php';
        }
    }

    private function sendResetLink() {
        $email = $_POST['email'] ?? '';
        $stmt = $this->conn->prepare("SELECT * FROM customer WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = "Email không tồn tại trong hệ thống.";
            include __DIR__ . '/../views/forgot_password.php';
            return;
        }

        $otp = rand(100000, 999999);
        $_SESSION['reset_email'] = $email;
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expire'] = time() + 300;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'Nhathuoc21623@gmail.com'; 
            $mail->Password   = 'bvrqsgxdiahupvsa';      
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('Nhathuoc21623@gmail.com', 'Jewelry Website');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code for Password Reset';
            $mail->Body    = "<p>Your OTP code is: <b>$otp</b></p><p>This code expires in 5 minutes.</p>";

            $mail->send();
            include __DIR__ . '/../views/verify_OTP.php';
        } catch (Exception $e) {
            $error = "Không thể gửi email. Lỗi: {$mail->ErrorInfo}";
            include __DIR__ . '/../views/forgot_password.php';
        }
    }

    private function verifyOtp() {
        $entered_otp = $_POST['otp'] ?? '';
        if (!isset($_SESSION['otp']) || time() > $_SESSION['otp_expire']) {
            $error = "Mã OTP đã hết hạn.";
            include __DIR__ . '/../views/forgot_password.php';
            return;
        }

        if ($entered_otp == $_SESSION['otp']) {
            $_SESSION['token_email'] = $_SESSION['reset_email'];
            $_SESSION['reset_token'] = bin2hex(random_bytes(16));
            include __DIR__ . '/../views/reset_password.php';
        } else {
            $error = "Mã OTP không chính xác.";
            include __DIR__ . '/../views/verify_OTP.php';
        }
    }

    private function resetPassword() {
        $email = $_POST['email'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if ($new_password !== $confirm_password) {
            $error = "Mật khẩu xác nhận không khớp.";
            include __DIR__ . '/../views/reset_password.php';
            return;
        }

        $hashed = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("UPDATE customer SET password = ? WHERE email = ?");
        $stmt->execute([$hashed, $email]);

        session_destroy();
        include __DIR__ . '/../views/password_reset_success.php';
    }
}
