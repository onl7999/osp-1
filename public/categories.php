<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../fn/functions.php';

$message = '';
// RENAME ITEM
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rename
    if (isset($_POST['rename'], $_POST['id'], $_POST['new_name'])) {
        $id   = (int)$_POST['id'];
        $name = trim($_POST['new_name']);
        if ($name !== '') {
            updateCategory($id, $name);
            header('Location: categories.php');
            exit;
        } else {
            $message = 'Name cannot be empty.';
        }
    }
    // del
    if (isset($_POST['delete'], $_POST['id'])) {
        $id = (int)$_POST['id'];
        deleteCategory($id);
        header('Location: categories.php');
        exit;
    }
    // CREATE
    if (isset($_POST['add'], $_POST['add_name'])) {
        $name = trim($_POST['add_name']);
        if ($name !== '') {
            addCategory($name);
            header('Location: categories.php');
            exit;
        } else {
            $message = 'Name cannot be empty.';
        }
    }
}

$cats = getCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Categories</title>
  <link rel="stylesheet" href="css/style.css" type="text/css" />
</head>
<body>
  <div class="container">
    <h1>Manage Categories</h1>

    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <table class="table mb-1">
      <thead>
        <tr>
          <th>Category Name</th>
          <th style="width: 180px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cats as $c): ?>
        <tr>
          <form method="POST" style="display: contents;">
            <td>
              <input
                type="text"
                name="new_name"
                value="<?= htmlspecialchars($c['name']) ?>"
                required
                style="width:100%; padding:4px;"
              >
            </td>
            <td>
              <input type="hidden" name="id" value="<?= $c['id'] ?>">
              <button
                type="submit"
                name="rename"
                class="btn"
                style="margin-right:0.5rem;"
              >
                Rename
              </button>
              <button
                type="submit"
                name="delete"
                class="btn"
                onclick="return confirm('Delete this category?');"
              >
                Delete
              </button>
            </td>
          </form>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <form method="POST" class="mb-1">
      <label>
        New Category:
        <input type="text" name="add_name" required>
      </label>
      <button type="submit" name="add" class="btn">Add</button>
    </form>

    <p><a href="portal.php" class="btn">Back to Dashboard</a></p>
  </div>
</body>
</html>
