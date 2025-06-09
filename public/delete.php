<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once __DIR__ . '/../fn/functions.php';

$id = $_GET['id'] ?? null;
deleteExpense($id, $_SESSION['user_id']);
header('Location: index.php'); exit;
