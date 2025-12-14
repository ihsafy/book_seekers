<?php
// checkout.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please login to claim or buy books.";
    header("Location: login.php");
    exit();
}

if (!isset($_GET['book_id'])) {
    header("Location: search.php");
    exit();
}

$book_id = intval($_GET['book_id']);

// Fetch Book Details
$sql = "SELECT * FROM books WHERE book_id = ? AND status = 'Available'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<h3>Error: This book is unavailable.</h3><a href='search.php'>Go Back</a>");
}

$book = $result->fetch_assoc();
$is_donation = ($book['price'] == 0);
$price_bdt = $book['price'] * 120; // Dummy conversion

include 'includes/header.php';
?>

<div class="container" style="padding-top: 3rem; padding-bottom: 5rem; max-width: 600px;">
    
    <h1 style="font-family: 'Poppins', sans-serif; text-align: center; margin-bottom: 2rem;">
        <?= $is_donation ? "Confirm Donation Claim" : "Secure Checkout"; ?>
    </h1>

    <div class="book-card" style="padding: 2rem; margin-bottom: 2rem; border-top: 4px solid <?= $is_donation ? '#10b981' : 'var(--primary)'; ?>;">
        <h3 style="margin-bottom: 1.5rem; font-family: 'Poppins', sans-serif;">Summary</h3>
        
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.8rem; font-size: 1.1rem;">
            <span style="color: var(--gray-text);">Book Title</span>
            <span style="font-weight: 600; text-align: right;"><?= htmlspecialchars($book['title']); ?></span>
        </div>

        <hr style="margin: 1.5rem 0; border: 0; border-top: 1px dashed #cbd5e1;">

        <div style="display: flex; justify-content: space-between; font-size: 1.4rem; font-weight: 700;">
            <span>Total to Pay</span>
            <?php if($is_donation): ?>
                <span style="color: #10b981;">FREE (Donation)</span>
            <?php else: ?>
                <span style="color: var(--primary);">৳ <?= number_format($price_bdt, 2); ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="book-card" style="padding: 2rem;">
        
        <form action="actions/process_payment.php" method="POST">
            <input type="hidden" name="book_id" value="<?= $book['book_id']; ?>">
            <input type="hidden" name="seller_id" value="<?= $book['seller_id']; ?>">
            <input type="hidden" name="amount" value="<?= $book['price']; ?>">

            <?php if($is_donation): ?>
                <div style="background: #ecfdf5; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #a7f3d0; color: #065f46;">
                    <p><strong>Generosity Alert!</strong> This book is being donated for free.</p>
                    <p style="font-size: 0.9rem; margin-top: 5px;">Simply confirm below to claim it. No payment required.</p>
                </div>
                <input type="hidden" name="trx_id" value="DONATION-FREE">
                
                <button type="submit" class="btn" style="width: 100%; padding: 1rem; font-size: 1.1rem; border-radius: 8px; font-weight: 600; background-color: #10b981; color: white; border: none; cursor: pointer;">
                    Claim Book Now
                </button>

            <?php else: ?>
                <h3 style="margin-bottom: 1.5rem;">Select Payment Method</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                    <label style="cursor: pointer;">
                        <input type="radio" name="payment_method" value="bkash" required style="display: none;" id="bkash_opt">
                        <div class="payment-box" style="border: 2px solid #e2e8f0; padding: 1rem; border-radius: 12px; text-align: center;">
                            <strong style="color: #e2136e;">bKash</strong>
                        </div>
                    </label>
                    <label style="cursor: pointer;">
                        <input type="radio" name="payment_method" value="nagad" style="display: none;" id="nagad_opt">
                        <div class="payment-box" style="border: 2px solid #e2e8f0; padding: 1rem; border-radius: 12px; text-align: center;">
                            <strong style="color: #f6921e;">Nagad</strong>
                        </div>
                    </label>
                </div>

                <style>
                    input[type="radio"]:checked + .payment-box {
                        border-color: var(--primary) !important;
                        background-color: #e0e7ff;
                    }
                </style>

                <div style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Transaction ID</label>
                    <input type="text" name="trx_id" required placeholder="e.g. 8JHS723B" style="width: 100%; padding: 1rem; border: 2px solid #cbd5e1; border-radius: 8px;">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem; border-radius: 8px;">
                    Confirm Payment
                </button>
            <?php endif; ?>
        </form>
    </div>

</div>
<?php include 'includes/footer.php'; ?>