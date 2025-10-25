<?php
// Bật hiển thị lỗi
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Cấu hình database
define('DB_HOST', 'localhost');
define('DB_NAME', 'jewelry_store');  
define('DB_USER', 'root');
define('DB_PASS', ''); 

try {
    // Kết nối MySQL bằng PDO
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS
    );

    // Thiết lập chế độ lỗi
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Kết nối database thất bại: " . $e->getMessage());
}

// (Tuỳ chọn) khởi động session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
