<?php
// payment_success.php
session_start();
include 'includes/header.php';
?>

<div class="container" style="padding: 5rem 1rem; text-align: center;">
    
    <div style="animation: fadeInUp 0.6s ease-out;">
        <div style="font-size: 5rem; margin-bottom: 1rem;">🎉</div>
        <h1 style="font-family: 'Poppins', sans-serif; color: #10b981; margin-bottom: 1rem;">Payment Successful!</h1>
        <p style="font-size: 1.2rem; color: var(--gray-text); margin-bottom: 2rem;">
            Your order has been placed. The seller has been notified.
        </p>
        
        <div style="background: white; padding: 2rem; border-radius: 12px; display: inline-block; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 2rem; border: 1px solid #e2e8f0;">
            <p style="margin-bottom: 0.5rem;">Transaction ID: <strong>#<?= htmlspecialchars($_GET['tid'] ?? '0000'); ?></strong></p>
            <p style="color: var(--primary);">Status: <strong>Paid via Mobile Banking</strong></p>
        </div>
        
        <br>

        <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
        <a href="search.php" class="btn btn-outline" style="margin-left: 10px;">Buy More Books</a>
    </div>

</div>

<?php include 'includes/footer.php'; ?>