<?php
// actions/add_book_action.php
session_start();
require_once '../config/db.php';

// Check if user is seller
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header("Location: ../dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Sanitize Inputs (Added Location here)
    $title = htmlspecialchars(trim($_POST['title']));
    $author = htmlspecialchars(trim($_POST['author']));
    $genre = $_POST['genre'];
    $condition = $_POST['condition'];
    $price = floatval($_POST['price']);
    $location = htmlspecialchars(trim($_POST['location'])); // <--- Captured!
    $description = htmlspecialchars(trim($_POST['description']));
    $seller_id = $_SESSION['user_id'];

    // 2. Handle File Upload
    $target_dir = "../uploads/books/";
    $image_name = basename($_FILES["book_image"]["name"]);
    
    // Create a unique name to prevent overwriting (e.g., book_169342_myimage.jpg)
    $unique_name = "book_" . time() . "_" . $image_name;
    $target_file = $target_dir . $unique_name;
    $uploadOk = 1;

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["book_image"]["tmp_name"]);
    if($check === false) {
        $_SESSION['error'] = "File is not an image.";
        header("Location: ../add_book.php");
        exit();
    }

    // Try to move the uploaded file
    if (move_uploaded_file($_FILES["book_image"]["tmp_name"], $target_file)) {
        
        // 3. Insert into Database (Updated SQL to include location)
        $sql = "INSERT INTO books (seller_id, title, author, genre, price, location, book_condition, description, image_url, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Available')";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            // Note: Types string is "isssdssss" (i=int, s=string, d=decimal)
            $stmt->bind_param("isssdssss", $seller_id, $title, $author, $genre, $price, $location, $condition, $description, $unique_name);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Book posted successfully!";
                header("Location: ../dashboard.php");
            } else {
                $_SESSION['error'] = "Database error: " . $stmt->error;
                header("Location: ../add_book.php");
            }
            $stmt->close();
        } else {
             $_SESSION['error'] = "Failed to prepare statement.";
             header("Location: ../add_book.php");
        }
    } else {
        $_SESSION['error'] = "Sorry, there was an error uploading your file.";
        header("Location: ../add_book.php");
    }

} else {
    header("Location: ../add_book.php");
}
?>