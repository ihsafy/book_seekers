<?php
// dashboard.php
session_start();
require_once 'config/db.php';

// 1. Security Check: Is user logged in?
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$full_name = $_SESSION['full_name'];

include 'includes/header.php';
?>

<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    
    <?php if(isset($_SESSION['success'])): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #a7f3d0; text-align: center;">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <?php if(isset($_SESSION['error'])): ?>
        <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #fecaca; text-align: center;">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-family: 'Poppins', sans-serif; font-size: 2rem;">
                Dashboard
            </h1>
            <p style="color: var(--gray-text);">Welcome back, <strong><?= htmlspecialchars($full_name); ?></strong></p>
        </div>
        
        <?php if($role === 'seller'): ?>
            <a href="add_book.php" class="btn btn-primary" style="box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.4);">
                + Sell New Book
            </a>
        <?php else: ?>
            <a href="search.php" class="btn btn-primary">
                Browse Books
            </a>
        <?php endif; ?>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        
        <div class="book-card" style="padding: 1.5rem; display: flex; align-items: center;">
            <div style="font-size: 2rem; margin-right: 1rem;">🛡️</div>
            <div>
                <div style="font-size: 0.85rem; color: var(--gray-text); text-transform: uppercase; font-weight: 600;">Account Type</div>
                <div style="font-weight: 700; font-size: 1.1rem; text-transform: capitalize;"><?= $role; ?></div>
            </div>
        </div>

        <?php if($role === 'seller'): 
            // Fetch Seller Stats
            $sql_books = "SELECT COUNT(*) as total FROM books WHERE seller_id = $user_id";
            $count_books = $conn->query($sql_books)->fetch_assoc()['total'];
            
            $sql_sales = "SELECT SUM(amount) as earnings FROM transactions WHERE seller_id = $user_id";
            $earnings = $conn->query($sql_sales)->fetch_assoc()['earnings'] ?? 0;
        ?>
            <div class="book-card" style="padding: 1.5rem; display: flex; align-items: center;">
                <div style="font-size: 2rem; margin-right: 1rem;">📚</div>
                <div>
                    <div style="font-size: 0.85rem; color: var(--gray-text); text-transform: uppercase; font-weight: 600;">My Listings</div>
                    <div style="font-weight: 700; font-size: 1.1rem;"><?= $count_books; ?> Books</div>
                </div>
            </div>

            <div class="book-card" style="padding: 1.5rem; display: flex; align-items: center;">
                <div style="font-size: 2rem; margin-right: 1rem;">💰</div>
                <div>
                    <div style="font-size: 0.85rem; color: var(--gray-text); text-transform: uppercase; font-weight: 600;">Total Earnings</div>
                    <div style="font-weight: 700; font-size: 1.1rem; color: var(--secondary);">$<?= number_format($earnings, 2); ?></div>
                </div>
            </div>

        <?php else: // SEEKER STATS 
            $sql_wish = "SELECT COUNT(*) as total FROM wishlist WHERE user_id = $user_id";
            $count_wish = $conn->query($sql_wish)->fetch_assoc()['total'];
        ?>
            <div class="book-card" style="padding: 1.5rem; display: flex; align-items: center;">
                <div style="font-size: 2rem; margin-right: 1rem;">❤️</div>
                <div>
                    <div style="font-size: 0.85rem; color: var(--gray-text); text-transform: uppercase; font-weight: 600;">Wishlist</div>
                    <div style="font-weight: 700; font-size: 1.1rem;"><?= $count_wish; ?> Saved Items</div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if($role === 'seller'): ?>
        <h2 style="font-family: 'Poppins', sans-serif; margin-bottom: 1.5rem;">My Active Listings</h2>
        
        <?php
        $my_books_sql = "SELECT * FROM books WHERE seller_id = $user_id ORDER BY created_at DESC";
        $my_books = $conn->query($my_books_sql);
        ?>

        <?php if($my_books->num_rows > 0): ?>
            <div class="grid-books">
                <?php while($book = $my_books->fetch_assoc()): ?>
                    <div class="book-card">
                        <div class="card-img-placeholder" style="height: 180px;">
                             <?php if($book['image_url']): ?>
                                <img src="uploads/books/<?= htmlspecialchars($book['image_url']); ?>" alt="Cover" style="width:100%; height:100%; object-fit:cover;">
                            <?php else: ?>
                                <span>No Image</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body">
                            <h4 style="margin-bottom: 0.5rem;"><?= htmlspecialchars($book['title']); ?></h4>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <span class="price-tag">$<?= $book['price']; ?></span>
                                <span style="font-size: 0.8rem; padding: 2px 8px; background: #e2e8f0; border-radius: 4px; font-weight: 600;">
                                    <?= $book['status']; ?>
                                </span>
                            </div>

                            <div style="display: flex; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 10px;">
                                <a href="book_details.php?id=<?= $book['book_id']; ?>" class="btn btn-primary-sm" style="flex: 1; text-align: center; margin:0;">View</a>
                                
                                <a href="actions/delete_my_book.php?id=<?= $book['book_id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this listing?');"
                                   style="color: #ef4444; border: 1px solid #ef4444; padding: 0.4rem 0.8rem; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                                   Delete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; background: var(--white); border-radius: var(--radius); color: var(--gray-text);">
                <p>You haven't listed any books yet.</p>
                <br>
                <a href="add_book.php" class="btn btn-primary-sm" style="margin: 0;">Start Selling</a>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <h2 style="font-family: 'Poppins', sans-serif; margin-bottom: 1.5rem;">My Wishlist</h2>
        
        <?php
        // JOIN query to get book details from the wishlist table
        $wish_sql = "SELECT books.*, wishlist.wishlist_id 
                     FROM wishlist 
                     JOIN books ON wishlist.book_id = books.book_id 
                     WHERE wishlist.user_id = $user_id";
        $wish_items = $conn->query($wish_sql);
        ?>

        <?php if($wish_items->num_rows > 0): ?>
            <div class="grid-books">
                <?php while($book = $wish_items->fetch_assoc()): ?>
                    <div class="book-card">
                        <div class="card-img-placeholder" style="height: 180px;">
                             <?php if($book['image_url']): ?>
                                <img src="uploads/books/<?= htmlspecialchars($book['image_url']); ?>" alt="Cover" style="width:100%; height:100%; object-fit:cover;">
                            <?php else: ?>
                                <span>No Image</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h4 style="margin-bottom: 0.5rem;"><?= htmlspecialchars($book['title']); ?></h4>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span class="price-tag">$<?= $book['price']; ?></span>
                                <a href="book_details.php?id=<?= $book['book_id']; ?>" class="btn btn-primary-sm" style="padding: 0.4rem 1rem; font-size: 0.8rem;">View</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; background: var(--white); border-radius: var(--radius); color: var(--gray-text);">
                <p>Your wishlist is empty.</p>
                <br>
                <a href="search.php" class="btn btn-primary-sm" style="margin: 0;">Find Books</a>
            </div>
        <?php endif; ?>

    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>