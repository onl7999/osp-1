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
    if ($user = verifyUser($u, $p)) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: portal.php');
        exit;
    } else {
        $message = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Expense Tracker</title>
  <style>
    body { font-family: sans-serif; background: #f2f2f2; margin: 0; padding: 2rem; display: flex; justify-content: center; align-items: center; height: 100vh; }
    .card { background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
    .card h1 { margin-top: 0; }
    .form-group { margin-bottom: 1rem; }
    label { display: block; margin-bottom: .5rem; }
    input { width: 100%; padding: .5rem; border: 1px solid #ccc; border-radius: 4px; }
    .btn { width: 100%; padding: .7rem; background: #007BFF; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
    .btn:hover { background: #0056b3; }
    .message { color: red; margin-bottom: 1rem; }
    .footer { margin-top: 1rem; text-align: center; font-size: .9rem; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Expense Tracker</h1>
    <p>Track your spending, categorize expenses, and view your financial summaries.</p>

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
      <button class="btn" type="submit">Login</button>
    </form>

    <div class="footer">
      No account? <a href="reg.php">Register here</a>
    </div>
  </div>
</body>
</html>
