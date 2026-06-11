<?php
require 'includes/db.php';
require 'includes/header.php';

if (!isset($_GET['order_id'])) {
    header('Location: /index.php');
    exit;
}

$order_id = (int)$_GET['order_id'];

// Get order details
$stmt = $pdo->prepare("
  SELECT o.*, c.full_name, c.email, c.phone, c.address
  FROM orders o
  JOIN customers c ON o.customer_id = c.id
  WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Get order items
$stmt = $pdo->prepare("
  SELECT oi.*, p.name, p.image
  FROM order_items oi
  JOIN products p ON oi.product_id = p.id
  WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container" style="max-width:700px; text-align:center;">

    <!-- SUCCESS MESSAGE -->
    <div style="background:#d4edda; border-radius:10px; padding:40px; margin-bottom:30px;">
        <div style="font-size:60px;">✅</div>
        <h1 style="color:#155724; margin:15px 0;">Order Placed Successfully!</h1>
        <p style="color:#155724; font-size:16px;">
            Thank you, <strong><?= htmlspecialchars($order['full_name']) ?></strong>!
            Your order <strong>#<?= $order_id ?></strong> has been received.
        </p>
    </div>

    <!-- ORDER DETAILS -->
    <div style="background:white; border-radius:10px; padding:30px; box-shadow:0 2px 10px rgba(0,0,0,0.08); text-align:left;">
        <h3 style="margin-bottom:20px; color:#1a1a2e;">📋 Order Details</h3>

        <div style="display:flex; gap:20px; flex-wrap:wrap; margin-bottom:20px;">
            <div style="flex:1;">
                <p><strong>Order ID:</strong> #<?= $order_id ?></p>
                <p><strong>Name:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
            </div>
            <div style="flex:1;">
                <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
                <p><strong>Status:</strong> <span style="color:orange; font-weight:bold;">⏳ Pending</span></p>
            </div>
        </div>

        <hr style="margin-bottom:20px;">

        <h4 style="margin-bottom:15px;">Items Ordered:</h4>
        <?php foreach ($items as $item): ?>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <img src="/assets/images/<?= htmlspecialchars($item['image']) ?>"
                        onerror="this.src='https://placehold.co/50x50?text=?'"
                        style="width:50px; height:50px; object-fit:cover; border-radius:6px;">
                    <span><?= htmlspecialchars($item['name']) ?> x<?= $item['quantity'] ?></span>
                </div>
                <span style="font-weight:bold;">RWF <?= number_format($item['price'] * $item['quantity']) ?></span>
            </div>
        <?php endforeach; ?>

        <hr style="margin:15px 0;">
        <div style="display:flex; justify-content:space-between; font-size:20px; font-weight:bold;">
            <span>Total Paid</span>
            <span style="color:#e94560;">RWF <?= number_format($order['total_amount']) ?></span>
        </div>
    </div>

    <a href="/index.php" class="btn" style="margin-top:30px; font-size:16px;">
        🏠 Back to Home
    </a>
</div>

<?php require 'includes/footer.php'; ?>