<?php
session_start();


require_once __DIR__ . '/../app/controllers/forgotPasswordController.php';

$controller = new ForgotPasswordController();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'send_reset_link':
            $controller->sendResetLink();
            break;
        case 'verify_otp':
            $controller->verifyOtp();
            break;
        case 'reset_password':
            $controller->resetPassword();
            break;
        default:
            $controller->showForm();
    }
} else {
    $controller->showForm();
}
?>
