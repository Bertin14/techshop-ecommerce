<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TechShop Rwanda</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <div class="toast" id="toast"></div>

  <header>
    <div class="navbar">
      <div class="logo">
        <a href="/index.php">⚡ TechShop</a>
      </div>
      <nav>
        <a href="/admin/login.php" style="color:#e94560;">📊 Admin</a>
        <a href="/index.php">Home</a>
        <a href="/products.php">Products</a>
        <a href="/cart.php">
          🛒 Cart
          <?php
          $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
          $count = array_sum(array_column($cart, 'quantity'));
          if ($count > 0) echo "<span class='cart-count'>$count</span>";
          ?>
        </a>
        <button class="dark-toggle" onclick="toggleDark()" id="darkBtn">🌙 Dark</button>
      </nav>
    </div>
  </header>

  <script>
    function toggleDark() {
      document.body.classList.toggle('dark-mode');
      const btn = document.getElementById('darkBtn');
      const isDark = document.body.classList.contains('dark-mode');
      btn.textContent = isDark ? '☀️ Light' : '🌙 Dark';
      localStorage.setItem('darkMode', isDark);
    }

    if (localStorage.getItem('darkMode') === 'true') {
      document.body.classList.add('dark-mode');
      document.getElementById('darkBtn').textContent = '☀️ Light';
    }

    function showToast(msg) {
      const t = document.getElementById('toast');
      t.textContent = msg;
      t.classList.add('show');
      setTimeout(() => t.classList.remove('show'), 3000);
    }
  </script>