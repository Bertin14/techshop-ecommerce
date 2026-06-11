<?php require 'includes/db.php'; ?>
<?php require 'includes/header.php'; ?>
<?php
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== '') {
    echo "<script>window.onload = function(){ document.getElementById('searchInput').value = '" . htmlspecialchars($search) . "'; filterProducts(); }</script>";
}
?>

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

    <!-- SEARCH BAR -->
    <div style="margin-bottom:20px;">
        <input type="text" id="searchInput" placeholder="🔍 Search products..."
            onkeyup="filterProducts()"
            style="width:100%; max-width:400px; padding:12px 20px; border:2px solid #ddd;
             border-radius:8px; font-size:16px; outline:none;">
    </div>

    <!-- FILTER BAR -->
    <div style="margin-bottom:25px; display:flex; gap:10px; flex-wrap:wrap;">
        <a href="/products.php" class="btn" style="background:#1a1a2e;">All</a>
        <?php
        $stmt = $pdo->query("SELECT * FROM categories");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($categories as $cat):
        ?>
            <a href="/products.php?category=<?= $cat['id'] ?>"
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
            <?php foreach ($products as $product):
                $badges = ['New', 'Hot', 'Sale', 'New', 'Hot', 'Sale', 'New', 'Hot'];
                $badgeClasses = ['badge-new', 'badge-hot', 'badge-sale', 'badge-new', 'badge-hot', 'badge-sale', 'badge-new', 'badge-hot'];
                $i = ($product['id'] - 1) % 8;
                $img = $product['image'];
                $src = (strpos($img, 'http') === 0) ? $img : '/assets/images/' . htmlspecialchars($img);
            ?>
                <div class="product-card">
                    <span class="product-badge <?= $badgeClasses[$i] ?>"><?= $badges[$i] ?></span>
                    <img src="<?= $src ?>"
                        alt="<?= htmlspecialchars($product['name']) ?>"
                        onerror="this.src='https://placehold.co/300x200?text=No+Image'">
                    <div class="card-body">
                        <div class="stars">★★★★☆</div>
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <p style="color:#888; font-size:13px; margin-bottom:8px;">
                            <?= htmlspecialchars(substr($product['description'], 0, 55)) ?>...
                        </p>
                        <p class="price">RWF <?= number_format($product['price']) ?></p>
                        <p style="font-size:13px; color: <?= $product['stock'] > 0 ? 'green' : 'red' ?>;">
                            <?= $product['stock'] > 0 ? '✅ In Stock' : '❌ Out of Stock' ?>
                        </p>
                        <a href="/product-detail.php?id=<?= $product['id'] ?>" class="btn" style="margin-top:10px;">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<script>
    function filterProducts() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const cards = document.querySelectorAll('.product-card');
        cards.forEach(card => {
            const name = card.querySelector('h3').textContent.toLowerCase();
            card.style.display = name.includes(input) ? 'block' : 'none';
        });
    }
</script>

<?php require 'includes/footer.php'; ?>