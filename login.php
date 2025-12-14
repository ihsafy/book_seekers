<?php
// login.php
session_start();

// If user is already logged in, send them to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

include 'includes/header.php';
?>

<div class="container" style="max-width: 450px; margin-top: 5rem; margin-bottom: 5rem;">
    
    <div style="animation: fadeInUp 0.6s ease-out;">
        
        <div class="book-card" style="padding: 2.5rem;">
            <h2 style="text-align: center; margin-bottom: 1rem; font-family: 'Poppins', sans-serif;">Welcome Back</h2>
            <p style="text-align: center; color: var(--gray-text); margin-bottom: 2rem;">Login to continue your journey.</p>

            <?php if(isset($_SESSION['success'])): ?>
                <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center;">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center;">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="actions/login_action.php" method="POST">
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email Address</label>
                    <input type="email" name="email" required 
                           style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit;">
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Password</label>
                    <input type="password" name="password" required 
                           style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit;">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 8px;">Login</button>
            
            </form>
            
            <p style="text-align: center; margin-top: 1.5rem; color: var(--gray-text);">
                Don't have an account? <a href="register.php" style="color: var(--primary); font-weight: 600;">Sign up</a>
            </p>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>