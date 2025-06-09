<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../fn/functions.php';

$userId          = $_SESSION['user_id'];
$currentYear     = (int)date('Y');
$currentMonth    = (int)date('m');
$currentMonthName= date('F');

// ts month total
$monthExpenses = getExpensesByMonth($userId, $currentYear, $currentMonth);
$monthTotal    = array_reduce(
    $monthExpenses,
    fn($sum, $e) => $sum + (float)$e['amount'],
    0.0
);

// summary filter
$summaryType  = $_GET['summary_type']   ?? 'yearly';
$summaryYears = getExpenseYears($userId);
$selYear      = (int)($_GET['summary_year']  ?? $currentYear);
$selMonth     = (int)($_GET['summary_month'] ?? $currentMonth);
$months       = [
    1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',
    5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',
    9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'
];

// summary data
if ($summaryType === 'monthly') {
    $summaryData = getCategoryTotalsByMonth($userId, $selYear, $selMonth);
} else {
    $summaryData = getCategoryTotalsByYear($userId, $selYear);
}
$summaryTotal = array_reduce(
    $summaryData,
    fn($sum, $c) => $sum + (float)$c['total'],
    0.0
);

// add
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_expense'])) {
    $catId       = (int)$_POST['category_id'];
    $amount      = trim($_POST['amount']);
    $description = trim($_POST['description']);

    if (!empty($_POST['date_option']) && $_POST['date_option'] === 'custom') {
        $date = $_POST['expense_date'];
        $time = $_POST['expense_time'];
        $expenseDate = "$date $time:00";
    } else {
        $expenseDate = date('Y-m-d H:i:00');
    }

    if (addExpense($userId, $catId, $amount, $description, $expenseDate)) {
        header('Location: portal.php');
        exit;
    }
    $message = 'Failed to add expense.';
}

$categories = getCategories();
usort($categories, function ($a, $b) {
    if ($a['name'] === 'Others') return 1;
    if ($b['name'] === 'Others') return -1;
    return strcmp($a['name'], $b['name']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/style.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="container">
    <header>
      <h1>Dashboard</h1>
      <nav>
        Hello, <?= htmlspecialchars($_SESSION['username']) ?> |
        <a href="logout.php">Logout</a>
      </nav>
    </header>

    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="dashboard-grid" style="grid-template-columns: 2fr 1fr;">

      <div class="card">
        <h2>Overview</h2>

        <div class="mb-1">
          <h3>Expenses This Month</h3>
          <p>
            <strong><?= $currentMonthName ?> <?= $currentYear ?></strong><br>
            Total spent: <strong><?= number_format($monthTotal, 2) ?></strong>
          </p>
          <a href="index.php" class="btn">Edit My Expenses</a>
        </div>

        <hr>

        <div>
          <h3>Summary (<?= ucfirst($summaryType) ?>)</h3>
          <form method="get" class="mb-1">
            <label>
              View:
              <select name="summary_type" onchange="this.form.submit()">
                <option value="yearly" <?= $summaryType==='yearly'  ? 'selected':'' ?>>Yearly</option>
                <option value="monthly"<?= $summaryType==='monthly'?'selected':'' ?>>Monthly</option>
              </select>
            </label>

            <label>
              Year:
              <select name="summary_year" onchange="this.form.submit()">
                <?php foreach ($summaryYears as $yr): ?>
                  <option value="<?= $yr ?>" <?= $yr===$selYear?'selected':'' ?>><?= $yr ?></option>
                <?php endforeach; ?>
              </select>
            </label>

            <?php if ($summaryType==='monthly'): ?>
            <label>
              Month:
              <select name="summary_month" onchange="this.form.submit()">
                <?php foreach ($months as $num=>$m): ?>
                  <option value="<?= $num ?>" <?= $num===$selMonth?'selected':'' ?>><?= $m ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <?php endif; ?>
          </form>

          <?php if (empty($summaryData)): ?>
            <p>No data.</p>
          <?php else: ?>
            <p>Total: <strong><?= number_format($summaryTotal, 2) ?></strong></p>
            <div class="chart-container">
              <canvas id="summaryChart"></canvas>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="card">
        <h2>Add Expense</h2>
        <form method="POST">
          <input type="hidden" name="add_expense" value="1">

          <label>Date & Time:</label>
          <div style="display:flex; gap:1rem; align-items:center;">
            <label>
              <input type="radio" name="date_option" id="dateNow" value="now" checked>
              Now
            </label>
            <span id="nowDisplay" style="font-weight:600;"></span>
          </div>
          <div style="display:flex; gap:1rem; align-items:center;">
            <label>
              <input type="radio" name="date_option" id="dateCustom" value="custom">
            </label>
            <input type="date" name="expense_date" id="expense_date" disabled required>
            <input type="time" name="expense_time" id="expense_time" step="60" disabled required>
          </div>

          <label>
            Category:
            <select name="category_id" required>
  <option value="" selected disabled>Select a category</option>
  <?php foreach ($categories as $c): ?>
    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
  <?php endforeach; ?>
</select>

          </label>

          <label>
            Amount:
            <input type="number" name="amount" step="0.01" required>
          </label>

          <label>
            Description:
            <input type="text" name="description">
          </label>

          <div class="form-actions">
            <button type="submit" class="btn">Add Now</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const nowRadio    = document.getElementById('dateNow');
      const customRadio = document.getElementById('dateCustom');
      const dateIn      = document.getElementById('expense_date');
      const timeIn      = document.getElementById('expense_time');
      const nowDisp     = document.getElementById('nowDisplay');

      const pad = n => n.toString().padStart(2,'0');
      function updateNow() {
        const d = new Date();
        nowDisp.textContent = 
          `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} `
          + `${pad(d.getHours())}:${pad(d.getMinutes())}`;
      }
      function toggle() {
        const custom = customRadio.checked;
        dateIn.disabled = !custom;
        timeIn.disabled = !custom;
        if (!custom) updateNow();
      }

      updateNow();
      setInterval(updateNow, 60000);
      nowRadio.addEventListener('change', toggle);
      customRadio.addEventListener('change', toggle);
    });

    <?php if (!empty($summaryData)): ?>
    const ctx = document.getElementById('summaryChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode(array_column($summaryData, 'category_name')) ?>,
        datasets: [{
          label: '<?= ucfirst($summaryType) ?> Breakdown',
          data: <?= json_encode(array_map(fn($c) => (float)$c['total'], $summaryData)) ?>,
          backgroundColor: 'rgba(0,123,255,0.5)',
          borderColor: 'rgba(0,123,255,1)',
          borderWidth: 1
        }]
      },
      options: {
        scales: { y: { beginAtZero: true } }
      }
    });
    <?php endif; ?>
  </script>
</body>
</html>
