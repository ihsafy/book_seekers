<?php
// search.php
session_start();
require_once 'config/db.php';
include 'includes/header.php';

// 1. Initialize Variables
$search = $_GET['q'] ?? '';
$genre_filter = $_GET['genre'] ?? '';
$location_filter = $_GET['location'] ?? '';
$sort = $_GET['sort'] ?? 'newest';

// 2. Build the SQL Query dynamically
$sql = "SELECT * FROM books WHERE status = 'Available'";
$params = [];
$types = "";

// Add Search Keyword Condition
if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR author LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

// Add Genre Condition
if (!empty($genre_filter)) {
    $sql .= " AND genre = ?";
    $params[] = $genre_filter;
    $types .= "s";
}

// Add Location Condition
if (!empty($location_filter)) {
    $sql .= " AND location LIKE ?";
    $location_param = "%$location_filter%";
    $params[] = $location_param;
    $types .= "s";
}

// Add Sorting
if ($sort === 'price_asc') {
    $sql .= " ORDER BY price ASC";
} elseif ($sort === 'price_desc') {
    $sql .= " ORDER BY price DESC";
} else {
    $sql .= " ORDER BY created_at DESC"; // Default: Newest first
}

// 3. Execute the Query safely
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    
    <h1 style="font-family: 'Poppins', sans-serif; margin-bottom: 2rem; text-align: center;">Find Your Next Read</h1>

    <div class="book-card" style="padding: 1.5rem; margin-bottom: 3rem; animation: fadeInUp 0.5s ease-out;">
        <form action="search.php" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            
            <div>
                <label style="font-size: 0.9rem; font-weight: 600; margin-bottom: 0.3rem; display: block;">Search</label>
                <input type="text" name="q" value="<?= htmlspecialchars($search); ?>" placeholder="Title or Author..."
                       style="width: 100%; padding: 0.7rem; border: 1px solid #e2e8f0; border-radius: 8px;">
            </div>

            <div>
                <label style="font-size: 0.9rem; font-weight: 600; margin-bottom: 0.3rem; display: block;">Genre</label>
                <select name="genre" style="width: 100%; padding: 0.7rem; border: 1px solid #e2e8f0; border-radius: 8px; background: white;">
                    <option value="">All Genres</option>
                    <option value="Fiction" <?= $genre_filter == 'Fiction' ? 'selected' : ''; ?>>Fiction</option>
                    <option value="Non-Fiction" <?= $genre_filter == 'Non-Fiction' ? 'selected' : ''; ?>>Non-Fiction</option>
                    <option value="Educational" <?= $genre_filter == 'Educational' ? 'selected' : ''; ?>>Educational</option>
                    <option value="Sci-Fi" <?= $genre_filter == 'Sci-Fi' ? 'selected' : ''; ?>>Sci-Fi</option>
                    <option value="Biography" <?= $genre_filter == 'Biography' ? 'selected' : ''; ?>>Biography</option>
                    <option value="Children" <?= $genre_filter == 'Children' ? 'selected' : ''; ?>>Children</option>
                </select>
            </div>

            <div>
                <label style="font-size: 0.9rem; font-weight: 600; margin-bottom: 0.3rem; display: block;">Location</label>
                <input type="text" name="location" value="<?= htmlspecialchars($location_filter); ?>" placeholder="City or Area..."
                       style="width: 100%; padding: 0.7rem; border: 1px solid #e2e8f0; border-radius: 8px;">
            </div>

            <div>
                <label style="font-size: 0.9rem; font-weight: 600; margin-bottom: 0.3rem; display: block;">Sort By</label>
                <select name="sort" style="width: 100%; padding: 0.7rem; border: 1px solid #e2e8f0; border-radius: 8px; background: white;">
                    <option value="newest" <?= $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="height: 46px; border-radius: 8px;">Search Books</button>
        
        </form>
    </div>

    <div class="grid-books">
        <?php if ($result->num_rows > 0): ?>
            <?php while($book = $result->fetch_assoc()): ?>
                
                <div class="book-card">
                    <div class="card-img-placeholder" style="height: 220px;">
                        <?php if($book['image_url']): ?>
                            <img src="uploads/books/<?= htmlspecialchars($book['image_url']); ?>" alt="Cover" style="width:100%; height:100%; object-fit: cover;">
                        <?php else: ?>
                            <span>No Image</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="background: #e0e7ff; color: var(--primary); font-size: 0.7rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; text-transform: uppercase;">
                                <?= htmlspecialchars($book['genre']); ?>
                            </span>
                            <span style="font-size: 0.7rem; color: var(--gray-text);">
                                📍 <?= htmlspecialchars($book['location']); ?>
                            </span>
                        </div>
                        
                        <h3 style="margin: 0.5rem 0; font-size: 1.1rem; line-height: 1.3;">
                            <?= htmlspecialchars($book['title']); ?>
                        </h3>
                        <p style="color: var(--gray-text); font-size: 0.9rem; margin-bottom: 1rem;">
                            by <?= htmlspecialchars($book['author']); ?>
                        </p>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.8rem; border-top: 1px solid #f1f5f9;">
                            <span class="price-tag">$<?= number_format($book['price'], 2); ?></span>
                            <a href="book_details.php?id=<?= $book['book_id']; ?>" class="btn btn-primary-sm">View</a>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; color: var(--gray-text);">
                <h3 style="margin-bottom: 1rem;">No books found matching your criteria.</h3>
                <a href="search.php" class="btn btn-outline">Clear Filters</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>