<?php
session_start();

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: /admin/dashboard.php');
    exit;
}

require '../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: /admin/dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — TechShop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0f0f1a;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-box {
            background: #1a1a2e;
            padding: 40px;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            border: 1px solid #333;
        }

        .login-logo {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: #e94560;
            margin-bottom: 8px;
        }

        .login-subtitle {
            text-align: center;
            color: #aaa;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #ccc;
            font-size: 14px;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            background: #0f0f1a;
            border: 1px solid #444;
            border-radius: 8px;
            color: #fff;
            font-size: 15px;
            transition: border 0.2s;
        }

        .form-group input:focus {
            border-color: #e94560;
            outline: none;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #e94560;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-login:hover {
            background: #c73652;
        }

        .error {
            background: rgba(233, 69, 96, 0.15);
            border: 1px solid #e94560;
            color: #e94560;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #aaa;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            color: #e94560;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <div class="login-logo">⚡ TechShop</div>
        <div class="login-subtitle">Admin Panel — Sign In</div>

        <?php if ($error): ?>
            <div class="error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter username" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn-login">🔐 Sign In</button>
        </form>

        <a href="/index.php" class="back-link">← Back to Store</a>
    </div>
</body>

</html>