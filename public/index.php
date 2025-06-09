<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../fn/functions.php';

$user_id       = $_SESSION['user_id'];
$years         = getExpenseYears($user_id);
$selectedYear  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');

$months = [
    0 => 'All',
    1 => 'January',   2 => 'February',
    3 => 'March',     4 => 'April',
    5 => 'May',       6 => 'June',
    7 => 'July',      8 => 'August',
    9 => 'September',10 => 'October',
    11=> 'November', 12 => 'December'
];

if ($selectedMonth === 0) {
    // whole year
    $expenses = getExpensesByYear($user_id, $selectedYear);
} else {
    // single month
    $expenses = getExpensesByMonth($user_id, $selectedYear, $selectedMonth);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit My Expenses</title>
  <link rel="stylesheet" href="css/style.css" type="text/css" />
</head>
<body class="container">
  <header>
    <h1>Edit My Expenses</h1>
    <nav>
      Hello, <?= htmlspecialchars($_SESSION['username']) ?> |
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <form method="get" class="mb-1">
    <label>
      Year:
      <select name="year" onchange="this.form.submit()">
        <?php foreach ($years as $yr): ?>
          <option value="<?= $yr ?>" <?= $yr === $selectedYear ? 'selected' : '' ?>>
            <?= $yr ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>
      Month:
      <select name="month" onchange="this.form.submit()">
        <?php foreach ($months as $num => $name): ?>
          <option value="<?= $num ?>" <?= $num === $selectedMonth ? 'selected' : '' ?>>
            <?= $name ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <noscript><button class="btn" type="submit">Go</button></noscript>
  </form>

  <?php if (empty($expenses)): ?>
    <?php if ($selectedMonth === 0): ?>
      <p>No spendings in <?= $selectedYear ?>.</p>
    <?php else: ?>
      <p>No spendings for <?= $months[$selectedMonth] ?> <?= $selectedYear ?>.</p>
    <?php endif; ?>
  <?php else: ?>
    <table class="table">
      <thead>
        <tr>
          <th>Date &amp; Time</th>
          <th>Category</th>
          <th>Amount</th>
          <th>Description</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($expenses as $e): ?>
          <tr>
            <td><?= date('Y-m-d H:i', strtotime($e['expense_date'])) ?></td>
            <td><?= htmlspecialchars($e['category_name'] ?? '—') ?></td>
            <td><?= htmlspecialchars($e['amount']) ?></td>
            <td><?= htmlspecialchars($e['description']) ?></td>
            <td>
              <a href="edit.php?id=<?= urlencode($e['id']) ?>">Edit</a> |
              <a href="delete.php?id=<?= urlencode($e['id']) ?>"
                 onclick="return confirm('Delete this expense?');">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <p><a href="portal.php" class="btn">Back to Dashboard</a></p>
</body>
</html>
