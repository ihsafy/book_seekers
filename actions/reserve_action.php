<?php
// actions/reserve_action.php
session_start();
require_once '../config/db.php';

// 1. Auth Check: Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = intval($_POST['book_id']);
    $user_id = $_SESSION['user_id'];

    // 2. Check if Book is still Available
    // We lock the row (FOR UPDATE) to prevent race conditions if two people click at the exact same time
    $check_sql = "SELECT status FROM books WHERE book_id = ? LIMIT 1 FOR UPDATE";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    if ($book && $book['status'] === 'Available') {
        
        // 3. Reserve the Book
        // We update the status AND set a reservation timestamp (optional, but good for features like expiring reservations)
        $update_sql = "UPDATE books SET status = 'Reserved', reservation_end = DATE_ADD(NOW(), INTERVAL 24 HOUR) WHERE book_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $book_id);
        
        if ($update_stmt->execute()) {
            
            // 4. Create a Reservation Record (Optional but recommended for history)
            // For now, updating the book status is enough for the core requirement.
            
            $_SESSION['success'] = "Book Reserved successfully! You have 24 hours to contact the seller.";
        } else {
            $_SESSION['error'] = "Database error could not reserve.";
        }
        
    } else {
        $_SESSION['error'] = "Sorry, this book is no longer available.";
    }

    $stmt->close();
    
    // Redirect back to the book page
    header("Location: ../book_details.php?id=" . $book_id);
    exit();

} else {
    header("Location: ../index.php");
}
?>