<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'movieshub';

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($dbName);

$conn->query(
    'CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(140) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB'
);

$conn->query(
    'CREATE TABLE IF NOT EXISTS movies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(160) NOT NULL,
        category VARCHAR(60) NOT NULL,
        year_released SMALLINT DEFAULT NULL,
        description TEXT,
        poster_url VARCHAR(255) NOT NULL,
        teaser_url VARCHAR(255) NOT NULL,
        teaser_preview_url VARCHAR(255) DEFAULT NULL,
        rating DECIMAL(3,1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB'
);

$conn->query(
    'CREATE TABLE IF NOT EXISTS watchlist (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        movie_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_movie (user_id, movie_id),
        CONSTRAINT fk_watchlist_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT fk_watchlist_movie FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
    ) ENGINE=InnoDB'
);

$columnExists = function (string $table, string $column) use ($conn): bool {
    $safeTable = $conn->real_escape_string($table);
    $safeColumn = $conn->real_escape_string($column);
    $res = $conn->query("SHOW COLUMNS FROM `$safeTable` LIKE '$safeColumn'");
    return $res && $res->num_rows > 0;
};

// Migration support for older installs (pre-users watchlist / pre-year_released movies).
if (!$columnExists('movies', 'year_released')) {
    $conn->query('ALTER TABLE movies ADD COLUMN year_released SMALLINT DEFAULT NULL AFTER category');
}
if (!$columnExists('watchlist', 'user_id')) {
    $conn->query('ALTER TABLE watchlist ADD COLUMN user_id INT NULL AFTER id');
}
if ($columnExists('watchlist', 'movie_id') && $columnExists('watchlist', 'user_id')) {
    $conn->query('ALTER TABLE watchlist DROP INDEX unique_movie');
    $conn->query('ALTER TABLE watchlist ADD UNIQUE KEY unique_user_movie (user_id, movie_id)');
}
if ($columnExists('watchlist', 'user_id')) {
    $conn->query('ALTER TABLE watchlist MODIFY user_id INT NOT NULL');
}

