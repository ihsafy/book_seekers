<?php
// actions/wishlist_action.php
session_start();
require_once '../config/db.php';

// Auth Check
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login
    $_SESSION['error'] = "Please login to use the wishlist.";
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = intval($_POST['book_id']);
    $user_id = $_SESSION['user_id'];

    // 1. Check if already in Wishlist
    $check_sql = "SELECT wishlist_id FROM wishlist WHERE user_id = ? AND book_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // --- CASE A: REMOVE from Wishlist ---
        $delete_sql = "DELETE FROM wishlist WHERE user_id = ? AND book_id = ?";
        $del_stmt = $conn->prepare($delete_sql);
        $del_stmt->bind_param("ii", $user_id, $book_id);
        $del_stmt->execute();
        // No success message needed for removal, usually just a UI update is enough
    } else {
        // --- CASE B: ADD to Wishlist ---
        $insert_sql = "INSERT INTO wishlist (user_id, book_id) VALUES (?, ?)";
        $ins_stmt = $conn->prepare($insert_sql);
        $ins_stmt->bind_param("ii", $user_id, $book_id);
        $ins_stmt->execute();
        $_SESSION['success'] = "Added to your Wishlist!";
    }

    $stmt->close();

    // Redirect back to the same book page
    header("Location: ../book_details.php?id=" . $book_id);
    exit();
}
?>