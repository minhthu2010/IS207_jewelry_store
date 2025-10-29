<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>D. Patel Jewelry</title>
  
  <!-- Bootstrap offline -->
  <link rel="stylesheet" href="../public/assets/css/bootstrap-5.3.8-dist/bootstrap-5.3.8-dist/css/bootstrap.min.css">
  
  <!-- CSS tùy chỉnh -->
  <link rel="stylesheet" href="../public/assets/css/style.css">
  <link rel="stylesheet" href="../public/assets/css/style_header.css">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <header class="navbar">
  <div class="logo">D. Patel</div>
  <nav class="menu">
    <a href="index.php?action=list">Shop All</a>
    <a href="#">About</a>
    <a href="#">Contact</a>
  </nav>
  <div class="actions">
    <i class="fas fa-search"></i>

    <div id="user-area">
<?php if (isset($_SESSION['customer'])): ?>
    <?php 
        $fullname = $_SESSION['customer']['fullname'];
        $firstLetter = strtoupper(substr($fullname, 0, 1));
    ?>
    <div class="user-menu">
        <div class="avatar" id="avatar"><?php echo $firstLetter; ?></div>
        <span class="user-name"><?php echo htmlspecialchars($fullname); ?></span>
        <div class="dropdown-menu" id="dropdown-menu">
            <a href="#" id="logoutBtn" class="logout">Log Out</a>
        </div>
    </div>
<?php else: ?>
    <a href="login.php" class="login" id="loginLink">Log In</a>
<?php endif; ?>
</div>

    <div class="cart">
      <a href="index.php?action=cart" class="cart-link">
        <i class="fas fa-shopping-bag"></i>
        <span class="count" id="cart-count" style="display: none;">0</span>
      </a>
    </div>
  </div>
</header>

<script>
// Cập nhật số lượng giỏ hàng
function updateCartCount(count) {
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        cartCount.textContent = count;
        if (count == 0) {
            cartCount.style.display = 'none';
        } else {
            cartCount.style.display = 'flex';
        }
    }
}

// Lấy số lượng giỏ hàng từ server khi trang loaded
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['customer'])): ?>
    fetch('../app/controllers/cartController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_cart_count'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount(data.itemCount);
        }
    })
    .catch(error => {
        console.error('Error fetching cart count:', error);
    });
    <?php endif; ?>
});

// Các event listeners khác giữ nguyên...
const avatar = document.getElementById('avatar');
const dropdown = document.getElementById('dropdown-menu');
const logoutBtn = document.getElementById('logoutBtn');
const userArea = document.getElementById('user-area');

if (avatar && dropdown) {
  avatar.addEventListener('click', () => {
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
  });

  window.addEventListener('click', (e) => {
    if (!avatar.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.style.display = 'none';
    }
  });
}

if (logoutBtn) {
  logoutBtn.addEventListener('click', async (e) => {
    e.preventDefault();

    const res = await fetch('logout.php', { method: 'POST' });
    const data = await res.json();

    if (data.status === 'success') {
      userArea.innerHTML = `<a href="login.php" class="login" id="loginLink">Log In</a>`;
      updateCartCount(0);
    }
  });
}
</script>

</body>
</html>
