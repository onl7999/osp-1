<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once __DIR__ . '/../fn/functions.php';

$id = $_GET['id'] ?? null;
$expense = getExpense($id, $_SESSION['user_id']);
$cats = getCategories();
if (!$expense) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    updateExpense($id, $_SESSION['user_id'], 
                  $_POST['category_id'] ?: null,
                  $_POST['amount'], $_POST['description'], $_POST['expense_date']);
    header('Location: index.php'); exit;
}
?>
<!DOCTYPE html>
<html><head><title>Edit Expense</title></head>
<link rel="stylesheet" href="css/style.css" type="text/css" />
<body>
  <h1>Edit Expense</h1>
  <form method="POST">
    <label>Date:<input type="date" name="expense_date" value="<?= $expense['expense_date'] ?>" required></label><br>
    <label>Category:
      <select name="category_id">
        <option value="">—</option>
        <?php foreach($cats as $c): ?>
        <option value="<?= $c['id'] ?>" <?= $expense['category_id'] == $c['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($c['name']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </label><br>
    <label>Amount:<input type="number" step="0.01" name="amount" value="<?= $expense['amount'] ?>" required></label><br>
    <label>Description:<br><textarea name="description"><?= $expense['description'] ?></textarea></label><br>
    <button type="submit">Update</button>
  </form>
</body></html>
