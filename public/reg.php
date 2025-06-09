<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: portal.php');
    exit;
}
require_once __DIR__ . '/../fn/user_model.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username']);
    $p = trim($_POST['password']);
    $cp = trim($_POST['confirm_password']);

    if ($p !== $cp) {
        $message = 'Passwords do not match.';
    } elseif (findUserByUsername($u)) {
        $message = 'Username already taken.';
    } else {
        if (registerUser($u, $p)) {
            header('Location: login.php');
            exit;
        } else {
            $message = 'Registration failed.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register | Expense Tracker</title>
  <style>
    body { font-family: sans-serif; background: #f2f2f2; margin: 0; padding: 2rem; display: flex; justify-content: center; align-items: center; height: 100vh; }
    .card { background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
    .card h1 { margin-top: 0; }
    .form-group { margin-bottom: 1rem; }
    label { display: block; margin-bottom: .5rem; }
    input { width: 100%; padding: .5rem; border: 1px solid #ccc; border-radius: 4px; }
    .btn { width: 100%; padding: .7rem; background: #28a745; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
    .btn:hover { background: #218838; }
    .message { color: red; margin-bottom: 1rem; }
    .footer { margin-top: 1rem; text-align: center; font-size: .9rem; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Create Account</h1>
    <p>Sign up to start tracking your expenses.</p>

    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>
      </div>
      <button class="btn" type="submit">Register</button>
    </form>

    <div class="footer">
      Already have an account? <a href="login.php">Log in</a>
    </div>
  </div>
</body>
</html>
