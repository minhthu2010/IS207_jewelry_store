

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../public/assets/css/bootstrap-5.3.8-dist/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../public/assets/css/style_login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="login">
    <div class="login-container">
        <div class="login-left">
            <h1>Reset Your Password</h1>
            <p>Enter your email to receive a password reset link.</p>
        </div>

        <div class="login-right">
            <h2>Forgot Password</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="forgot_password.php?action=send_reset_link">

                <div class="input-group">
                    <i class="fa-solid fa-envelope icon"></i>
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required />
                </div>

                <button type="submit" class="btn-login w-100">Send Reset Link</button>

                <p class="signup-text">
                    Remember your password? <a href="login.php">Back to Login</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
