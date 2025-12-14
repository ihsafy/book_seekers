<?php
// chat.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$my_id = $_SESSION['user_id'];
$other_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($other_user_id === 0 || $other_user_id === $my_id) {
    header("Location: messages.php");
    exit();
}

// Get Other User's Name
$user_sql = "SELECT full_name FROM users WHERE user_id = ?";
$u_stmt = $conn->prepare($user_sql);
$u_stmt->bind_param("i", $other_user_id);
$u_stmt->execute();
$other_user = $u_stmt->get_result()->fetch_assoc();

if (!$other_user) {
    die("User not found.");
}

// Fetch Conversation History
$sql = "SELECT * FROM messages 
        WHERE (sender_id = ? AND receiver_id = ?) 
           OR (sender_id = ? AND receiver_id = ?) 
        ORDER BY sent_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $my_id, $other_user_id, $other_user_id, $my_id);
$stmt->execute();
$messages = $stmt->get_result();

include 'includes/header.php';
?>

<div class="container" style="padding-top: 2rem; padding-bottom: 5rem; max-width: 800px;">
    
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center;">
        <a href="messages.php" style="text-decoration: none; color: var(--gray-text); margin-right: 1rem; font-size: 1.2rem;">← Back</a>
        <h2 style="font-family: 'Poppins', sans-serif; margin: 0;">
            Chat with <?= htmlspecialchars($other_user['full_name']); ?>
        </h2>
    </div>

    <div class="book-card" style="height: 500px; display: flex; flex-direction: column;">
        
        <div style="flex: 1; padding: 1.5rem; overflow-y: auto; background: #f8fafc; display: flex; flex-direction: column; gap: 1rem;" id="chat-box">
            
            <?php if ($messages->num_rows > 0): ?>
                <?php while($msg = $messages->fetch_assoc()): ?>
                    
                    <?php 
                        $is_me = ($msg['sender_id'] == $my_id); 
                        $align = $is_me ? 'align-self: flex-end;' : 'align-self: flex-start;';
                        $bg = $is_me ? 'background: var(--primary); color: white;' : 'background: white; border: 1px solid #e2e8f0; color: var(--dark);';
                    ?>

                    <div style="max-width: 70%; padding: 0.8rem 1.2rem; border-radius: 12px; font-size: 0.95rem; line-height: 1.5; <?= $align ?> <?= $bg ?>">
                        <?= nl2br(htmlspecialchars($msg['message_text'])); ?>
                        <div style="font-size: 0.7rem; opacity: 0.7; margin-top: 5px; text-align: right;">
                            <?= date("h:i A", strtotime($msg['sent_at'])); ?>
                        </div>
                    </div>

                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; color: var(--gray-text); margin-top: 2rem;">
                    <p>No messages yet. Say hello!</p>
                </div>
            <?php endif; ?>

        </div>

        <div style="padding: 1rem; border-top: 1px solid #e2e8f0; background: white;">
            <form action="actions/send_message_action.php" method="POST" style="display: flex; gap: 10px;">
                <input type="hidden" name="receiver_id" value="<?= $other_user_id; ?>">
                
                <input type="text" name="message" required placeholder="Type a message..." autocomplete="off"
                       style="flex: 1; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 50px; outline: none; padding-left: 1.5rem;">
                
                <button type="submit" class="btn btn-primary" style="border-radius: 50px; padding: 0 1.5rem;">Send</button>
            </form>
        </div>

    </div>

    <script>
        var chatBox = document.getElementById("chat-box");
        chatBox.scrollTop = chatBox.scrollHeight;
    </script>

</div>

<?php include 'includes/footer.php'; ?>