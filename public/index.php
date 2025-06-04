<?php
require_once __DIR__ . '/../fn/db.php';
require_once __DIR__ . '/../fn/functions.php';

$month = $_GET['month'] ?? date('Y-m');      
$cat   = $_GET['cat']   ?? '';
$params = [':start' => "$month-01", ':end' => "$month-31"];

$sql = "SELECT e.*, c.name AS category
        FROM expenses e JOIN categories c ON c.id = e.category_id
        WHERE e.happened_on BETWEEN :start AND :end";
if ($cat !== '') {
    $sql .= " AND e.category_id = :cat";
    $params[':cat'] = $cat;
}
$sql .= " ORDER BY e.happened_on DESC, e.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$cats = $pdo->query('SELECT id,name FROM categories ORDER BY name')->fetchAll();

$total = array_sum(array_column($rows, 'amount')); //total amount of spending

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><title>Expense Log</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header><h1>Expense Log</h1></header>
<nav>
  <a class="btn" href="create.php">+ New Expense</a>
  <a class="btn" href="categories.php">Categories</a>
  <a class="btn" href="summary.php">Summary</a>
</nav>

<form method="get">
  <label>Month:
    <input type="month" name="month" value="<?=h($month)?>">
  </label>
  <label>Category:
    <select name="cat">
      <option value="">— All —</option>
      <?php foreach ($cats as $c): ?>
        <option value="<?=$c['id']?>" <?= $cat==$c['id']?'selected':'' ?>><?=h($c['name'])?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <button class="btn">Filter</button>
</form>

<h2>Total: NT$<?=number_format($total,2)?></h2>
<table class="table">
  <tr><th>Date</th><th>Category</th><th>Amount ($)</th><th>Note</th><th></th></tr>
  <?php foreach ($rows as $r): ?>
  <tr>
    <td><?=h($r['happened_on'])?></td>
    <td><?=h($r['category'])?></td>
    <td style="text-align:right;"><?=number_format($r['amount'],2)?></td>
    <td><?=h($r['note'])?></td>
    <td><a href="edit.php?id=<?= $r['id'] ?>">edit</a></td>
    </tr>
  <?php endforeach; ?>
</table>
</body>
</html>
