<?php
// actions/register_action.php
session_start();
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Sanitize Inputs (Trimming whitespace is crucial!)
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $email = trim($_POST['email']); // Removes spaces from start/end
    $password = $_POST['password'];
    $role = $_POST['role'];

    // 2. Validation
    if (empty($full_name) || empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../register.php");
        exit();
    }

    // 3. Check if Email Exists
    $check_sql = "SELECT user_id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "This email is already registered. Please login.";
        header("Location: ../register.php");
        exit();
    }
    $stmt->close();

    // 4. Create New User
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $insert_sql = "INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    
    if ($stmt) {
        $stmt->bind_param("ssss", $full_name, $email, $password_hash, $role);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Account created! Please login.";
            header("Location: ../login.php");
        } else {
            $_SESSION['error'] = "Database error: " . $stmt->error;
            header("Location: ../register.php");
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Failed to prepare statement.";
        header("Location: ../register.php");
    }

} else {
    header("Location: ../register.php");
}
?>