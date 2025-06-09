<?php
require_once __DIR__ . '/db.php';

/**
 * no filter all expense
 */
function getExpenses(int $user_id): array
{
    global $pdo;
    $stmt = $pdo->prepare(
        'SELECT e.*, c.name AS category_name
           FROM expenses e
           LEFT JOIN categories c ON e.category_id = c.id
          WHERE e.user_id = ?
          ORDER BY e.expense_date DESC'
    );
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * all expense month + year
 */
function getExpensesByMonth(int $user_id, int $year, int $month): array
{
    global $pdo;
    $stmt = $pdo->prepare(
        'SELECT e.*, c.name AS category_name
           FROM expenses e
           LEFT JOIN categories c ON e.category_id = c.id
          WHERE e.user_id   = :uid
            AND YEAR(e.expense_date)  = :year
            AND MONTH(e.expense_date) = :month
          ORDER BY e.expense_date DESC'
    );
    $stmt->execute([
        ':uid'   => $user_id,
        ':year'  => $year,
        ':month' => $month,
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * specific year expense
 */
function getExpensesByYear(int $user_id, int $year): array
{
    global $pdo;
    $stmt = $pdo->prepare(
        'SELECT e.*, c.name AS category_name
           FROM expenses e
           LEFT JOIN categories c ON e.category_id = c.id
          WHERE e.user_id       = :uid
            AND YEAR(e.expense_date) = :year
          ORDER BY e.expense_date DESC'
    );
    $stmt->execute([
        ':uid'  => $user_id,
        ':year' => $year,
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * single expense search
 */
function getExpense(int $id, int $user_id): ?array
{
    global $pdo;
    $stmt = $pdo->prepare(
        'SELECT * FROM expenses
          WHERE id = ? AND user_id = ?'
    );
    $stmt->execute([$id, $user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/**
 * insert expense
 */
function addExpense(int $user_id, int $category_id, float $amount, string $description, string $expense_date): bool
{
    global $pdo;
    $stmt = $pdo->prepare(
        'INSERT INTO expenses (user_id, category_id, amount, description, expense_date)
         VALUES (?, ?, ?, ?, ?)'
    );
    return $stmt->execute([$user_id, $category_id, $amount, $description, $expense_date]);
}

/**
 * update expense
 */
function updateExpense(int $id, int $user_id, int $category_id, float $amount, string $description, string $expense_date): bool
{
    global $pdo;
    $stmt = $pdo->prepare(
        'UPDATE expenses
            SET category_id = ?, amount = ?, description = ?, expense_date = ?
          WHERE id = ? AND user_id = ?'
    );
    return $stmt->execute([$category_id, $amount, $description, $expense_date, $id, $user_id]);
}

/**
 * delete
 */
function deleteExpense(int $id, int $user_id): bool
{
    global $pdo;
    $stmt = $pdo->prepare(
        'DELETE FROM expenses
          WHERE id = ? AND user_id = ?'
    );
    return $stmt->execute([$id, $user_id]);
}

/**
 * get category
 */
function getCategories(): array
{
    global $pdo;
    $stmt = $pdo->query(
        'SELECT * FROM categories ORDER BY name'
    );
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * add category
 */
function addCategory(string $name): bool
{
    global $pdo;
    $stmt = $pdo->prepare(
        'INSERT INTO categories (name) VALUES (?)'
    );
    return $stmt->execute([$name]);
}

/**
 * update category
 */
function updateCategory(int $id, string $name): bool
{
    global $pdo;
    $stmt = $pdo->prepare(
        'UPDATE categories SET name = ? WHERE id = ?'
    );
    return $stmt->execute([$name, $id]);
}

/**
 * delete category
 */
function deleteCategory(int $id): bool
{
    global $pdo;
    $stmt = $pdo->prepare(
        'DELETE FROM categories WHERE id = ?'
    );
    return $stmt->execute([$id]);
}

/**
 * find what year there are expenses thing
 */
function getExpenseYears(int $user_id): array
{
    global $pdo;
    $stmt = $pdo->prepare(
        'SELECT DISTINCT YEAR(expense_date) AS year
           FROM expenses
          WHERE user_id = :uid
          ORDER BY year DESC'
    );
    $stmt->execute([':uid' => $user_id]);
    return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'year');
}

/**
 * total category 
 */
function getCategoryTotalsByYear(int $user_id, int $year): array
{
    global $pdo;
    $stmt = $pdo->prepare(
        'SELECT c.name AS category_name, SUM(e.amount) AS total
           FROM expenses e
           JOIN categories c ON e.category_id = c.id
          WHERE e.user_id = :uid
            AND YEAR(e.expense_date) = :year
          GROUP BY e.category_id, c.name
          ORDER BY total DESC'
    );
    $stmt->execute([
        ':uid'  => $user_id,
        ':year' => $year,
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * total category spent
 */
function getCategoryTotalsByMonth(int $user_id, int $year, int $month): array
{
    global $pdo;
    $stmt = $pdo->prepare(
        'SELECT c.name AS category_name, SUM(e.amount) AS total
           FROM expenses e
           JOIN categories c ON e.category_id = c.id
          WHERE e.user_id   = :uid
            AND YEAR(e.expense_date)  = :year
            AND MONTH(e.expense_date) = :month
          GROUP BY e.category_id, c.name
          ORDER BY total DESC'
    );
    $stmt->execute([
        ':uid'   => $user_id,
        ':year'  => $year,
        ':month' => $month,
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * total spent per month in a year
 */
function getMonthlyTotalsByYear(int $user_id, int $year): array
{
    global $pdo;
    $stmt = $pdo->prepare(
        'SELECT MONTH(expense_date) AS month, SUM(amount) AS total
           FROM expenses
          WHERE user_id = :uid
            AND YEAR(expense_date) = :year
          GROUP BY MONTH(expense_date)
          ORDER BY MONTH(expense_date)'
    );
    $stmt->execute([
        ':uid'  => $user_id,
        ':year' => $year,
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}