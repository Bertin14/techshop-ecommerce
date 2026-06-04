<?php require 'includes/db.php'; ?>
<?php require 'includes/header.php'; ?>

<section class="section">
    <h2>
        <?php
        if (isset($_GET['category'])) {
            $catId = (int)$_GET['category'];
            $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
            $stmt->execute([$catId]);
            $cat = $stmt->fetch(PDO::FETCH_ASSOC);
            echo htmlspecialchars($cat['name']) . " Products";
        } else {
            echo "All Products";
        }
        ?>
    </h2>

    <!-- FILTER BAR -->
    <div style="margin-bottom:25px; display:flex; gap:10px; flex-wrap:wrap;">
        <a href="/techshop/products.php" class="btn" style="background:#1a1a2e;">All</a>
        <?php
        $stmt = $pdo->query("SELECT * FROM categories");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($categories as $cat):
        ?>
            <a href="/techshop/products.php?category=<?= $cat['id'] ?>"
                class="btn"
                style="background: <?= (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? '#e94560' : '#555' ?>;">
                <?= htmlspecialchars($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- PRODUCTS GRID -->
    <div class="products-grid">
        <?php
        if (isset($_GET['category'])) {
            $catId = (int)$_GET['category'];
            $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ?");
            $stmt->execute([$catId]);
        } else {
            $stmt = $pdo->query("SELECT * FROM products");
        }
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($products) === 0):
        ?>
            <p>No products found in this category.</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="/techshop/assets/images/<?= htmlspecialchars($product['image']) ?>"
                        alt="<?= htmlspecialchars($product['name']) ?>"
                        onerror="this.src='https://placehold.co/300x180?text=No+Image'">
                    <div class="card-body">
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <p style="color:#888; font-size:13px; margin-bottom:8px;">
                            <?= htmlspecialchars(substr($product['description'], 0, 60)) ?>...
                        </p>
                        <p class="price">RWF <?= number_format($product['price']) ?></p>
                        <p style="font-size:13px; color: <?= $product['stock'] > 0 ? 'green' : 'red' ?>;">
                            <?= $product['stock'] > 0 ? '✅ In Stock' : '❌ Out of Stock' ?>
                        </p>
                        <a href="/techshop/product-detail.php?id=<?= $product['id'] ?>" class="btn" style="margin-top:10px;">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require 'includes/footer.php'; ?>