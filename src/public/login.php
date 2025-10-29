

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/controllers/loginController.php';

$controller = new LoginController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->login();
} else {
    include __DIR__ . '/../app/views/login.php';
}
?>

