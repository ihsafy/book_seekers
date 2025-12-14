<?php
// admin/index.php
session_start();
// Go up one level to find config
require_once '../config/db.php';

// 1. Security Check: Is this user an Admin?
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // If not admin, kick them out to login page
    header("Location: ../login.php");
    exit();
}

// 2. Analytics Queries
// Count Users
$user_sql = "SELECT COUNT(*) as total FROM users WHERE role != 'admin'";
$total_users = $conn->query($user_sql)->fetch_assoc()['total'];

// Count Books
$book_sql = "SELECT COUNT(*) as total FROM books";
$total_books = $conn->query($book_sql)->fetch_assoc()['total'];

// Count Sales (Total Transaction Value)
$sales_sql = "SELECT COALESCE(SUM(amount), 0) as total FROM transactions";
$total_sales = $conn->query($sales_sql)->fetch_assoc()['total'];

// Count Active Reservations
$res_sql = "SELECT COUNT(*) as total FROM books WHERE status = 'Reserved'";
$active_reservations = $conn->query($res_sql)->fetch_assoc()['total'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Book Seekers</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <style>
        /* Admin Specific Overrides */
        body { background-color: #f1f5f9; }
        .admin-header { background: #1e293b; border-bottom: none; }
        .admin-logo { color: white; }
        .stat-card { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .stat-number { font-size: 2.5rem; font-weight: 700; color: #1e293b; margin-top: 10px; }
        .stat-label { font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .manage-table th { font-size: 0.9rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    </style>
</head>
<body>

    <header class="admin-header">
        <div class="header-container">
            <a href="#" class="logo admin-logo">Admin Panel</a>
            <nav>
                <a href="../index.php" style="color: #94a3b8; text-decoration: none; margin-right: 20px; font-size: 0.9rem;">View Main Site</a>
                <a href="../logout.php" class="btn" style="background: #ef4444; color: white; padding: 0.5rem 1.2rem; font-size: 0.9rem; border-radius: 50px;">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
        
        <?php if(isset($_GET['msg'])): ?>
            <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #a7f3d0;">
                <?= htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #fecaca;">
                <?= htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <h1 style="font-family: 'Poppins', sans-serif; margin-bottom: 2rem; color: #1e293b;">Platform Overview</h1>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            
            <div class="stat-card" style="border-left: 5px solid var(--primary);">
                <div class="stat-label">Total Users</div>
                <div class="stat-number"><?= $total_users; ?></div>
            </div>

            <div class="stat-card" style="border-left: 5px solid #10b981;">
                <div class="stat-label">Total Books</div>
                <div class="stat-number"><?= $total_books; ?></div>
            </div>

            <div class="stat-card" style="border-left: 5px solid #f59e0b;">
                <div class="stat-label">Total Sales Volume</div>
                <div class="stat-number" style="color: #f59e0b;">$<?= number_format($total_sales, 2); ?></div>
            </div>

            <div class="stat-card" style="border-left: 5px solid #ec4899;">
                <div class="stat-label">Active Reservations</div>
                <div class="stat-number"><?= $active_reservations; ?></div>
            </div>

        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 3rem; margin-top: 3rem;">
            
            <div class="book-card" style="padding: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <h3 style="font-family: 'Poppins', sans-serif; margin-bottom: 1.5rem; color: var(--dark);">Newest Members</h3>
                
                <?php
                // Fetch All Users (except the current admin)
                $all_users_sql = "SELECT * FROM users WHERE user_id != " . $_SESSION['user_id'] . " ORDER BY created_at DESC";
                $all_users = $conn->query($all_users_sql);
                ?>

                <div style="overflow-x: auto;">
                    <table class="manage-table" style="width: 100%; border-collapse: collapse; min-width: 600px;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 12px 10px;">Name</th>
                                <th style="padding: 12px 10px;">Email</th>
                                <th style="padding: 12px 10px;">Role</th>
                                <th style="padding: 12px 10px;">Joined</th>
                                <th style="padding: 12px 10px; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($all_users->num_rows > 0): ?>
                                <?php while($u = $all_users->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 14px 10px; font-weight: 600; color: #1e293b;"><?= htmlspecialchars($u['full_name']); ?></td>
                                    <td style="padding: 14px 10px; color: #64748b;"><?= htmlspecialchars($u['email']); ?></td>
                                    <td style="padding: 14px 10px;">
                                        <span style="padding: 4px 12px; border-radius: 50px; font-size: 0.8rem; background: #e0e7ff; color: var(--primary); font-weight: 700;">
                                            <?= ucfirst($u['role']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 14px 10px; font-size: 0.9rem; color: #64748b;"><?= date("M d, Y", strtotime($u['created_at'])); ?></td>
                                    <td style="padding: 14px 10px; text-align: right;">
                                        <a href="delete_user.php?id=<?= $u['user_id']; ?>" 
                                           onclick="return confirm('Are you sure? This will delete the user and potentially their listings.');"
                                           style="color: #ef4444; text-decoration: none; font-weight: 600; font-size: 0.85rem; padding: 6px 12px; background: #fef2f2; border-radius: 6px; border: 1px solid #fee2e2;">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="padding: 20px; text-align: center; color: #94a3b8;">No other users found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="book-card" style="padding: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <h3 style="font-family: 'Poppins', sans-serif; margin-bottom: 1.5rem; color: var(--dark);">Manage Book Posts</h3>
                
                <?php
                // Fetch All Books with Seller Name
                $all_books_sql = "SELECT books.*, users.full_name as seller_name FROM books JOIN users ON books.seller_id = users.user_id ORDER BY created_at DESC";
                $all_books = $conn->query($all_books_sql);
                ?>

                <div style="overflow-x: auto;">
                    <table class="manage-table" style="width: 100%; border-collapse: collapse; min-width: 600px;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 12px 10px;">Book Title</th>
                                <th style="padding: 12px 10px;">Posted By</th>
                                <th style="padding: 12px 10px;">Status</th>
                                <th style="padding: 12px 10px;">Price</th>
                                <th style="padding: 12px 10px; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($all_books->num_rows > 0): ?>
                                <?php while($b = $all_books->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 14px 10px; font-weight: 600;">
                                        <a href="../book_details.php?id=<?= $b['book_id']; ?>" target="_blank" style="text-decoration: none; color: #1e293b;">
                                            <?= htmlspecialchars($b['title']); ?> ↗
                                        </a>
                                    </td>
                                    <td style="padding: 14px 10px; color: #64748b;"><?= htmlspecialchars($b['seller_name']); ?></td>
                                    <td style="padding: 14px 10px;">
                                        <span style="font-size: 0.85rem; font-weight: 700; color: <?= $b['status']=='Available' ? '#10b981' : '#f59e0b'; ?>">
                                            <?= $b['status']; ?>
                                        </span>
                                    </td>
                                    <td style="padding: 14px 10px; font-weight: 600;">$<?= number_format($b['price'], 2); ?></td>
                                    <td style="padding: 14px 10px; text-align: right;">
                                        <a href="delete_book.php?id=<?= $b['book_id']; ?>" 
                                           onclick="return confirm('Delete this book listing permanently?');"
                                           style="color: #ef4444; text-decoration: none; font-weight: 600; font-size: 0.85rem; padding: 6px 12px; background: #fef2f2; border-radius: 6px; border: 1px solid #fee2e2;">
                                            Remove
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="padding: 20px; text-align: center; color: #94a3b8;">No books listed yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</body>
</html>