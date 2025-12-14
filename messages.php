<?php
// messages.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];
include 'includes/header.php';

// SQL Logic: Find all unique users I have sent messages to OR received messages from.
// This is a bit complex in SQL, so we use a UNION to get all IDs, then DISTINCT.
$sql = "
    SELECT DISTINCT u.user_id, u.full_name, u.role
    FROM users u
    JOIN messages m ON (u.user_id = m.sender_id OR u.user_id = m.receiver_id)
    WHERE (m.sender_id = ? OR m.receiver_id = ?) AND u.user_id != ?
    ORDER BY m.sent_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $current_user_id, $current_user_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container" style="padding-top: 3rem; padding-bottom: 5rem; max-width: 800px;">
    
    <h1 style="font-family: 'Poppins', sans-serif; margin-bottom: 2rem;">My Messages</h1>

    <div class="book-card" style="padding: 0;">
        <?php if ($result->num_rows > 0): ?>
            <div style="display: flex; flex-direction: column;">
                <?php while($user = $result->fetch_assoc()): ?>
                    
                    <a href="chat.php?user_id=<?= $user['user_id']; ?>" 
                       style="text-decoration: none; color: inherit; padding: 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; transition: background 0.2s;">
                        
                        <div style="width: 50px; height: 50px; background: #e0e7ff; color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem; margin-right: 1rem;">
                            <?= strtoupper(substr($user['full_name'], 0, 1)); ?>
                        </div>

                        <div>
                            <div style="font-weight: 600; font-size: 1.1rem;">
                                <?= htmlspecialchars($user['full_name']); ?>
                            </div>
                            <div style="font-size: 0.85rem; color: var(--gray-text);">
                                <?= ucfirst($user['role']); ?> • Click to view conversation
                            </div>
                        </div>

                        <div style="margin-left: auto; color: var(--primary); font-size: 1.5rem;">
                            ›
                        </div>
                    </a>

                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="padding: 3rem; text-align: center; color: var(--gray-text);">
                <p>No messages yet.</p>
                <p style="font-size: 0.9rem;">Start a conversation by visiting a book page!</p>
                <a href="search.php" class="btn btn-primary-sm" style="margin-top: 1rem; display: inline-block;">Browse Books</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>