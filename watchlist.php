<?php
require_once 'auth.php';
require_once 'db.php';
requireLogin();

$user = currentUser();
$userId = (int)$user['id'];

$stmt = $conn->prepare('SELECT m.* FROM watchlist w JOIN movies m ON w.movie_id = m.id WHERE w.user_id = ? ORDER BY w.created_at DESC');
$movies = false;
if ($stmt) {
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $movies = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Watchlist | MoviesHub</title><link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="topbar"><div class="brand">🎬 MoviesHub</div><nav><a href="index.php">Home</a><a href="logout.php">Logout</a></nav></header>
  <main class="panel">
    <h2>YourWatchlist</h2>
    <section class="movie-grid">
      <?php while ($movies && ($movie = $movies->fetch_assoc())): ?>
      <article class="movie-card">
        <div class="movie-media"><img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>"></div>
        <div class="movie-info">
          <span class="badge"><?= htmlspecialchars($movie['category']) ?></span>
          <h3><?= htmlspecialchars($movie['title']) ?></h3>
          <p><?= htmlspecialchars($movie['description']) ?></p>
        </div>
      </article>
      <?php endwhile; ?>
    </section>
  </main>
</body>
</html>