$seedCount = (int) (($conn->query('SELECT COUNT(*) total FROM movies')->fetch_assoc()['total']) ?? 0);
if ($seedCount === 0) {
    $seedMovies = [
        ['Interstellar', 'Sci-Fi', 2014, 'A desperate mission through space and time to save Earth.', 'https://images.unsplash.com/photo-1472457897821-70d3819a0e24?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/zSWdZVtXT7E', 'https://media.istockphoto.com/id/1480591826/video/planet-earth-rotating-space-stock-video.mp4?s=mp4-640x640-is&k=20&c=N6GpocF74x5AJRDigw1k3DybZ0SqnX5nGooy2cuglRc=', 8.7],
        ['Dune', 'Sci-Fi', 2021, 'A gifted young man confronts destiny on a desert planet.', 'https://images.unsplash.com/photo-1532012197267-da84d127e765?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/8g18jFHCLXk', 'https://media.istockphoto.com/id/2160642258/video/sandy-desert-landscape-on-a-cloudy-day.mp4?s=mp4-640x640-is&k=20&c=4QJo4jv8WzyIjheYjZQdM_RhgcgG6zqYhMNyj2f7fc0=', 8.1],
        ['Avatar', 'Sci-Fi', 2009, 'A marine enters the world of Pandora and takes sides.', 'https://images.unsplash.com/photo-1610296669228-602fa827fc1f?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/5PSNL1qE6VY', 'https://media.istockphoto.com/id/1263994244/video/futuristic-alien-forest.mp4?s=mp4-640x640-is&k=20&c=Fy8Ga6uB5vCUiJEdvECrTnlZQ4G0xqD7mKVj7A3mN7A=', 7.9],
        ['Oppenheimer', 'Thriller', 2023, 'The story of J. Robert Oppenheimer and the atomic era.', 'https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/uYPbbksJxIg', 'https://media.istockphoto.com/id/1388018960/video/fire-burst-background.mp4?s=mp4-640x640-is&k=20&c=Qt9kb5aYUBFSJ8h-KCEjMf8KxxgL9WaJqnY5fW6fqPQ=', 8.5],
        ['Se7en', 'Thriller', 1995, 'Detectives track a serial killer obsessed with seven sins.', 'https://images.unsplash.com/photo-1478720568477-152d9b164e26?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/znmZoVkCjpI', 'https://media.istockphoto.com/id/1477124108/video/night-city-traffic.mp4?s=mp4-640x640-is&k=20&c=VYz0l1E4mda0OrD5z8M90oCbGyF_F7fsRG3Gzdh0dX8=', 8.6],
        ['Gone Girl', 'Thriller', 2014, 'A marriage mystery spirals into media frenzy.', 'https://images.unsplash.com/photo-1517637382994-f02da38c6728?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/2-_-1nJf8Vg', 'https://media.istockphoto.com/id/1206024966/video/rain-drops-on-window-at-night.mp4?s=mp4-640x640-is&k=20&c=5Y6bVuQNkfU9mLUOktp9dgblEOVr7A4x8AC8bo0P8JY=', 8.1],
        ['The Conjuring', 'Horror', 2013, 'Investigators battle an ancient dark force in a farmhouse.', 'https://images.unsplash.com/photo-1509248961158-e54f6934749c?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/k10ETZ41q5o', 'https://media.istockphoto.com/id/1353802544/video/scary-dark-corridor-with-flickering-lights-and-smoke.mp4?s=mp4-640x640-is&k=20&c=R9fR-r1lYGMYxVFhJfH_DMFtR1j3rxqedkQSEvAEfVI=', 7.8],
        ['It', 'Horror', 2017, 'A shape-shifting clown terrorizes a small town.', 'https://images.unsplash.com/photo-1570612861542-284f4c12e75f?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/xKJmEC5ieOk', 'https://media.istockphoto.com/id/1135050912/video/halloween-pumpkin-with-candles.mp4?s=mp4-640x640-is&k=20&c=GIGZKAf6z9hZJjI1N2M6mZXQXlLAcKTG2Jf0zV54jdc=', 7.3],
        ['A Quiet Place', 'Horror', 2018, 'Silence means survival in a world of sound-hunting creatures.', 'https://images.unsplash.com/photo-1503435980610-a51f3ddfee50?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/WR7cc5t7tv8', 'https://media.istockphoto.com/id/468267633/video/dark-forest-in-fog.mp4?s=mp4-640x640-is&k=20&c=uBNSnUCElZf8W2sR9bck8YQxFQ7ZA6B9lAf68vBnrG8=', 7.5],
        ['Inception', 'Action', 2010, 'Dream thieves attempt an impossible heist inside the mind.', 'https://images.unsplash.com/photo-1517602302552-471fe67acf66?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/YoHD9XEInc0', 'https://media.istockphoto.com/id/1465945996/video/neon-city-futuristic-background.mp4?s=mp4-640x640-is&k=20&c=UU0h4qt77AsUYxxYOlK6i6f9_ZNDQQgQvFjQW0fvkY4=', 8.8],
        ['Mad Max: Fury Road', 'Action', 2015, 'A high-octane chase through a post-apocalyptic wasteland.', 'https://images.unsplash.com/photo-1542204625-de293a0f5d0f?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/hEJnMQG9ev8', 'https://media.istockphoto.com/id/1404254508/video/desert-road-driving.mp4?s=mp4-640x640-is&k=20&c=tFndf00mV-vJSVE8mpXQ3fT6MhCUuJ0ENwICg13JU_E=', 8.2],
        ['John Wick', 'Action', 2014, 'A legendary assassin returns for vengeance.', 'https://images.unsplash.com/photo-1513106580091-1d82408b8cd6?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/2AUmvWm5ZDQ', 'https://media.istockphoto.com/id/1680057124/video/car-lights-at-night.mp4?s=mp4-640x640-is&k=20&c=9aDs9o37aXwq4Qtl4bn8m4x8Al0h9C9WsI6a6YuOJt0=', 7.9],
        ['The Notebook', 'Romance', 2004, 'A timeless romance spanning decades.', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/FC6biTjEyZw', 'https://media.istockphoto.com/id/1338053819/video/romantic-sunset-beach.mp4?s=mp4-640x640-is&k=20&c=8IrA3YUN_miMdv4bcoOMw0yd5QtbFHLQ5lmh3Asf7f4=', 7.9],
        ['La La Land', 'Romance', 2016, 'A pianist and actor chase love and ambition in LA.', 'https://images.unsplash.com/photo-1520209759809-a9bcb6cb3241?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/0pdqf4P9MB8', 'https://media.istockphoto.com/id/1344040608/video/couple-holding-hands.mp4?s=mp4-640x640-is&k=20&c=1L9TRzUexHsT_2q3G4AUCkR_lNfBzhBSv5lDz6pQzAo=', 8.0],
        ['Titanic', 'Romance', 1997, 'An epic love story on the ill-fated ship.', 'https://images.unsplash.com/photo-1473773508845-188df298d2d1?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/2e-eXJ6HgkQ', 'https://media.istockphoto.com/id/1271937325/video/ship-at-sea.mp4?s=mp4-640x640-is&k=20&c=l4Y86EjtwVfD6Y8xvA8YomznVnWfnZJiHvQv_lMx0qU=', 7.9],
        ['Coco', 'Animation', 2017, 'A musical journey through the Land of the Dead.', 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/xlnPHQ3TLX8', 'https://media.istockphoto.com/id/1425402807/video/colorful-confetti-background.mp4?s=mp4-640x640-is&k=20&c=21VYVnX6jttA4y7sMZDXMpaWQ2qj8qV6PdYriRO6S4A=', 8.4],
        ['Spider-Man: Into the Spider-Verse', 'Animation', 2018, 'A teen hero meets spider-heroes from parallel worlds.', 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/g4Hbz2jLxvQ', 'https://media.istockphoto.com/id/1466188161/video/comic-style-background.mp4?s=mp4-640x640-is&k=20&c=Hnclq4Qxhm6ur9GI6EdcpYxCeQrTkdrXpS7vVvR9z0A=', 8.4],
        ['Toy Story 4', 'Animation', 2019, 'Woody and friends go on an emotional road adventure.', 'https://images.unsplash.com/photo-1596727147705-61a532a659bd?auto=format&fit=crop&w=1200&q=80', 'https://www.youtube.com/embed/wmiIUN-7qhE', 'https://media.istockphoto.com/id/535629218/video/colorful-toys-background.mp4?s=mp4-640x640-is&k=20&c=Qj-k7Q5yT3D-UGxQC0ZnPv0Rr5H6fUyyfPVwCDzdM_Y=', 7.7]
    ];

    $stmt = $conn->prepare('INSERT INTO movies (title, category, year_released, description, poster_url, teaser_url, teaser_preview_url, rating) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    if ($stmt) {
        foreach ($seedMovies as $movie) {
            $stmt->bind_param('ssissssd', $movie[0], $movie[1], $movie[2], $movie[3], $movie[4], $movie[5], $movie[6], $movie[7]);
            $stmt->execute();
        }
        $stmt->close();
    }
}
?>
