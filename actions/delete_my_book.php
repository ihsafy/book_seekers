<?php
// actions/delete_my_book.php
session_start();
require_once '../config/db.php';

// 1. Security Check: User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $book_id = intval($_GET['id']);

    // 2. VERIFY OWNERSHIP & GET IMAGE
    // We try to find the book, but ONLY if seller_id matches the current user
    $check_sql = "SELECT image_url, status FROM books WHERE book_id = ? AND seller_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $book_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    if ($book) {
        // Ownership verified!
        
        // Optional: Block deleting if already sold (Uncomment below if you want this rule)
        /*
        if ($book['status'] === 'Sold') {
             $_SESSION['error'] = "You cannot delete a book that has already been sold.";
             header("Location: ../dashboard.php");
             exit();
        }
        */

        // --- STEP A: CLEAN UP DEPENDENCIES (The Fix) ---
        // We must delete records in other tables that point to this book_id
        
        // 1. Delete from Transactions
        $conn->query("DELETE FROM transactions WHERE book_id = $book_id");

        // 2. Delete from Wishlists
        $conn->query("DELETE FROM wishlist WHERE book_id = $book_id");

        // 3. Delete from Messages
        $conn->query("DELETE FROM messages WHERE book_id = $book_id");

        // --- STEP B: DELETE IMAGE FILE ---
        if (!empty($book['image_url'])) {
            $file_path = "../uploads/books/" . $book['image_url'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        // --- STEP C: DELETE BOOK FROM DATABASE ---
        $del_sql = "DELETE FROM books WHERE book_id = ?";
        $del_stmt = $conn->prepare($del_sql);
        $del_stmt->bind_param("i", $book_id);
        
        if ($del_stmt->execute()) {
            $_SESSION['success'] = "Your listing was removed successfully.";
        } else {
            $_SESSION['error'] = "Database error: Could not delete book.";
        }

    } else {
        $_SESSION['error'] = "You do not have permission to delete this book.";
    }
}

// Redirect back to dashboard
header("Location: ../dashboard.php");
exit();
?>