<?php
require_once 'auth.php';
require_once 'db.php';

$msg = '';
$err = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare('SELECT id, full_name, email, password_hash, phone FROM users WHERE email = ? LIMIT 1');
    $user = null;
    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
        ];
        header('Location: index.php');
        exit;
    }

    $msg = 'Invalid email or password.';
    $err = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | MoviesHub</title><link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="panel">
    <h2>User Login</h2>
    <?php if ($msg): ?><div class="alert <?= $err ? 'error' : '' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <form method="post" class="form-grid">
      <label>Email<input type="email" name="email" required></label>
      <label>Password<input type="password" name="password" required></label>
      <div class="full">
        <button class="btn primary" type="submit">Login</button>
        <a class="btn secondary" href="register.php">Register</a>
        <a class="btn secondary" href="admin.php">Admin Login</a>
      </div>
    </form>
  </div>
</body>
</html>
