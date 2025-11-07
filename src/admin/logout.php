<?php
session_start();
setcookie('admin_token', '', time() - 3600, '/');
unset($_SESSION['admin']);
header("Location: login.php");
exit;
?>
