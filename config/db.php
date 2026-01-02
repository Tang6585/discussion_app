<?php
// config/db.php
$dsn = 'mysql:host=localhost;dbname=discussion_item;charset=utf8mb4';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die('Database Connection Failed: ' . $e->getMessage());
}
