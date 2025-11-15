<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập Hệ Thống</title>
    <link rel="stylesheet" href="../public/assets/css/style_admin_login.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

   

    <!-- Khung đăng nhập -->
    <div class="login-container">
        <div class="login-box">
            <h2>Đăng Nhập Hệ Thống</h2>

            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fa fa-user"></i></label>
                    <input type="text" name="email_or_username" placeholder="Email" required>
                </div>

                <div class="form-group">
                    <label><i class="fa fa-lock"></i></label>
                    <input type="password" name="password" placeholder="Mật khẩu" required>
                    
                </div>

                <div class="form-options">
                    <label><input type="checkbox" name="remember"> Ghi nhớ đăng nhập</label>
                </div>

                

                <button type="submit" class="login-btn">Đăng nhập</button>
            </form>
        </div>
    </div>

    
</body>
</html>
