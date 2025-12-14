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

    // --- STEP 1: CLEAN UP DEPENDENCIES ---

    // A. Delete Transactions (Where they were Buyer OR Seller)
    $conn->query("DELETE FROM transactions WHERE buyer_id = $user_id OR seller_id = $user_id");

    // B. Delete Wishlist items
    $conn->query("DELETE FROM wishlist WHERE user_id = $user_id");

    // C. Delete Messages (Sent or Received)
    $conn->query("DELETE FROM messages WHERE sender_id = $user_id OR receiver_id = $user_id");

    // D. Delete Notifications
    $conn->query("DELETE FROM notifications WHERE user_id = $user_id");

    // E. Delete Their Books (And the images)
    $book_sql = "SELECT book_id, image_url FROM books WHERE seller_id = $user_id";
    $result = $conn->query($book_sql);
    
    while ($book = $result->fetch_assoc()) {
        // Delete the image file
        if (!empty($book['image_url'])) {
            $path = "../uploads/books/" . $book['image_url'];
            if (file_exists($path)) {
                unlink($path);
            }
        }
        // Delete related wishlist entries for this book (from other users)
        $conn->query("DELETE FROM wishlist WHERE book_id = " . $book['book_id']);
        
        // Delete related transactions for this book (extra safety)
        $conn->query("DELETE FROM transactions WHERE book_id = " . $book['book_id']);
    }

    // Now delete all their books from DB
    $conn->query("DELETE FROM books WHERE seller_id = $user_id");

    // --- STEP 2: DELETE THE USER ---
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        header("Location: index.php?msg=User and all their data deleted successfully");
    } else {
        header("Location: index.php?error=Failed to delete user: " . $conn->error);
    }
} else {
    header("Location: index.php");
}
?>