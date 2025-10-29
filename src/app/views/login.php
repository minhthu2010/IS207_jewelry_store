<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Page</title>
  <link rel="stylesheet" href="../public/assets/css/bootstrap-5.3.8-dist/bootstrap-5.3.8-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../public/assets/css/style_login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body class="login">
  <div class="login-container">
    <div class="login-left">
      <h1>Welcome back!</h1>
      <p>You can log in to access with your existing account.</p>
    </div>

    <div class="login-right">
      <h2>Log In</h2>
      <?php if (!empty($error)): ?>
  <div class="alert alert-danger error-message">
      <?= htmlspecialchars($error) ?>
  </div>
<?php endif; ?>




      <form method="POST" action="">
  <div class="input-group">
    <i class="fa-solid fa-user icon"></i>
    <input type="text" name="email" class="form-control" placeholder="Email" required />
  </div>

  <div class="input-group">
    <i class="fa-solid fa-lock icon"></i>
    <input type="password" name="password" class="form-control" placeholder="Password" required />
  </div>

  <div class="options">
  <div>
    <input type="checkbox" id="remember" name="remember" />
    <label for="remember">Remember me</label>
  </div>
  <a href="forgot_password.php">Forgot password?</a> 
</div>


  <button type="submit" class="btn-login w-100">Log In</button>

  <p class="signup-text">
    New here? <a href="sign_up.php">Create an Account</a>
  </p>
</form>


    </div>
  </div>
</body>
</html>
