<?php
// book_details.php
session_start();
require_once 'config/db.php';

// 1. Get Book ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: search.php");
    exit();
}

$book_id = intval($_GET['id']);
$current_user_id = $_SESSION['user_id'] ?? 0;

// 2. Fetch Book & Seller Details (Using JOIN)
$sql = "SELECT b.*, u.full_name as seller_name, u.email as seller_email, u.is_verified 
        FROM books b 
        JOIN users u ON b.seller_id = u.user_id 
        WHERE b.book_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<h3>Book not found.</h3><a href='search.php'>Go Back</a>");
}

$book = $result->fetch_assoc();

// 3. Check if Book is in User's Wishlist
$in_wishlist = false;
if ($current_user_id > 0) {
    $wish_sql = "SELECT wishlist_id FROM wishlist WHERE user_id = ? AND book_id = ?";
    $w_stmt = $conn->prepare($wish_sql);
    $w_stmt->bind_param("ii", $current_user_id, $book_id);
    $w_stmt->execute();
    if ($w_stmt->get_result()->num_rows > 0) {
        $in_wishlist = true;
    }
}

include 'includes/header.php';
?>

<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    
    <?php if(isset($_SESSION['error'])): ?>
        <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; text-align: center;">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <?php if(isset($_SESSION['success'])): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; text-align: center;">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div style="margin-bottom: 2rem; color: var(--gray-text); font-size: 0.9rem;">
        <a href="index.php" style="text-decoration: none; color: var(--gray-text);">Home</a> / 
        <a href="search.php" style="text-decoration: none; color: var(--gray-text);">Books</a> / 
        <span style="color: var(--dark); font-weight: 600;"><?= htmlspecialchars($book['title']); ?></span>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 3rem; animation: fadeInUp 0.6s ease-out;">
        
        <div class="book-card" style="padding: 1rem; height: fit-content;">
            <div style="background: #f1f5f9; border-radius: 8px; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                 <?php if($book['image_url']): ?>
                    <img src="uploads/books/<?= htmlspecialchars($book['image_url']); ?>" alt="Book Cover" style="max-width: 100%; height: auto; max-height: 500px; object-fit: contain;">
                <?php else: ?>
                    <div style="height: 400px; width: 100%; display: flex; align-items: center; justify-content: center; color: #94a3b8;">No Image Available</div>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <div style="margin-bottom: 1rem;">
                <?php if($book['status'] === 'Available'): ?>
                    <span style="background: #d1fae5; color: #065f46; padding: 5px 12px; border-radius: 50px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase;">Available</span>
                <?php elseif($book['status'] === 'Reserved'): ?>
                    <span style="background: #ffedd5; color: #9a3412; padding: 5px 12px; border-radius: 50px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase;">Reserved</span>
                <?php else: ?>
                    <span style="background: #f1f5f9; color: #64748b; padding: 5px 12px; border-radius: 50px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase;">Sold</span>
                <?php endif; ?>
            </div>

            <h1 style="font-family: 'Poppins', sans-serif; font-size: 2.5rem; line-height: 1.2; margin-bottom: 0.5rem;">
                <?= htmlspecialchars($book['title']); ?>
            </h1>
            
            <p style="font-size: 1.2rem; color: var(--gray-text); margin-bottom: 1.5rem;">
                by <span style="color: var(--dark); font-weight: 600;"><?= htmlspecialchars($book['author']); ?></span>
            </p>

            <div style="font-size: 2rem; font-weight: 800; color: var(--primary); margin-bottom: 2rem;">
                $<?= number_format($book['price'], 2); ?>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; padding: 1.5rem 0;">
                <div>
                    <span style="display: block; font-size: 0.85rem; color: var(--gray-text); text-transform: uppercase; font-weight: 600;">Genre</span>
                    <span style="font-size: 1.1rem;"><?= htmlspecialchars($book['genre']); ?></span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.85rem; color: var(--gray-text); text-transform: uppercase; font-weight: 600;">Condition</span>
                    <span style="font-size: 1.1rem;"><?= htmlspecialchars($book['book_condition']); ?></span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.85rem; color: var(--gray-text); text-transform: uppercase; font-weight: 600;">Location</span>
                    <span style="font-size: 1.1rem;">📍 <?= htmlspecialchars($book['location'] ?? 'Unknown'); ?></span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.85rem; color: var(--gray-text); text-transform: uppercase; font-weight: 600;">Posted</span>
                    <span style="font-size: 1.1rem;"><?= date("M d, Y", strtotime($book['created_at'])); ?></span>
                </div>
            </div>

            <div style="margin-bottom: 2.5rem;">
                <h3 style="font-family: 'Poppins', sans-serif; margin-bottom: 0.5rem;">Description</h3>
                <p style="color: var(--gray-text); line-height: 1.7;">
                    <?= nl2br(htmlspecialchars($book['description'])); ?>
                </p>
            </div>

            <?php if($current_user_id == 0): ?>
                <div style="background: #f8fafc; padding: 1.5rem; border-radius: 12px; text-align: center;">
                    <p style="margin-bottom: 1rem;">Please login to buy this book.</p>
                    <a href="login.php" class="btn btn-primary">Login Now</a>
                </div>
            
            <?php elseif($current_user_id == $book['seller_id']): ?>
                <div style="background: #f0fdf4; padding: 1rem; border-radius: 8px; color: #166534; border: 1px solid #bbf7d0;">
                    <strong>Listing Owner:</strong> This is your book.
                </div>

            <?php else: ?>
                <?php if($book['status'] === 'Available'): ?>
                    
                    <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 1.5rem;">
                        <a href="checkout.php?book_id=<?= $book['book_id']; ?>" class="btn" style="background-color: #10b981; color: white; padding: 1rem 2rem; font-size: 1.1rem; text-decoration: none; border-radius: 8px; font-weight: 600;">
                            Buy Now
                        </a>
                        </div>

                <?php else: ?>
                    <button class="btn" style="background: #cbd5e1; cursor: not-allowed; margin-bottom: 1.5rem;" disabled>
                        Currently Unavailable
                    </button>
                <?php endif; ?>

                <form action="actions/wishlist_action.php" method="POST">
                    <input type="hidden" name="book_id" value="<?= $book['book_id']; ?>">
                    <?php if($in_wishlist): ?>
                        <button type="submit" class="btn btn-outline" style="border-color: #ef4444; color: #ef4444;">
                            Remove from Wishlist
                        </button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-outline">
                            ❤️ Add to Wishlist
                        </button>
                    <?php endif; ?>
                </form>

            <?php endif; ?>

            <div style="margin-top: 3rem; padding: 1.5rem; border: 1px solid #e2e8f0; border-radius: 12px; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center;">
                    <div style="width: 50px; height: 50px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem; margin-right: 1rem;">
                        <?= strtoupper(substr($book['seller_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 1.1rem;">
                            <?= htmlspecialchars($book['seller_name']); ?>
                            <?php if($book['is_verified']): ?>
                                <span title="Verified Seller" style="color: var(--primary); margin-left: 5px;">✓</span>
                            <?php endif; ?>
                        </div>
                        <div style="color: var(--gray-text); font-size: 0.9rem;">Verified Seller</div>
                    </div>
                </div>

                <?php if($current_user_id != $book['seller_id'] && $current_user_id > 0): ?>
                    <a href="chat.php?user_id=<?= $book['seller_id']; ?>" class="btn btn-outline" style="border-radius: 50px;">
                        ✉️ Contact Seller
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>