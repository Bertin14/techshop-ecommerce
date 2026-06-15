<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: /admin/login.php');
    exit;
}
?>
<?php
require '../includes/db.php';

// Get stats
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalCustomers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(total_amount) FROM orders")->fetchColumn();

// Get recent orders
$recentOrders = $pdo->query("
  SELECT o.id, o.total_amount, o.status, o.created_at, c.full_name
  FROM orders o
  JOIN customers c ON o.customer_id = c.id
  ORDER BY o.created_at DESC
  LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Get sales by category
$salesByCategory = $pdo->query("
  SELECT c.name, SUM(oi.quantity * oi.price) as total
  FROM order_items oi
  JOIN products p ON oi.product_id = p.id
  JOIN categories c ON p.category_id = c.id
  GROUP BY c.name
")->fetchAll(PDO::FETCH_ASSOC);

// Get top products
$topProducts = $pdo->query("
  SELECT p.name, SUM(oi.quantity) as total_sold
  FROM order_items oi
  JOIN products p ON oi.product_id = p.id
  GROUP BY p.name
  ORDER BY total_sold DESC
  LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Get orders per day (last 7 days)
$ordersPerDay = $pdo->query("
  SELECT DATE(created_at) as date, COUNT(*) as count, SUM(total_amount) as revenue
  FROM orders
  WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
  GROUP BY DATE(created_at)
  ORDER BY date ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0f0f1a;
            color: #eee;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 220px;
            height: 100vh;
            background: #1a1a2e;
            padding: 20px 0;
            z-index: 100;
        }

        .sidebar-logo {
            color: #e94560;
            font-size: 20px;
            font-weight: bold;
            padding: 0 20px 20px;
            border-bottom: 1px solid #333;
        }

        .sidebar a {
            display: block;
            color: #aaa;
            text-decoration: none;
            padding: 12px 20px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            color: #fff;
            background: rgba(233, 69, 96, 0.15);
            border-left: 3px solid #e94560;
        }

        .sidebar a i {
            margin-right: 10px;
            width: 16px;
        }

        /* MAIN */
        .main {
            margin-left: 220px;
            padding: 30px;
        }

        .page-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 25px;
            color: #fff;
        }

        /* STAT CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #1a1a2e;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #333;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }

        .stat-card.red::before {
            background: #e94560;
        }

        .stat-card.blue::before {
            background: #4361ee;
        }

        .stat-card.green::before {
            background: #1D9E75;
        }

        .stat-card.orange::before {
            background: #EF9F27;
        }

        .stat-icon {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #fff;
        }

        .stat-label {
            font-size: 13px;
            color: #aaa;
            margin-top: 4px;
        }

        /* CHARTS */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: #1a1a2e;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #333;
        }

        .chart-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #fff;
        }

        /* TABLE */
        .table-card {
            background: #1a1a2e;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 10px;
            font-size: 13px;
            color: #aaa;
            border-bottom: 1px solid #333;
        }

        td {
            padding: 10px;
            font-size: 14px;
            border-bottom: 1px solid #222;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-pending {
            background: rgba(239, 159, 39, 0.2);
            color: #EF9F27;
        }

        .badge-completed {
            background: rgba(29, 158, 117, 0.2);
            color: #1D9E75;
        }

        /* TOP BAR */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .back-btn {
            background: #e94560;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-logo">⚡ TechShop Admin</div>
        <a href="dashboard.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="/products.php"><i class="fas fa-box"></i> Products</a>
        <a href="/orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a href="/index.php"><i class="fas fa-store"></i> View Store</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main">

        <div class="topbar">
            <h1 class="page-title">📊 Admin Dashboard</h1>
            <div style="display:flex; gap:10px; align-items:center;">
                <span style="color:#aaa; font-size:14px;">👤 <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
                <a href="/admin/logout.php" style="background:#333; color:#aaa; padding:8px 16px; border-radius:6px; text-decoration:none; font-size:14px;">🚪 Logout</a>
                <a href="/index.php" class="back-btn">← Back to Store</a>
            </div>
        </div>

        <!-- STAT CARDS -->
        <div class="stats-grid">
            <div class="stat-card red">
                <div class="stat-icon">📦</div>
                <div class="stat-value"><?= $totalProducts ?></div>
                <div class="stat-label">Total Products</div>
            </div>
            <div class="stat-card blue">
                <div class="stat-icon">🛒</div>
                <div class="stat-value"><?= $totalOrders ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon">👥</div>
                <div class="stat-value"><?= $totalCustomers ?></div>
                <div class="stat-label">Total Customers</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-icon">💰</div>
                <div class="stat-value">RWF <?= number_format($totalRevenue ?? 0) ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>

        <!-- CHARTS -->
        <div class="charts-grid">
            <!-- Sales by Category -->
            <div class="chart-card">
                <div class="chart-title">💹 Sales by Category</div>
                <canvas id="categoryChart" height="200"></canvas>
            </div>

            <!-- Top Products -->
            <div class="chart-card">
                <div class="chart-title">🏆 Top Selling Products</div>
                <canvas id="productsChart" height="200"></canvas>
            </div>
        </div>

        <!-- RECENT ORDERS TABLE -->
        <div class="table-card">
            <div class="chart-title">🕐 Recent Orders</div>
            <?php if (empty($recentOrders)): ?>
                <p style="color:#aaa; text-align:center; padding:30px;">No orders yet.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td>#<?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['full_name']) ?></td>
                                <td>RWF <?= number_format($order['total_amount']) ?></td>
                                <td><span class="badge badge-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>

    <script>
        // Category Chart
        const categoryData = <?= json_encode($salesByCategory) ?>;
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: categoryData.map(d => d.name),
                datasets: [{
                    data: categoryData.map(d => d.total || 0),
                    backgroundColor: ['#e94560', '#4361ee', '#1D9E75', '#EF9F27', '#9b5de5'],
                    borderWidth: 0
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#aaa'
                        }
                    }
                }
            }
        });

        // Top Products Chart
        const productsData = <?= json_encode($topProducts) ?>;
        new Chart(document.getElementById('productsChart'), {
            type: 'bar',
            data: {
                labels: productsData.map(d => d.name.substring(0, 15)),
                datasets: [{
                    label: 'Units Sold',
                    data: productsData.map(d => d.total_sold || 0),
                    backgroundColor: '#e94560',
                    borderRadius: 6
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#aaa'
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#aaa'
                        },
                        grid: {
                            color: '#333'
                        }
                    },
                    y: {
                        ticks: {
                            color: '#aaa'
                        },
                        grid: {
                            color: '#333'
                        }
                    }
                }
            }
        });
    </script>

</body>

</html>