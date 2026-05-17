# BoiBazaar - Peer-to-Peer Book Marketplace

## About The Project
BoiBazaar (Book Marketplace) is a PHP-based web application designed to connect book lovers. It provides a platform where users can easily buy, sell, or donate used and new books without any hidden charges. 

## Key Features
* **Role-Based Access:** Distinct dashboards and actions for Buyers and Sellers.
* **Book Listings:** Sellers can easily upload book details including title, author, genre, price, and cover images.
* **Fresh Arrivals:** Automated homepage section showcasing the latest available books.
* **Secure Output:** Implementation of htmlspecialchars() to prevent XSS vulnerabilities.
* **Empty State Handling:** Friendly user interface prompts when no books are currently available.

## Built With
* **Backend:** PHP (Vanilla)
* **Database:** MySQL
* **Frontend:** HTML5, CSS3 (Custom UI with CSS variables)

## Prerequisites
To run this project locally, you will need a local server environment such as:
* XAMPP (Windows/Linux/Mac)
* MAMP (Mac)
* PHP 7.4 or higher
* MySQL database

## Installation & Setup

1. **Clone the repository**
   Download or clone the project folder into your local server's root directory (e.g., htdocs for XAMPP or www for WAMP).

2. **Database Setup**
   * Open phpMyAdmin (usually http://localhost/phpmyadmin).
   * Create a new database named boibazaar_db (or your preferred name).
   * Import the provided .sql file (e.g., database.sql) to generate the required tables (users, books, etc.).

3. **Configure Database Connection**
   * Navigate to config/db.php.
   * Update the database credentials to match your local setup:
     $host = "localhost";
     $user = "root";
     $password = ""; // Default XAMPP password is blank
     $dbname = "boibazaar_db";

4. **Directory Permissions**
   Ensure that the uploads/books/ directory has write permissions so that users can successfully upload book cover images.

5. **Run the Application**
   Open your web browser and navigate to: http://localhost/your-project-folder-name/

## Folder Structure
* /config - Database configuration files (db.php).
* /includes - Reusable layout components (header.php, footer.php).
* /uploads/books - Directory for storing uploaded book cover images.
* index.php - The main homepage (Fresh Arrivals).
* add_book.php - Form for sellers to list new books.
* search.php - Page to browse and filter the book collection.
* register.php - User registration and role selection.

## Contributing
Contributions, issues, and feature requests are welcome! Feel free to fork the repository and submit pull requests to improve the platform.
