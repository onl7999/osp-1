<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../fn/functions.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id     = $_SESSION['user_id'];
    $category_id = (int) $_POST['category_id'];
    $amount      = trim($_POST['amount']);
    $description = trim($_POST['description']);

    if (isset($_POST['date_option']) && $_POST['date_option'] === 'custom') {
        // User picked a date; append midnight time
        $expense_date = $_POST['expense_date'] . ' 00:00:00';
    } else {
        $expense_date = date('Y-m-d H:i:s');
    }

    if (addExpense($user_id, $category_id, $amount, $description, $expense_date)) {
        header('Location: index.php');
        exit;
    } else {
        $message = 'Failed to add expense. Please try again.';
    }
}

$cats = getCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Expense</title>
  <link rel="stylesheet" href="css/style.css" type="text/css" />
</head>
<body>
  <div class="container">
    <h1>Add Expense</h1>

    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" action="create.php">
      <div class="mb-1">
        <label>Date:</label><br>

        <label for="dateNow">
          <input type="radio" name="date_option" id="dateNow" value="now" checked>
          Now
        </label>
        <span id="nowDisplay" class="text-center mb-1"></span><br>

        <label for="dateCustom">
          <input type="radio" name="date_option" id="dateCustom" value="custom">
          Custom
        </label><br>

        <input
          type="date"
          name="expense_date"
          id="expense_date"
          disabled
        >
      </div>

      <div class="mb-1">
        <label>
          Category:
          <select name="category_id" required>
            <?php foreach ($cats as $c): ?>
              <option value="<?= $c['id'] ?>">
                <?= htmlspecialchars($c['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>
      </div>

      <div class="mb-1">
        <label>
          Amount:
          <input type="number" name="amount" step="0.01" required>
        </label>
      </div>

      <div class="mb-1">
        <label>
          Description:
          <textarea name="description"></textarea>
        </label>
      </div>

      <button type="submit" class="btn">Add Expense</button>
    </form>

    <p><a href="portal.php" class="btn">Back to Dashboard</a></p>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const nowRadio    = document.getElementById('dateNow');
      const customRadio = document.getElementById('dateCustom');
      const dateInput   = document.getElementById('expense_date');
      const nowDisplay  = document.getElementById('nowDisplay');

      function formatTimestamp(date) {
        const pad = n => n.toString().padStart(2, '0');
        return date.getFullYear() + '-' +
               pad(date.getMonth()+1) + '-' +
               pad(date.getDate()) + ' ' +
               pad(date.getHours()) + ':' +
               pad(date.getMinutes()) + ':' +
               pad(date.getSeconds());
      }

      function updateNowDisplay() {
        const now = new Date();
        nowDisplay.textContent = formatTimestamp(now);
      }

      function toggleDateInput() {
        if (customRadio.checked) {
          dateInput.disabled = false;
          dateInput.required = true;
          nowDisplay.textContent = '';
        } else {
          dateInput.disabled = true;
          dateInput.required = false;
          updateNowDisplay();
        }
      }

      updateNowDisplay();
      setInterval(updateNowDisplay, 1000); // live clock
      nowRadio.addEventListener('change', toggleDateInput);
      customRadio.addEventListener('change', toggleDateInput);
    });
  </script>
</body>
</html>
