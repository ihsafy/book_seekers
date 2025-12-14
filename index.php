<?php
// 1. Connect to Database & Start Session
require_once 'config/db.php';
// session_start(); // Ensure session is started in header.php or config/db.php

// 2. Fetch Latest Books
$sql = "SELECT * FROM books WHERE status = 'Available' ORDER BY created_at DESC LIMIT 6"; 
$result = $conn->query($sql);

// 3. Fetch Unique Genres for the Filter Section (Optional improvement)
$genreSql = "SELECT DISTINCT genre FROM books LIMIT 5";
$genreResult = $conn->query($genreSql);
?>

<?php include 'includes/header.php'; ?>

<!-- Embedded CSS for Modern Styling -->
<style>
    :root {
        --primary: #4f46e5; /* Indigo */
        --primary-dark: #4338ca;
        --secondary: #ec4899; /* Pink accent */
        --dark: #1e293b;
        --light: #f8fafc;
        --gray: #64748b;
        --white: #ffffff;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --radius: 12px;
    }

    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, #e0e7ff 0%, #f3e8ff 100%);
        padding: 6rem 1rem;
        text-align: center;
        border-radius: 0 0 2rem 2rem;
        margin-bottom: 4rem;
    }

    .hero-title {
        font-family: 'Poppins', sans-serif;
        font-size: 3.5rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 1rem;
        line-height: 1.2;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        color: var(--gray);
        margin-bottom: 2.5rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Search Bar in Hero */
    .hero-search-box {
        background: var(--white);
        padding: 0.75rem;
        border-radius: 50px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        margin: 0 auto 2.5rem;
        display: flex;
        gap: 0.5rem;
    }

    .hero-search-input {
        border: none;
        flex-grow: 1;
        padding: 0 1.5rem;
        font-size: 1rem;
        outline: none;
        border-radius: 50px;
    }

    .hero-search-btn {
        background: var(--primary);
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }

    .hero-search-btn:hover { background: var(--primary-dark); }

    /* Why Choose Us Section */
    .why-us-section {
        margin-bottom: 5rem;
        text-align: center;
    }

    .why-us-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }

    .info-box {
        background: var(--white);
        padding: 2.5rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
        transition: transform 0.3s ease;
        text-align: left;
    }

    .info-box:hover {
        transform: translateY(-5px);
        border-color: var(--primary);
    }

    .info-icon {
        width: 60px;
        height: 60px;
        background: #e0e7ff;
        color: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .info-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 1rem;
    }

    .info-text {
        color: var(--gray);
        line-height: 1.6;
        font-size: 0.95rem;
    }

    /* Section Headings */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .section-title {
        font-size: 2rem;
        color: var(--dark);
        font-weight: 700;
    }

    /* Book Grid */
    .grid-books {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 2rem;
    }

    .book-card {
        background: var(--white);
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
    }

    .book-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .card-img-container {
        height: 300px;
        background-color: #f8fafc;
        position: relative;
        overflow: hidden;
    }

    .book-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .book-card:hover .book-img { transform: scale(1.05); }

    .card-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.9);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--primary);
        backdrop-filter: blur(4px);
    }

    .card-body {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .book-genre {
        color: var(--secondary);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .book-title {
        font-size: 1.25rem;
        margin: 0 0 0.5rem;
        color: var(--dark);
        line-height: 1.4;
    }

    .book-author {
        color: var(--gray);
        font-size: 0.9rem;
        margin-bottom: auto; /* Pushes price/button to bottom */
    }

    .card-footer {
        margin-top: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .book-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
    }

    .btn-view {
        background: var(--dark);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: background 0.2s;
    }
    .btn-view:hover { background: var(--primary); }

    /* Genre Tags */
    .genre-list {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 2rem;
        justify-content: center;
    }
    .genre-tag {
        background: var(--light);
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        color: var(--gray);
        text-decoration: none;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }
    .genre-tag:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    @media (max-width: 768px) {
        .hero-title { font-size: 2.5rem; }
        .hero-section { padding: 4rem 1rem; }
    }
</style>

<!-- HERO SECTION -->
<header class="hero-section">
    <div class="container">
        <h1 class="hero-title">
            Find Your Next<br>Favorite Book
        </h1>
        <p class="hero-subtitle">
            Join the community of trusted book lovers. Buy, sell, or exchange your old books with zero hidden charges.
        </p>

        <!-- Search Bar -->
        <form action="search.php" method="GET" class="hero-search-box">
            <input type="text" name="q" class="hero-search-input" placeholder="Search by title, author, or ISBN...">
            <button type="submit" class="hero-search-btn">Search</button>
        </form>

        <!-- Quick Links -->
        <div class="genre-list">
            <span style="align-self: center; color: var(--gray); font-size: 0.9rem;">Popular:</span>
            <?php 
            if($genreResult->num_rows > 0) {
                while($g = $genreResult->fetch_assoc()) {
                    echo '<a href="search.php?genre='.urlencode($g['genre']).'" class="genre-tag">'.htmlspecialchars($g['genre']).'</a>';
                }
            } else {
                echo '<a href="#" class="genre-tag">Fiction</a><a href="#" class="genre-tag">Science</a>';
            }
            ?>
        </div>
    </div>
</header>

<main class="container">
    
    <!-- WHY PUTHI PUSTAK SECTION (New Addition) -->
    <div class="why-us-section">
        <h2 class="section-title" style="text-align: center;">Why should you use PuthiPustak?</h2>
        <div style="width: 60px; height: 4px; background: var(--primary); margin: 10px auto 0;"></div>

        <div class="why-us-grid">
            <!-- Box 1 -->
            <div class="info-box">
                <div class="info-icon">💰</div>
                <h3 class="info-title">Cost‑effectiveness</h3>
                <p class="info-text">
                    Used‑book platforms tend to offer books at lower cost than brand‑new ones — good for students or budget‑conscious readers (like you, since you may need textbooks or project‑related books for university).
                </p>
            </div>

            <!-- Box 2 -->
            <div class="info-box">
                <div class="info-icon">🌱</div>
                <h3 class="info-title">Environmental Choice</h3>
                <p class="info-text">
                    Buying used books is more sustainable (less demand for new print runs). Since you work on projects like BOWMS and e‑waste / green supply chain ideas, this aligns with eco‑friendly thinking.
                </p>
            </div>

            <!-- Box 3 -->
            <div class="info-box">
                <div class="info-icon">🤝</div>
                <h3 class="info-title">Flexibility for All</h3>
                <p class="info-text">
                    Good for student community. If PuthiPustak allows individuals to list used books, you might find textbooks or reference books previously used by other students — possibly saving money and matching what you need for coursework.
                </p>
            </div>
        </div>
    </div>

    <!-- FRESH ARRIVALS SECTION -->
    <div class="section-header">
        <h2 class="section-title">Fresh Arrivals</h2>
        <a href="search.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">View All &rarr;</a>
    </div>

    <div class="grid-books">
        <?php if ($result->num_rows > 0): ?>
            <?php while($book = $result->fetch_assoc()): ?>
                
                <div class="book-card">
                    <a href="book_details.php?id=<?= $book['book_id']; ?>" class="card-img-container">
                        <span class="card-badge"><?= htmlspecialchars($book['condition'] ?? 'Used'); ?></span>
                        <?php if($book['image_url']): ?>
                            <img src="uploads/books/<?= htmlspecialchars($book['image_url']); ?>" alt="<?= htmlspecialchars($book['title']); ?>" class="book-img">
                        <?php else: ?>
                            <div style="height:100%; display:flex; align-items:center; justify-content:center; color:#cbd5e1;">
                                No Image
                            </div>
                        <?php endif; ?>
                    </a>
                    
                    <div class="card-body">
                        <div class="book-genre"><?= htmlspecialchars($book['genre']); ?></div>
                        
                        <h3 class="book-title">
                            <a href="book_details.php?id=<?= $book['book_id']; ?>" style="text-decoration:none; color:inherit;">
                                <?= htmlspecialchars($book['title']); ?>
                            </a>
                        </h3>
                        
                        <p class="book-author">by <?= htmlspecialchars($book['author']); ?></p>
                        
                        <div class="card-footer">
                            <span class="book-price">$<?= number_format($book['price'], 2); ?></span>
                            <a href="book_details.php?id=<?= $book['book_id']; ?>" class="btn-view">Details</a>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #f8fafc; border-radius: 12px;">
                <h3 style="color: var(--gray);">No books listed yet.</h3>
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'seller'): ?>
                    <a href="add_book.php" class="btn-view" style="margin-top: 1rem; display:inline-block;">Start Selling</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- CALL TO ACTION -->
    <div style="background: var(--dark); color: white; border-radius: 20px; padding: 4rem; text-align: center; margin-top: 6rem;">
        <h2 style="margin-bottom: 1rem;">Ready to unclutter your bookshelf?</h2>
        <p style="color: #94a3b8; margin-bottom: 2rem;">Join 10,000+ users selling their books today.</p>
        
        <?php if(!isset($_SESSION['user_id'])): ?>
            <a href="register.php" style="background: var(--primary); color: white; padding: 1rem 2.5rem; border-radius: 50px; text-decoration: none; font-weight: 700;">Create Account</a>
        <?php else: ?>
            <a href="add_book.php" style="background: var(--primary); color: white; padding: 1rem 2.5rem; border-radius: 50px; text-decoration: none; font-weight: 700;">Sell a Book</a>
        <?php endif; ?>
    </div>

</main>

<?php include 'includes/footer.php'; ?>