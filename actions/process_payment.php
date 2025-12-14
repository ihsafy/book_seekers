<?php
// actions/process_payment.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../login.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];
$book_id = intval($_POST['book_id']);
$seller_id = intval($_POST['seller_id']);
$total_amount = floatval($_POST['amount']);
$trx_id = htmlspecialchars($_POST['trx_id']);

// --- 1. CALCULATE FEES (Handle Donations) ---
if ($total_amount == 0) {
    // It's a Donation
    $admin_fee = 0.00;
    $seller_earning = 0.00;
    $is_donation = true;
} else {
    // Regular Sale
    $admin_percentage = 0.20;
    $seller_percentage = 0.80;
    $admin_fee = $total_amount * $admin_percentage;
    $seller_earning = $total_amount * $seller_percentage;
    $is_donation = false;
}

// --- 2. VERIFY AVAILABILITY ---
$check_sql = "SELECT title, status FROM books WHERE book_id = ? AND status = 'Available'";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    $_SESSION['error'] = "Sorry! This book is no longer available.";
    header("Location: ../book_details.php?id=$book_id");
    exit();
}
$book_info = $res->fetch_assoc();
$book_title = $book_info['title'];

// --- 3. PROCESS TRANSACTION ---
$conn->begin_transaction();

try {
    // A. Record Transaction
    $trans_sql = "INSERT INTO transactions (buyer_id, seller_id, book_id, amount, seller_earning, admin_fee) VALUES (?, ?, ?, ?, ?, ?)";
    $t_stmt = $conn->prepare($trans_sql);
    $t_stmt->bind_param("iiiddd", $buyer_id, $seller_id, $book_id, $total_amount, $seller_earning, $admin_fee);
    $t_stmt->execute();

    // B. Mark Book as SOLD
    $update_sql = "UPDATE books SET status = 'Sold' WHERE book_id = ?";
    $u_stmt = $conn->prepare($update_sql);
    $u_stmt->bind_param("i", $book_id);
    $u_stmt->execute();

    // C. SEND NOTIFICATIONS (Logic Fixed)

    // 1. Notify Seller
    if ($is_donation) {
        $msg_seller = "Your book '$book_title' has been claimed as a donation! Thank you for your generosity.";
    } else {
        $msg_seller = "Good news! Your book '$book_title' was sold. You earned $" . number_format($seller_earning, 2);
    }
    
    $notif_sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
    $n_stmt1 = $conn->prepare($notif_sql);
    $n_stmt1->bind_param("is", $seller_id, $msg_seller);
    $n_stmt1->execute();

    // 2. Notify Admin
    $admin_sql = "SELECT user_id FROM users WHERE role = 'admin' LIMIT 1";
    $admin_res = $conn->query($admin_sql);
    if($admin_row = $admin_res->fetch_assoc()){
        $admin_id = $admin_row['user_id'];
        
        if ($is_donation) {
            $msg_admin = "Donation Alert: '$book_title' was claimed for free. No platform fees.";
        } else {
            $msg_admin = "New Sale: '$book_title' sold for $" . number_format($total_amount, 2) . ". Fee: $" . number_format($admin_fee, 2);
        }

        $n_stmt2 = $conn->prepare($notif_sql);
        $n_stmt2->bind_param("is", $admin_id, $msg_admin);
        $n_stmt2->execute();
    }

    // D. Commit
    $conn->commit();

    header("Location: ../payment_success.php?tid=" . $trx_id);
    exit();

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Transaction failed. Please try again.";
    header("Location: ../checkout.php?book_id=$book_id");
    exit();
}
?>