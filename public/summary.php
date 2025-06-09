<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../fn/functions.php';

$user_id      = $_SESSION['user_id'];
$years        = getExpenseYears($user_id);
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// static month names
$months = [
   1=>'January',2=>'February',3=>'March',   4=>'April',
   5=>'May',    6=>'June',    7=>'July',    8=>'August',
   9=>'September',10=>'October',11=>'November',12=>'December'
];

// get monthly totals for the year
$monthlyTotals = getMonthlyTotalsByYear($user_id, $selectedYear);

// build full 12-month array so chart always shows Jan→Dec
$chartData = array_fill(1, 12, 0.0);
foreach ($monthlyTotals as $mt) {
    $chartData[(int)$mt['month']] = (float)$mt['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Monthly Summary</title>
  <link rel="stylesheet" href="css/style.css" type="text/css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="container">
  <header>
    <h1>Monthly Summary</h1>
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
          <option value="<?= $yr ?>" <?= $yr=== $selectedYear ? 'selected' : '' ?>>
            <?= $yr ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <noscript><button type="submit" class="btn">Go</button></noscript>
  </form>

  <?php if (array_sum($chartData) === 0): ?>
    <p>No spendings recorded for <?= $selectedYear ?>.</p>
  <?php else: ?>
    <canvas id="monthlyChart" width="600" height="300"></canvas>
    <script>
      const ctx = document.getElementById('monthlyChart').getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: <?= json_encode(array_values($months)) ?>,
          datasets: [{
            label: 'Total Spent (<?= $selectedYear ?>)',
            data: <?= json_encode(array_values($chartData)) ?>,
            backgroundColor: 'rgba(0,123,255,0.5)',
            borderColor: 'rgba(0,123,255,1)',
            borderWidth: 1
          }]
        },
        options: {
          scales: { y: { beginAtZero: true } }
        }
      });
    </script>
  <?php endif; ?>

  <p><a href="portal.php" class="btn">Back to Dashboard</a></p>
</body>
</html>
