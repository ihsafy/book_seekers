<?php
// admin/delete_user.php
session_start();
require_once '../config/db.php';

// Security: Only Admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    // Prevent Admin from deleting themselves
    if ($user_id == $_SESSION['user_id']) {
        header("Location: index.php?error=You cannot delete yourself");
        exit();
    }

    // Delete the user (Cascade will handle books/messages if set up, otherwise we do it manually)
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        header("Location: index.php?msg=User deleted successfully");
    } else {
        header("Location: index.php?error=Failed to delete user");
    }
}
?>