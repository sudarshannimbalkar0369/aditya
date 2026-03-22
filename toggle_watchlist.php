<?php
header('Content-Type: application/json');
require_once 'auth.php';
require_once 'db.php';

$user = currentUser();
if (!$user) {
    echo json_encode(['status' => 'not_logged_in']);
    exit;
}

$userId = (int) $user['id'];
$movieId = isset($_POST['movie_id']) ? (int) $_POST['movie_id'] : 0;
if ($movieId <= 0) {
    echo json_encode(['status' => 'error']);
    exit;
}

$check = $conn->prepare('SELECT id FROM watchlist WHERE user_id = ? AND movie_id = ?');
if (!$check) {
    echo json_encode(['status' => 'error']);
    exit;
}
$check->bind_param('ii', $userId, $movieId);
$check->execute();
$result = $check->get_result();

if ($result && $result->num_rows > 0) {
    $delete = $conn->prepare('DELETE FROM watchlist WHERE user_id = ? AND movie_id = ?');
    $delete->bind_param('ii', $userId, $movieId);
    $delete->execute();
    $delete->close();
    echo json_encode(['status' => 'removed']);
} else {
    $insert = $conn->prepare('INSERT IGNORE INTO watchlist (user_id, movie_id) VALUES (?, ?)');
    $insert->bind_param('ii', $userId, $movieId);
    $insert->execute();
    $insert->close();
    echo json_encode(['status' => 'added']);
}

$check->close();
?>
