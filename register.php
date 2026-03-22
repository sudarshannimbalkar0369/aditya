<?php
require_once 'auth.php';
require_once 'db.php';

$msg = '';
$err = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name && $email && $phone && strlen($password) >= 6) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (full_name, email, password_hash, phone) VALUES (?, ?, ?, ?)');
        if ($stmt) {
            $stmt->bind_param('ssss', $name, $email, $hash, $phone);
            if ($stmt->execute()) {
                $msg = 'Registration successful. You can login now.';
            } else {
                $msg = 'Email already exists.';
                $err = true;
            }
            $stmt->close();
        }
    } else {
        $msg = 'Enter all fields (password minimum 6 chars).';
        $err = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | MoviesHub</title><link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="panel">
    <h2>Create Account</h2>
    <?php if ($msg): ?><div class="alert <?= $err ? 'error' : '' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <form method="post" class="form-grid">
      <label>Full Name<input type="text" name="full_name" required></label>
      <label>Email<input type="email" name="email" required></label>
      <label>Phone No<input type="text" name="phone" required></label>
      <label>Password<input type="password" name="password" minlength="6" required></label>
      <div class="full">
        <button class="btn primary" type="submit">Register</button>
        <a class="btn secondary" href="login.php">Back to Login</a>
      </div>
    </form>
  </div>
</body>
</html>
