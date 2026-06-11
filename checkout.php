<?php
require 'includes/db.php';
require 'includes/header.php';

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: /cart.php');
    exit;
}

// HANDLE ORDER SUBMISSION
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $email     = htmlspecialchars(trim($_POST['email']));
    $phone     = htmlspecialchars(trim($_POST['phone']));
    $address   = htmlspecialchars(trim($_POST['address']));

    // Save customer
    $stmt = $pdo->prepare("INSERT INTO customers (full_name, email, phone, address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$full_name, $email, $phone, $address]);
    $customer_id = $pdo->lastInsertId();

    // Calculate total
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Save order
    $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$customer_id, $total]);
    $order_id = $pdo->lastInsertId();

    // Save order items
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $product_id, $item['quantity'], $item['price']]);

        // Reduce stock
        $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$item['quantity'], $product_id]);
    }

    // Clear cart
    $_SESSION['cart'] = [];

    // Redirect to confirmation
    header("Location: /order-confirmation.php?order_id=$order_id");
    exit;
}
?>

<div class="container" style="max-width:750px;">
    <h2 style="margin-bottom:30px;">📦 Checkout</h2>

    <div style="display:flex; gap:30px; flex-wrap:wrap;">

        <!-- CUSTOMER FORM -->
        <div style="flex:2; min-width:280px;">
            <h3 style="margin-bottom:20px; color:#1a1a2e;">Your Details</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="john@example.com" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" placeholder="+250 700 000 000" required>
                </div>
                <div class="form-group">
                    <label>Delivery Address</label>
                    <textarea name="address" rows="3" placeholder="Kigali, Rwanda..." required
                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:15px;"></textarea>
                </div>
                <button type="submit" class="btn" style="width:100%; padding:15px; font-size:18px;">
                    ✅ Place Order
                </button>
            </form>
        </div>

        <!-- ORDER SUMMARY -->
        <div style="flex:1; min-width:250px;">
            <h3 style="margin-bottom:20px; color:#1a1a2e;">Order Summary</h3>
            <div style="background:white; border-radius:10px; padding:20px; box-shadow:0 2px 10px rgba(0,0,0,0.08);">
                <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $item):
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                    <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-size:14px;">
                        <span><?= htmlspecialchars($item['name']) ?> x<?= $item['quantity'] ?></span>
                        <span>RWF <?= number_format($subtotal) ?></span>
                    </div>
                <?php endforeach; ?>
                <hr style="margin:15px 0;">
                <div style="display:flex; justify-content:space-between; font-weight:bold; font-size:18px;">
                    <span>Total</span>
                    <span style="color:#e94560;">RWF <?= number_format($total) ?></span>
                </div>
            </div>
            <a href="/cart.php" style="display:block; text-align:center; margin-top:15px; color:#888;">
                ← Back to Cart
            </a>
        </div>

    </div>
</div>

<?php require 'includes/footer.php'; ?>