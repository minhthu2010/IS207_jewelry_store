

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="../public/assets/css/bootstrap-5.3.8-dist/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../public/assets/css/style_login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="login">
    <div class="login-container">
        <div class="login-left">
            <h1>Đặt lại mật khẩu</h1>
            <p>Nhập email.</p>
        </div>

        <div class="login-right">
            <h2>Quên mật khẩu</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="forgot_password.php?action=send_reset_link">

                <div class="input-group">
                    <i class="fa-solid fa-envelope icon"></i>
                    <input type="email" name="email" class="form-control" placeholder="Nhập email của bạn" required />
                </div>

                <button type="submit" class="btn-login w-100">Gửi mã OTP</button>

                <p class="signup-text">
                    Nhớ mật khẩu? <a href="login.php">Quay về trang Đăng nhập</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
