<?php
require_once '../config/config.php';
require_once '../app/controllers/forgotPasswordController.php';

$controller = new ForgotPasswordController($conn);
$controller->handleRequest();
?>
