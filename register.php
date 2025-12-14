<?php
// register.php
session_start();
// If user is already logged in, redirect them to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
include 'includes/header.php';
?>

<div class="container" style="max-width: 500px; margin-top: 4rem; margin-bottom: 4rem;">
    
    <div style="animation: fadeInUp 0.6s ease-out;">
        
        <div class="book-card" style="padding: 2.5rem;">
            <h2 style="text-align: center; margin-bottom: 1.5rem; font-family: 'Poppins', sans-serif;">Join the Community</h2>
            <p style="text-align: center; color: var(--gray-text); margin-bottom: 2rem;">Create an account to buy, sell, and donate books.</p>

            <?php if(isset($_SESSION['error'])): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center;">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="actions/register_action.php" method="POST">
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Full Name</label>
                    <input type="text" name="full_name" required 
                           style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email Address</label>
                    <input type="email" name="email" required 
                           style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Password</label>
                    <input type="password" name="password" required minlength="6"
                           style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit;">
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">I want to be a:</label>
                    <select name="role" style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; background: white;">
                        <option value="seeker">Book Seeker (Buyer)</option>
                        <option value="seller">Book Seller</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 8px;">Create Account</button>
            
            </form>
            
            <p style="text-align: center; margin-top: 1.5rem; color: var(--gray-text);">
                Already have an account? <a href="login.php" style="color: var(--primary); font-weight: 600;">Log in</a>
            </p>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>