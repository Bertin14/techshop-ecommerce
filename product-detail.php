<?php
require 'includes/db.php';
require 'includes/header.php';

if (!isset($_GET['id'])) {
    header('Location: /techshop/products.php');
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p 
                        JOIN categories c ON p.category_id = c.id 
                        WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<div class='container'><p>Product not found.</p></div>";
    require 'includes/footer.php';
    exit;
}
?>

<div class="container" style="display:flex; gap:40px; flex-wrap:wrap; margin-top:50px;">
    <!-- PRODUCT IMAGE -->
    <div style="flex:1; min-width:280px;">
        <img src="/techshop/assets/images/<?= htmlspecialchars($product['image']) ?>"
            alt="<?= htmlspecialchars($product['name']) ?>"
            onerror="this.src='https://placehold.co/400x300?text=No+Image'"
            style="width:100%; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
    </div>

    <!-- PRODUCT INFO -->
    <div style="flex:2; min-width:280px;">
        <p style="color:#e94560; margin-bottom:8px;"><?= htmlspecialchars($product['category_name']) ?></p>
        <h1 style="font-size:30px; margin-bottom:15px;"><?= htmlspecialchars($product['name']) ?></h1>
        <p style="color:#555; font-size:16px; line-height:1.7; margin-bottom:20px;">
            <?= htmlspecialchars($product['description']) ?>
        </p>
        <h2 style="color:#e94560; font-size:32px; margin-bottom:10px;">
            RWF <?= number_format($product['price']) ?>
        </h2>
        <p style="margin-bottom:25px; color: <?= $product['stock'] > 0 ? 'green' : 'red' ?>; font-size:15px;">
            <?= $product['stock'] > 0 ? '✅ In Stock (' . $product['stock'] . ' available)' : '❌ Out of Stock' ?>
        </p>

        <?php if ($product['stock'] > 0): ?>
            <form action="/techshop/cart.php" method="POST" style="display:flex; gap:15px; align-items:center; flex-wrap:wrap;">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="action" value="add">
                <div style="display:flex; align-items:center; gap:10px;">
                    <label style="font-weight:bold;">Qty:</label>
                    <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>"
                        style="width:70px; padding:8px; border:1px solid #ddd; border-radius:6px; font-size:16px;">
                </div>
                <button type="submit" class="btn" style="font-size:16px; padding:12px 30px;">
                    🛒 Add to Cart
                </button>
            </form>
        <?php endif; ?>

        <a href="/techshop/products.php" style="display:inline-block; margin-top:20px; color:#888;">
            ← Back to Products
        </a>
    </div>
</div>

<?php require 'includes/footer.php'; ?>