<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../public/assets/css/bootstrap-5.3.8-dist/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../public/assets/css/style_login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="login">
    <div class="login-container">
        <div class="login-left">
            <h1>Set New Password</h1>
            <p>Create your new password.</p>
        </div>

        <div class="login-right">
            <h2>New Password</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="forgot_password.php?action=reset_password">

                <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['token_email'] ?? '') ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['reset_token'] ?? '') ?>">
                
                <div class="input-group">
                    <i class="fa-solid fa-lock icon"></i>
                    <input type="password" name="new_password" class="form-control" placeholder="New Password" required />
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-lock icon"></i>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm New Password" required />
                </div>

                <button type="submit" class="btn-login w-100">Reset Password</button>

                <p class="signup-text">
                    <a href="login.php">Back to Login</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
