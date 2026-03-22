<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'movie_recommendation';

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($dbName);

$conn->query(
    'CREATE TABLE IF NOT EXISTS movies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        category VARCHAR(60) NOT NULL,
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
        movie_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_movie (movie_id),
        CONSTRAINT fk_watchlist_movie FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
    ) ENGINE=InnoDB'
);

$seedCountResult = $conn->query('SELECT COUNT(*) AS total FROM movies');
$seedCount = $seedCountResult ? (int) $seedCountResult->fetch_assoc()['total'] : 0;

if ($seedCount === 0) {
    $seedMovies = [
        ['Interstellar', 'Sci-Fi', 'A team travels through a wormhole to save humanity.', 'https://images.unsplash.com/photo-1472457897821-70d3819a0e24?auto=format&fit=crop&w=1000&q=80', 'https://www.youtube.com/embed/zSWdZVtXT7E', 'https://media.istockphoto.com/id/1480591826/video/planet-earth-rotating-space-stock-video.mp4?s=mp4-640x640-is&k=20&c=N6GpocF74x5AJRDigw1k3DybZ0SqnX5nGooy2cuglRc=', 8.7],
        ['The Conjuring', 'Horror', 'Paranormal investigators help a family terrorized by dark forces.', 'https://images.unsplash.com/photo-1509248961158-e54f6934749c?auto=format&fit=crop&w=1000&q=80', 'https://www.youtube.com/embed/k10ETZ41q5o', 'https://media.istockphoto.com/id/1353802544/video/scary-dark-corridor-with-flickering-lights-and-smoke.mp4?s=mp4-640x640-is&k=20&c=R9fR-r1lYGMYxVFhJfH_DMFtR1j3rxqedkQSEvAEfVI=', 7.8],
        ['Se7en', 'Thriller', 'Two detectives hunt a serial killer using seven deadly sins.', 'https://images.unsplash.com/photo-1478720568477-152d9b164e26?auto=format&fit=crop&w=1000&q=80', 'https://www.youtube.com/embed/znmZoVkCjpI', 'https://media.istockphoto.com/id/1477124108/video/night-city-traffic.mp4?s=mp4-640x640-is&k=20&c=VYz0l1E4mda0OrD5z8M90oCbGyF_F7fsRG3Gzdh0dX8=', 8.6],
        ['Inception', 'Action', 'A thief steals secrets through dream-sharing technology.', 'https://images.unsplash.com/photo-1517602302552-471fe67acf66?auto=format&fit=crop&w=1000&q=80', 'https://www.youtube.com/embed/YoHD9XEInc0', 'https://media.istockphoto.com/id/1465945996/video/neon-city-futuristic-background.mp4?s=mp4-640x640-is&k=20&c=UU0h4qt77AsUYxxYOlK6i6f9_ZNDQQgQvFjQW0fvkY4=', 8.8],
        ['Coco', 'Animation', 'A young musician explores the Land of the Dead.', 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=1000&q=80', 'https://www.youtube.com/embed/xlnPHQ3TLX8', 'https://media.istockphoto.com/id/1425402807/video/colorful-confetti-background.mp4?s=mp4-640x640-is&k=20&c=21VYVnX6jttA4y7sMZDXMpaWQ2qj8qV6PdYriRO6S4A=', 8.4],
        ['The Notebook', 'Romance', 'A timeless love story that spans decades.', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1000&q=80', 'https://www.youtube.com/embed/FC6biTjEyZw', 'https://media.istockphoto.com/id/1338053819/video/romantic-sunset-beach.mp4?s=mp4-640x640-is&k=20&c=8IrA3YUN_miMdv4bcoOMw0yd5QtbFHLQ5lmh3Asf7f4=', 7.9],
    ];

    $stmt = $conn->prepare('INSERT INTO movies (title, category, description, poster_url, teaser_url, teaser_preview_url, rating) VALUES (?, ?, ?, ?, ?, ?, ?)');
    if ($stmt) {
        foreach ($seedMovies as $movie) {
            $stmt->bind_param('ssssssd', $movie[0], $movie[1], $movie[2], $movie[3], $movie[4], $movie[5], $movie[6]);
            $stmt->execute();
        }
        $stmt->close();
    }
}
?>
