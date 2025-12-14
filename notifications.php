<?php
// notifications.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
include 'includes/header.php';

$sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container" style="padding-top: 3rem; padding-bottom: 5rem; max-width: 700px;">
    <h1 style="font-family: 'Poppins', sans-serif; margin-bottom: 2rem;">My Notifications</h1>
    
    <div class="book-card" style="padding: 0;">
        <?php if ($result->num_rows > 0): ?>
            <div style="display: flex; flex-direction: column;">
                <?php while($notif = $result->fetch_assoc()): ?>
                    <div style="padding: 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: start;">
                        <div style="margin-right: 15px; font-size: 1.5rem;">🔔</div>
                        <div>
                            <p style="font-size: 1rem; color: var(--dark); margin-bottom: 0.3rem;">
                                <?= htmlspecialchars($notif['message']); ?>
                            </p>
                            <small style="color: var(--gray-text);">
                                <?= date("M d, h:i A", strtotime($notif['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="padding: 3rem; text-align: center; color: var(--gray-text);">
                No notifications yet.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>