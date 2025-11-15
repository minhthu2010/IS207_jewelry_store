<?php
session_start();
session_unset();
session_destroy();

// Xóa cookie token nếu có
if (isset($_COOKIE['token'])) {
    setcookie('token', '', time() - 3600, "/");
}

// Trả JSON cho fetch JS
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'redirect' => 'index.php' 
]);
exit();
