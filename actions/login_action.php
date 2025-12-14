<?php
// actions/login_action.php
session_start();
require_once '../config/db.php';

// Turn on error reporting (Helpful for debugging login issues)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Sanitize Inputs
    $email = trim($_POST['email']); // Remove extra spaces
    $password = $_POST['password'];

    // 2. Validation
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Please fill in all fields.";
        header("Location: ../login.php");
        exit();
    }

    // 3. Prepare SQL Statement
    // We select the ID, Name, Hash, and Role based on the email provided
    $sql = "SELECT user_id, full_name, password_hash, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        // If SQL fails, show error
        die("Database Prepare Error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // 4. Check if user exists
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $full_name, $hashed_password, $role);
        $stmt->fetch();

        // 5. Verify Password
        if (password_verify($password, $hashed_password)) {
            
            // SUCCESS! Store user info in Session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['full_name'] = $full_name;
            $_SESSION['role'] = $role; // Important for redirection later

            // Redirect based on role
            if ($role === 'admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../dashboard.php");
            }
            exit();

        } else {
            // Password incorrect
            $_SESSION['error'] = "Invalid password. Please try again.";
            header("Location: ../login.php");
            exit();
        }
    } else {
        // User not found
        $_SESSION['error'] = "No account found with this email.";
        header("Location: ../login.php");
        exit();
    }
    
    $stmt->close();
} else {
    // If user tries to access this file directly
    header("Location: ../login.php");
    exit();
}
?>