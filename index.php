<?php require 'includes/db.php'; ?>
<?php require 'includes/header.php'; ?>

<!-- HERO SECTION -->
<section class="hero">
    <h1>Welcome to ⚡ TechShop</h1>
    <p>Your #1 Electronics Store in Rwanda — Fast Delivery, Best Prices</p>

    <form action="/techshop/products.php" method="GET" class="hero-search">
        <input type="text" name="search" placeholder="Search for laptops, phones, accessories...">
        <button type="submit">Search</button>
    </form>

    <a href="/techshop/products.php" class="btn">Browse All Products</a>

    <!-- ANIMATED STATS -->
    <div class="hero-stats">
        <div class="stat-item">
            <div class="stat-number" id="stat-products">0</div>
            <div class="stat-label">Products</div>
        </div>
        <div class="stat-item">
            <div class="stat-number" id="stat-categories">0</div>
            <div class="stat-label">Categories</div>
        </div>
        <div class="stat-item">
            <div class="stat-number" id="stat-orders">0</div>
            <div class="stat-label">Orders Delivered</div>
        </div>
    </div>
</section>

<script>
    function animateCounter(id, target, duration) {
        let start = 0;
        const step = target / (duration / 16);
        const el = document.getElementById(id);
        const timer = setInterval(() => {
            start += step;
            if (start >= target) {
                el.textContent = target + '+';
                clearInterval(timer);
            } else {
                el.textContent = Math.floor(start);
            }
        }, 16);
    }
    window.addEventListener('load', () => {
        animateCounter('stat-products', 50, 1500);
        animateCounter('stat-categories', 5, 1000);
        animateCounter('stat-orders', 200, 2000);
    });
</script>

<!-- SEARCH BAR -->
<form action="/techshop/products.php" method="GET" style="margin-top:25px; display:flex; justify-content:center; gap:0;">
    <input type="text" name="search" placeholder="🔍 Search for laptops, phones..."
        style="width:100%; max-width:420px; padding:14px 20px; border:none;
             border-radius:8px 0 0 8px; font-size:16px; outline:none;">
    <button type="submit"
        style="background:#e94560; color:white; border:none; padding:14px 24px;
             border-radius:0 8px 8px 0; font-size:16px; cursor:pointer;">
        Search
    </button>
</form>

<a href="/techshop/products.php" class="btn" style="margin-top:20px;">Browse All Products</a>
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
                <?php
                $badges = ['New', 'Hot', 'Sale', 'New', 'Hot', 'Sale', 'New', 'Hot'];
                $badgeClasses = ['badge-new', 'badge-hot', 'badge-sale', 'badge-new', 'badge-hot', 'badge-sale', 'badge-new', 'badge-hot'];
                $i = ($product['id'] - 1) % 8;
                echo "<span class='product-badge {$badgeClasses[$i]}'>{$badges[$i]}</span>";
                ?>
                <img src="/techshop/assets/images/<?= htmlspecialchars($product['image']) ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>"
                    onerror="this.src='https://placehold.co/300x200?text=No+Image'">
                <div class="card-body">
                    <div class="stars">★★★★☆</div>
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p style="color:#888; font-size:13px; margin-bottom:8px;">
                        <?= htmlspecialchars(substr($product['description'], 0, 55)) ?>...
                    </p>
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