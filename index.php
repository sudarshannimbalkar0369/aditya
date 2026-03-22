<?php
require_once 'db.php';

$movies = [];
$movieResult = $conn->query('SELECT * FROM movies ORDER BY created_at DESC');
if ($movieResult) {
    while ($row = $movieResult->fetch_assoc()) {
        $movies[] = $row;
    }
}

$watchlistMap = [];
$watchlistResult = $conn->query('SELECT movie_id FROM watchlist');
if ($watchlistResult) {
    while ($row = $watchlistResult->fetch_assoc()) {
        $watchlistMap[(int)$row['movie_id']] = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CineVerse | Movie Recommendations</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <header class="hero" id="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>CineVerse Picks</h1>
            <p>Discover Sci‑Fi, Horror, Thriller, Action, Romance and more in one stylish movie space.</p>
            <div class="hero-actions">
                <a href="#movies" class="btn primary">Explore Movies</a>
                <a href="admin.php" class="btn secondary">Admin Panel</a>
            </div>
        </div>
    </header>

    <main>
        <section class="toolbar">
            <h2 id="movies">Trending Categories</h2>
            <div class="filters" id="filters">
                <button class="active" data-category="All">All</button>
                <button data-category="Sci-Fi">Sci-Fi</button>
                <button data-category="Horror">Horror</button>
                <button data-category="Thriller">Thriller</button>
                <button data-category="Action">Action</button>
                <button data-category="Romance">Romance</button>
                <button data-category="Animation">Animation</button>
            </div>
        </section>

        <section class="movie-row-controls">
            <button id="scrollLeft" class="circle-btn">◀</button>
            <button id="scrollRight" class="circle-btn">▶</button>
        </section>

        <section class="movie-grid" id="movieGrid">
            <?php foreach ($movies as $movie): ?>
                <article
                    class="movie-card"
                    data-category="<?= htmlspecialchars($movie['category']) ?>"
                    data-bg="<?= htmlspecialchars($movie['poster_url']) ?>"
                    data-teaser="<?= htmlspecialchars($movie['teaser_url']) ?>"
                >
                    <div class="movie-media">
                        <img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>" />
                        <?php if (!empty($movie['teaser_preview_url'])): ?>
                            <video muted loop preload="none" class="preview-video">
                                <source src="<?= htmlspecialchars($movie['teaser_preview_url']) ?>" type="video/mp4" />
                            </video>
                        <?php endif; ?>
                    </div>
                    <div class="movie-info">
                        <span class="badge"><?= htmlspecialchars($movie['category']) ?></span>
                        <h3><?= htmlspecialchars($movie['title']) ?></h3>
                        <p><?= htmlspecialchars($movie['description']) ?></p>
                        <div class="movie-meta">
                            <strong>⭐ <?= htmlspecialchars($movie['rating']) ?></strong>
                            <button class="watchlist-btn <?= isset($watchlistMap[(int)$movie['id']]) ? 'added' : '' ?>" data-id="<?= (int)$movie['id'] ?>">
                                <?= isset($watchlistMap[(int)$movie['id']]) ? 'In Watchlist' : '+ Watchlist' ?>
                            </button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

    <div id="teaserModal" class="modal hidden">
        <div class="modal-content">
            <button id="closeModal" class="close-btn">✕</button>
            <iframe
                id="teaserFrame"
                width="100%"
                height="100%"
                src=""
                title="Movie Teaser"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
            ></iframe>
        </div>
    </div>

    <script>
        const MOVIES = <?= json_encode($movies, JSON_UNESCAPED_SLASHES); ?>;
    </script>
    <script src="script.js"></script>
</body>
</html>
