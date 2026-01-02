<?php
require 'db.php';

$stmt = $pdo->query("SELECT id, name, email, password, role FROM users WHERE role='admin'");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($admins);
echo "</pre>";
?>
