<?php
session_start();
session_unset();
session_destroy();

// Xóa cookie token
if (isset($_COOKIE['token'])) {
    setcookie('token', '', time() - 3600, "/");
}

header('Content-Type: application/json');
echo json_encode(['status' => 'success']);
exit();
?>
