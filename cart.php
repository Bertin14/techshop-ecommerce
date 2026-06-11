<?php
require 'includes/db.php';
require 'includes/header.php';

// HANDLE ACTIONS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $product_id = (int)$_POST['product_id'];

    if ($action === 'add') {
        $quantity = (int)$_POST['quantity'];
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($product) {
                $_SESSION['cart'][$product_id] = [
                    'name'     => $product['name'],
                    'price'    => $product['price'],
                    'image'    => $product['image'],
                    'quantity' => $quantity
                ];
            }
        }
        header('Location: /cart.php');
        exit;
    }

    if ($action === 'update') {
        $quantity = (int)$_POST['quantity'];
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
        } else {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        }
        header('Location: /cart.php');
        exit;
    }

    if ($action === 'remove') {
        unset($_SESSION['cart'][$product_id]);
        header('Location: /cart.php');
        exit;
    }
}
?>

<div class="container">
    <h2 style="margin-bottom:30px;">🛒 Your Shopping Cart</h2>

    <?php if (empty($_SESSION['cart'])): ?>
        <div style="text-align:center; padding:60px;">
            <p style="font-size:20px; color:#888;">Your cart is empty.</p>
            <a href="/products.php" class="btn" style="margin-top:20px;">Continue Shopping</a>
        </div>

    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $product_id => $item):
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                    <tr>
                        <td style="display:flex; align-items:center; gap:15px; text-align:left;">
                            <img src="/assets/images/<?= htmlspecialchars($item['image']) ?>"
                                onerror="this.src='https://placehold.co/60x60?text=?'"
                                style="width:60px; height:60px; object-fit:cover; border-radius:6px;">
                            <span><?= htmlspecialchars($item['name']) ?></span>
                        </td>
                        <td>RWF <?= number_format($item['price']) ?></td>
                        <td>
                            <form method="POST" style="display:flex; align-items:center; gap:8px; justify-content:center;">
                                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                <input type="hidden" name="action" value="update">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>"
                                    min="1" style="width:60px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:center;">
                                <button type="submit" class="btn" style="padding:6px 12px; font-size:13px;">Update</button>
                            </form>
                        </td>
                        <td>RWF <?= number_format($subtotal) ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                <input type="hidden" name="action" value="remove">
                                <button type="submit" style="background:#e94560; color:white; border:none; padding:8px 14px; border-radius:6px; cursor:pointer;">
                                    🗑 Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- TOTAL & CHECKOUT -->
        <div class="total-box" style="margin-top:30px;">
            <p style="font-size:24px; color:#1a1a2e;">
                Total: <span style="color:#e94560;">RWF <?= number_format($total) ?></span>
            </p>
            <div style="display:flex; gap:15px; justify-content:flex-end; margin-top:20px;">
                <a href="/products.php" class="btn" style="background:#555;">Continue Shopping</a>
                <a href="/checkout.php" class="btn">Proceed to Checkout →</a>
            </div>
        </div>

    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>