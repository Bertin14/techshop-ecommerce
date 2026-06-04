<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TechShop Rwanda</title>
  <link rel="stylesheet" href="/techshop/assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

  <header>
    <div class="navbar">
      <div class="logo">
        <a href="/index.php">⚡ TechShop</a>
      </div>
      <nav>
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
      </nav>
    </div>
  </header>