<?php
// includes/header.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection check for notifications
if (!isset($conn)) {
    $path_to_db = __DIR__ . '/../config/db.php';
    if (file_exists($path_to_db)) {
        require_once $path_to_db;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PuthiPustak- পুঁথিপুস্তক | The Transparent Book Marketplace</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/actions/') !== false) ? '../assets/css/style.css' : 'assets/css/style.css'; ?>">
</head>
<body class="<?= isset($_SESSION['theme']) ? $_SESSION['theme'] : 'light-mode'; ?>">

    <header>
        <div class="header-container">
            <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../index.php' : 'index.php'; ?>" class="logo">
                Puthi<span style="color: var(--primary);">Pustak.</span>
            </a>

            <nav class="main-nav">
                <a href="index.php" class="nav-link">Home</a>
                <a href="search.php" class="nav-link">Browse Books</a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                    <a href="messages.php" class="nav-link">Messages</a>

                    <?php
                        $notif_count = 0;
                        if(isset($conn) && isset($_SESSION['user_id'])) {
                            $u_id = $_SESSION['user_id'];
                            $notif_sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = $u_id AND is_read = 0";
                            $res = $conn->query($notif_sql);
                            if($res) {
                                $notif_count = $res->fetch_assoc()['count'];
                            }
                        }
                    ?>
                    <a href="notifications.php" class="nav-link" style="position: relative;">
                        Alerts
                        <?php if($notif_count > 0): ?>
                            <span style="background: #ef4444; color: white; font-size: 0.7rem; padding: 2px 6px; border-radius: 10px; position: relative; top: -2px;">
                                <?= $notif_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <?php if($_SESSION['role'] === 'seller'): ?>
                        <a href="add_book.php" class="nav-link">Sell a Book</a>
                    <?php endif; ?>

                    <?php if($_SESSION['role'] === 'admin'): ?>
                        <a href="admin/index.php" class="nav-link" style="color: var(--primary);">Admin</a>
                    <?php endif; ?>

                    <div class="user-menu">
                        <span class="user-name">Hi, <?= htmlspecialchars(substr($_SESSION['full_name'], 0, 10)); ?></span>
                        <a href="logout.php" class="btn btn-outline-sm">Logout</a>
                    </div>

                <?php else: ?>
                    <a href="login.php" class="nav-link">Login</a>
                    <a href="register.php" class="btn btn-primary-sm">Get Started</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>