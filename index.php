<?php require 'includes/db.php'; ?>
<?php require 'includes/header.php'; ?>

<!-- HERO SECTION -->
<section class="hero">
    <h1>Welcome to ⚡ TechShop</h1>
    <p>Your #1 Electronics Store in Rwanda</p>
    <a href="/techshop/products.php" class="btn">Shop Now</a>
</section>

<!-- FEATURED PRODUCTS -->
<section class="section">
    <h2>Featured Products</h2>
    <div class="products-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM products LIMIT 8");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $product):
        ?>
            <div class="product-card">
                <img src="/techshop/assets/images/<?= htmlspecialchars($product['image']) ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>"
                    onerror="this.src='https://placehold.co/300x180?text=No+Image'">
                <div class="card-body">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p class="price">RWF <?= number_format($product['price']) ?></p>
                    <a href="/techshop/product-detail.php?id=<?= $product['id'] ?>" class="btn">View Details</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- CATEGORIES SECTION -->
<section class="section" style="background:#fff;">
    <h2>Shop by Category</h2>
    <div class="products-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM categories");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($categories as $cat):
        ?>
            <a href="/techshop/products.php?category=<?= $cat['id'] ?>" style="text-decoration:none;">
                <div class="product-card" style="text-align:center; padding:30px;">
                    <div class="card-body">
                        <h3 style="font-size:20px;">
                            <?php
                            $icons = ['💻', '📱', '🔌', '📺', '🎧'];
                            echo $icons[($cat['id'] - 1) % 5] . ' ' . htmlspecialchars($cat['name']);
                            ?>
                        </h3>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<?php require 'includes/footer.php'; ?>