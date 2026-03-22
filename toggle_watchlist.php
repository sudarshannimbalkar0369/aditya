<?php
header('Content-Type: application/json');
require_once 'db.php';

$movieId = isset($_POST['movie_id']) ? (int) $_POST['movie_id'] : 0;
if ($movieId <= 0) {
    echo json_encode(['status' => 'error']);
    exit;
}

$check = $conn->prepare('SELECT id FROM watchlist WHERE movie_id = ?');
$check->bind_param('i', $movieId);
$check->execute();
$result = $check->get_result();

if ($result && $result->num_rows > 0) {
    $delete = $conn->prepare('DELETE FROM watchlist WHERE movie_id = ?');
    $delete->bind_param('i', $movieId);
    $delete->execute();
    echo json_encode(['status' => 'removed']);
    $delete->close();
} else {
    $insert = $conn->prepare('INSERT IGNORE INTO watchlist (movie_id) VALUES (?)');
    $insert->bind_param('i', $movieId);
    $insert->execute();
    echo json_encode(['status' => 'added']);
    $insert->close();
}

$check->close();
?>
