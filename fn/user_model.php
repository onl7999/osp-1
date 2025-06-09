<?php
require_once __DIR__ . '/db.php';

/**
 * check doubled user
 *
 * @param string $username
 * @return array|false
 */
function findUserByUsername(string $username)
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * give new user id after signing up for user
 *
 * @param string $username
 * @param string $password
 * @return int  The new user’s ID.
 */
function registerUser(string $username, string $password): int
{
    global $pdo;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
    $stmt->execute([$username, $hash]);
    return (int)$pdo->lastInsertId();
}

/**
 * 
 *
 * @param string $username
 * @param string $password
 * @return array|false
 */
function verifyUser(string $username, string $password)
{
    $user = findUserByUsername($username);
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}
