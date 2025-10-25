<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign Up Page</title>
  <link rel="stylesheet" href="public/assets/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../public/assets/css/style_sign_up.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body class="signup">
  <div class="signup-container">
    <!-- Bên trái -->
    <div class="signup-left">
      <h1>Join us today!</h1>
      <p>Create your account and start your journey with us.</p>
    </div>

    <!-- Bên phải -->
    <div class="signup-right">
      <h2>Sign Up</h2>
      <form>
        <div class="input-group">
          <i class="fa-solid fa-user icon"></i>
          <input type="text" class="form-control" placeholder="Full name" required />
        </div>

        <div class="input-group">
          <i class="fa-solid fa-envelope icon"></i>
          <input type="email" class="form-control" placeholder="Email address" required />
        </div>

        <div class="input-group">
          <i class="fa-solid fa-lock icon"></i>
          <input type="password" class="form-control" placeholder="Password" required />
        </div>

        <div class="input-group">
          <i class="fa-solid fa-lock icon"></i>
          <input type="password" class="form-control" placeholder="Confirm password" required />
        </div>

        <button type="submit" class="btn-signup w-100">Create Account</button>

        <p class="login-text">
          Already have an account? <a href="login.php">Log In</a>
        </p>
      </form>
    </div>
  </div>
</body>
</html>