<?php
require_once 'auth.php';
require_once 'db.php';

$user = currentUser();
$userId = $user['id'] ?? 0;

$movies = [];
$movieResult = $conn->query('SELECT * FROM movies ORDER BY created_at DESC');
while ($movieResult && ($row = $movieResult->fetch_assoc())) {
    $movies[] = $row;
}

$watchlistMap = [];
if ($userId > 0) {
    $wStmt = $conn->prepare('SELECT movie_id FROM watchlist WHERE user_id = ?');
    $wStmt->bind_param('i', $userId);
    $wStmt->execute();
    $result = $wStmt->get_result();
    while ($result && ($row = $result->fetch_assoc())) {
        $watchlistMap[(int) $row['movie_id']] = true;
    }
    $wStmt->close();
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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MoviesHub | Movie Recommendation Platform</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="spark spark-1"></div>
  <div class="spark spark-2"></div>
  <header class="topbar">
    <div class="brand">🎬 MoviesHub</div>
    <nav>
      <a href="index.php">Home</a>
      <a href="#categories">Categories</a>
      <a href="watchlist.php">YourWatchlist</a>
      <?php if ($user): ?>
        <span class="welcome">Hi, <?= htmlspecialchars($user['full_name']) ?></span>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
      <?php endif; ?>
      <a class="admin-chip" href="admin.php">Admin Login</a>
    </nav>
  </header>

  <section class="hero" id="hero">
    <div class="hero-blur-mask"></div>
    <div class="hero-content">
      <h1>Welcome to MoviesHub</h1>
      <p>Pick your mood and watch teasers instantly — Sci‑Fi, Horror, Thriller, Romance, Animation and more.</p>
      <div class="hero-actions">
        <a href="#movieGrid" class="btn primary">Start Exploring</a>
        <a href="watchlist.php" class="btn secondary">Open Watchlist</a>
      </div>
    </div>
  </section>

  <main>
    <section class="discover" id="categories">
      <h2>Choose Your Movie Vibe</h2>
      <div class="discover-actions">
        <div class="filters" id="filters">
          <button class="active" data-category="All">All</button>
          <button data-category="Sci-Fi">Sci-Fi</button>
          <button data-category="Horror">Horror</button>
          <button data-category="Thriller">Thriller</button>
          <button data-category="Action">Action</button>
          <button data-category="Romance">Romance</button>
          <button data-category="Animation">Animation</button>
        </div>
        <input type="search" id="searchInput" placeholder="Search movie name...">
      </div>
    </section>

    <section class="movie-grid" id="movieGrid">
      <?php foreach ($movies as $movie): ?>
        <article class="movie-card" data-category="<?= htmlspecialchars($movie['category']) ?>" data-bg="<?= htmlspecialchars($movie['poster_url']) ?>" data-title="<?= htmlspecialchars(strtolower($movie['title'])) ?>" data-teaser="<?= htmlspecialchars($movie['teaser_url']) ?>">
          <div class="movie-media">
            <img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
            <?php if (!empty($movie['teaser_preview_url'])): ?>
              <video muted loop preload="none" class="preview-video">
                <source src="<?= htmlspecialchars($movie['teaser_preview_url']) ?>" type="video/mp4">
              </video>
            <?php endif; ?>
          </div>
          <div class="movie-info">
            <div class="row-space">
              <span class="badge"><?= htmlspecialchars($movie['category']) ?></span>
              <small><?= (int) $movie['year_released'] ?></small>
            </div>
            <h3><?= htmlspecialchars($movie['title']) ?></h3>
            <p><?= htmlspecialchars($movie['description']) ?></p>
            <div class="movie-meta">
              <strong>⭐ <?= htmlspecialchars($movie['rating']) ?></strong>
              <button class="watchlist-btn <?= isset($watchlistMap[(int) $movie['id']]) ? 'added' : '' ?>" data-id="<?= (int) $movie['id'] ?>">
                <?= isset($watchlistMap[(int) $movie['id']]) ? 'Saved' : '+ Watchlist' ?>
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
      <iframe id="teaserFrame" src="" title="Movie teaser" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
    </div>
  </div>

  <script>
    const MOVIES = <?= json_encode($movies, JSON_UNESCAPED_SLASHES); ?>;
    const IS_LOGGED_IN = <?= $userId > 0 ? 'true' : 'false' ?>;
  </script>
  <script src="script.js"></script>
=======
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
