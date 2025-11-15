<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Đăng ký</title>
  <link rel="stylesheet" href="../public/assets/css/bootstrap-5.3.8-dist/bootstrap-5.3.8-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../public/assets/css/style_sign_up.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="signup">
  <div class="signup-container">
    <div class="signup-left">
      <h1>Tham gia ngay!</h1>
      <p>Đăng ký tài khoản và bắt đầu hành trình với chúng tôi.</p>
    </div>

    <div class="signup-right">
      <h2>Đăng ký</h2>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="input-group">
          <i class="fa-solid fa-user icon"></i>
          <input type="text" name="fullname" class="form-control" placeholder="Họ tên" required />
        </div>

        <div class="input-group">
          <i class="fa-solid fa-envelope icon"></i>
          <input type="email" name="email" class="form-control" placeholder="Email" required />
        </div>

        <div class="input-group">
          <i class="fa-solid fa-lock icon"></i>
          <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required />
        </div>

        <div class="input-group">
          <i class="fa-solid fa-lock icon"></i>
          <input type="password" name="confirm_password" class="form-control" placeholder="Xác nhận mật khẩu" required />
        </div>

        <button type="submit" class="btn-signup w-100">Đăng ký</button>

        <p class="login-text">
          Đã có tài khoản? <a href="login.php">Đăng nhập</a>
        </p>
      </form>
    </div>
  </div>
</body>
</html>
