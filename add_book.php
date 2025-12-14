<?php
// add_book.php
session_start();

// 1. Security: Only Sellers can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    // If not a seller, kick them back to the dashboard
    header("Location: dashboard.php");
    exit();
}

include 'includes/header.php';
?>

<div class="container" style="max-width: 700px; margin-top: 3rem; margin-bottom: 5rem;">
    
    <!-- Animated Entry -->
    <div style="animation: fadeInUp 0.6s ease-out;">
        
        <div class="book-card" style="padding: 2.5rem;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <h2 style="font-family: 'Poppins', sans-serif;">List a Book</h2>
                <p style="color: var(--gray-text);">Sell or Donate your books on PuthiPustak.</p>
            </div>

            <!-- Error Message -->
            <?php if(isset($_SESSION['error'])): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center;">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="actions/add_book_action.php" method="POST" enctype="multipart/form-data">
                
                <!-- Title & Author -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Book Title</label>
                        <input type="text" name="title" required placeholder="e.g. The Great Gatsby"
                               style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Author</label>
                        <input type="text" name="author" required placeholder="e.g. F. Scott Fitzgerald"
                               style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit;">
                    </div>
                </div>

                <!-- Genre & Condition -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Genre</label>
                        <select name="genre" required style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; background: white;">
                            <option value="">Select Genre</option>
                            <option value="Fiction">Fiction</option>
                            <option value="Non-Fiction">Non-Fiction</option>
                            <option value="Educational">Educational / Academic</option>
                            <option value="Sci-Fi">Sci-Fi & Fantasy</option>
                            <option value="Biography">Biography</option>
                            <option value="Children">Children</option>
                            <option value="Religious">Religious</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Condition</label>
                        <select name="condition" required style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; background: white;">
                            <option value="">Select Condition</option>
                            <!-- UPDATED CONDITIONS as requested -->
                            <option value="Like New">Like New</option>
                            <option value="Good">Good</option>
                            <option value="Acceptable">Acceptable</option>
                        </select>
                    </div>
                </div>

                <!-- Price, Location, & Image -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Price ($) - Enter 0 to Donate</label>
                        <input type="number" name="price" step="0.01" required placeholder="0.00"
                               style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Location</label>
                        <input type="text" name="location" required placeholder="e.g. Dhanmondi, Dhaka"
                               style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit;">
                    </div>
                </div>

                <!-- Image Upload -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Book Cover Image</label>
                    <input type="file" name="book_image" accept="image/*" required
                           style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc;">
                    <small style="color: var(--gray-text); display: block; margin-top: 5px;">Supported: JPG, PNG, JPEG</small>
                </div>

                <!-- Description -->
                <div style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Description</label>
                    <textarea name="description" rows="4" placeholder="Briefly describe the book content and specific condition..."
                              style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit;"></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 8px; font-size: 1rem; padding: 1rem;">
                    Post Listing Now
                </button>
            
            </form>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>