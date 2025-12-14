<?php
// admin/delete_book.php
session_start();
require_once '../config/db.php';

// Security: Only Admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $book_id = intval($_GET['id']);

    // --- STEP 1: CLEAN UP DEPENDENCIES ---

    // A. Delete Transactions linked to this book
    $conn->query("DELETE FROM transactions WHERE book_id = $book_id");

    // B. Delete Wishlist entries for this book
    $conn->query("DELETE FROM wishlist WHERE book_id = $book_id");
    
    // C. Delete Messages linked to this book (if any)
    $conn->query("DELETE FROM messages WHERE book_id = $book_id");

    // --- STEP 2: DELETE IMAGE FILE ---
    $query = "SELECT image_url FROM books WHERE book_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $book = $res->fetch_assoc();

    if ($book && !empty($book['image_url'])) {
        $file_path = "../uploads/books/" . $book['image_url'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // --- STEP 3: DELETE THE BOOK ---
    $sql = "DELETE FROM books WHERE book_id = ?";
    $del_stmt = $conn->prepare($sql);
    $del_stmt->bind_param("i", $book_id);
    
    if ($del_stmt->execute()) {
        header("Location: index.php?msg=Book deleted successfully");
    } else {
        header("Location: index.php?error=Failed to delete book: " . $conn->error);
    }
} else {
    header("Location: index.php");
}
?>