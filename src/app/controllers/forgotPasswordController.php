<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/customer.php';

class ForgotPasswordController {
    public function showForm() {
        include __DIR__ . '/../views/forgot_password.php';
    }

    public function sendResetLink() {
        global $conn;
        
        $email = $_POST['email'] ?? '';
        
        if (empty($email)) {
            $error = "Please enter your email address!";
            include __DIR__ . '/../views/forgot_password.php';
            return;
        }
        
        $customer = new Customer($conn);
        $customer->email = $email;
        
        if (!$customer->emailExists()) {
            $error = "Email not found!";
            include __DIR__ . '/../views/forgot_password.php';
            return;
        }
        
        // Generate OTP (6 digits)
        $otp = rand(100000, 999999);
        
        // Save OTP to session (expires in 10 minutes)
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['reset_email'] = $email;
        $_SESSION['otp_expiry'] = time() + 600; // 10 minutes
        
        // For demo, we'll show OTP on screen (remove this in production)
        $debug_otp = $otp;
        
        include __DIR__ . '/../views/verify_otp.php';
    }
    
    public function verifyOtp() {
        $email = $_POST['email'] ?? '';
        $user_otp = $_POST['otp'] ?? '';
        
        // Check if OTP exists and not expired
        if (!isset($_SESSION['reset_otp']) || 
            !isset($_SESSION['reset_email']) || 
            !isset($_SESSION['otp_expiry']) ||
            time() > $_SESSION['otp_expiry']) {
            
            $error = "OTP has expired! Please request a new one.";
            include __DIR__ . '/../views/forgot_password.php';
            return;
        }
        
        // Verify OTP
        if ($_SESSION['reset_otp'] == $user_otp && $_SESSION['reset_email'] == $email) {
            // OTP verified, generate reset token
            $token = bin2hex(random_bytes(32));
            $_SESSION['reset_token'] = $token;
            $_SESSION['token_email'] = $email;
            $_SESSION['token_expiry'] = time() + 1800; // 30 minutes
            
            include __DIR__ . '/../views/reset_password.php';
        } else {
            $error = "Invalid OTP!";
            include __DIR__ . '/../views/verify_otp.php';
        }
    }
    
    public function resetPassword() {
        global $conn;
        
        $email = $_POST['email'] ?? '';
        $token = $_POST['token'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validate token
        if (!isset($_SESSION['reset_token']) || 
            $_SESSION['reset_token'] !== $token ||
            $_SESSION['token_email'] !== $email ||
            time() > $_SESSION['token_expiry']) {
            
            $error = "Invalid or expired reset token!";
            include __DIR__ . '/../views/forgot_password.php';
            return;
        }
        
        // Validate passwords
        if ($new_password !== $confirm_password) {
            $error = "Passwords do not match!";
            include __DIR__ . '/../views/reset_password.php';
            return;
        }
        
        if (strlen($new_password) < 6) {
            $error = "Password must be at least 6 characters!";
            include __DIR__ . '/../views/reset_password.php';
            return;
        }
        
        // Update password in database
        $customer = new Customer($conn);
        if ($customer->updatePassword($email, $new_password)) {
            // Clear all reset sessions
            $this->clearResetSessions();
            include __DIR__ . '/../views/password_reset_success.php';
        } else {
            $error = "Password reset failed!";
            include __DIR__ . '/../views/reset_password.php';
        }
    }
    
    private function clearResetSessions() {
        unset($_SESSION['reset_otp']);
        unset($_SESSION['reset_email']);
        unset($_SESSION['otp_expiry']);
        unset($_SESSION['reset_token']);
        unset($_SESSION['token_email']);
        unset($_SESSION['token_expiry']);
    }
}
?>
