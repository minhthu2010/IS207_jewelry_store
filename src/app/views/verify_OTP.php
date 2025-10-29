<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="../public/assets/css/bootstrap-5.3.8-dist/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../public/assets/css/style_login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="login">
    <div class="login-container">
        <div class="login-left">
            <h1>Verify Your Identity</h1>
            <p>Enter the OTP sent to your email.</p>
        </div>

        <div class="login-right">
            <h2>Enter OTP</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (isset($debug_otp)): ?>
                <div class="alert alert-info">
                    <strong>DEBUG OTP:</strong> <?= $debug_otp ?> (Remove in production)
                </div>
            <?php endif; ?>

            <form method="POST" action="forgot_password.php?action=verify_otp">

                <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['reset_email'] ?? '') ?>">
                
                <div class="input-group">
                    <i class="fa-solid fa-shield-alt icon"></i>
                    <input type="text" name="otp" class="form-control" placeholder="Enter 6-digit OTP" required maxlength="6" />
                </div>

                <button type="submit" class="btn-login w-100">Verify OTP</button>

                <p class="signup-text">
                    <a href="forgot_password.php">Resend OTP</a> | 
                    <a href="login.php">Back to Login</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
