<?php
require_once 'auth.php';
require_once 'db.php';

$alert = '';
$error = false;

if (isset($_POST['admin_logout'])) {
    unset($_SESSION['admin_logged_in']);
    header('Location: admin.php');
    exit;
}

if (isset($_POST['admin_username'], $_POST['admin_password'])) {
    if ($_POST['admin_username'] === 'aditya' && $_POST['admin_password'] === '123') {
        $_SESSION['admin_logged_in'] = true;
        $alert = 'Admin login successful.';
    } else {
        $alert = 'Invalid admin username or password.';
        $error = true;
    }
}

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true && isset($_POST['title'])) {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $year = (int) ($_POST['year_released'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $poster = trim($_POST['poster_url'] ?? '');
    $teaser = trim($_POST['teaser_url'] ?? '');
    $preview = trim($_POST['teaser_preview_url'] ?? '');
    $rating = (float) ($_POST['rating'] ?? 0);

    if ($title && $category && $poster && $teaser) {
        $stmt = $conn->prepare('INSERT INTO movies (title, category, year_released, description, poster_url, teaser_url, teaser_preview_url, rating) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssissssd', $title, $category, $year, $description, $poster, $teaser, $preview, $rating);
        $stmt->execute();
        $stmt->close();
        $alert = 'Movie added successfully!';
        $error = false;
    } else {
        $alert = 'Please fill all required fields.';
        $error = true;
    }
}

$movies = $conn->query('SELECT title, category, year_released, rating FROM movies ORDER BY created_at DESC LIMIT 18');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MoviesHub Admin</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="topbar">
    <div class="brand">🎬 MoviesHub Admin</div>
    <nav>
      <a href="index.php">Back Home</a>
      <?php if (!empty($_SESSION['admin_logged_in'])): ?>
      <form method="post" style="display:inline;">
        <button class="btn secondary" type="submit" name="admin_logout" value="1">Admin Logout</button>
      </form>
      <?php endif; ?>
    </nav>
  </header>

  <div class="panel">
    <?php if ($alert): ?>
      <div class="alert <?= $error ? 'error' : '' ?>"><?= htmlspecialchars($alert) ?></div>
    <?php endif; ?>

    <?php if (empty($_SESSION['admin_logged_in'])): ?>
      <h2>Admin Login</h2>
      <p>Use the credentials configured by you.</p>
      <form method="post" class="form-grid">
        <label>
          Admin Name
          <input type="text" name="admin_username" placeholder="aditya" required>
        </label>
        <label>
          Password
          <input type="password" name="admin_password" placeholder="123" required>
        </label>
        <div class="full"><button class="btn primary" type="submit">Login as Admin</button></div>
      </form>
    <?php else: ?>
      <h2>Add New Movie</h2>
      <form method="post" class="form-grid">
        <label>Movie Title *<input type="text" name="title" required></label>
        <label>Category *
          <select name="category" required>
            <option>Sci-Fi</option><option>Horror</option><option>Thriller</option>
            <option>Action</option><option>Romance</option><option>Animation</option>
            <option>Drama</option><option>Comedy</option>
          </select>
        </label>
        <label>Year Released <input type="number" name="year_released" min="1900" max="2099" value="2024"></label>
        <label>Rating <input type="number" name="rating" min="0" max="10" step="0.1" value="8.0"></label>
        <label class="full">Description <textarea name="description" rows="3"></textarea></label>
        <label>Poster URL *<input type="url" name="poster_url" required></label>
        <label>Teaser URL (Embed) *<input type="url" name="teaser_url" required></label>
        <label class="full">Hover Preview Video URL<input type="url" name="teaser_preview_url"></label>
        <div class="full"><button class="btn primary" type="submit">Add Movie</button></div>
      </form>

      <h3>Latest Added Movies</h3>
      <section class="movie-grid">
        <?php while ($movies && ($movie = $movies->fetch_assoc())): ?>
          <article class="movie-card">
            <div class="movie-info">
              <span class="badge"><?= htmlspecialchars($movie['category']) ?></span>
              <h3><?= htmlspecialchars($movie['title']) ?></h3>
              <p><?= (int)$movie['year_released'] ?> • ⭐ <?= htmlspecialchars($movie['rating']) ?></p>
            </div>
          </article>
        <?php endwhile; ?>
      </section>
    <?php endif; ?>
  </div>
</body>
</html>
