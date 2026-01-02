<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $remember = isset($_POST['remember']);

    if (!$name || !$email || !$phone || !$password || !$confirmPassword) {
        echo "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address.";
    } elseif ($password !== $confirmPassword) {
        echo "Passwords do not match.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (:name, :email, :phone, :password)");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':password' => $hashedPassword
        ]);

        if ($remember) {
            setcookie('remember_email', $email, time() + (86400 * 30), "/");
        }

        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
<h2>Registration Form</h2>
<form method="POST">
    <label>Name:</label><br>
    <input type="text" name="name" required><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br>

    <label>Phone:</label><br>
    <input type="text" name="phone" required><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br>

    <label>Confirm Password:</label><br>
    <input type="password" name="confirm_password" required><br>

    <label>
        <input type="checkbox" name="remember"> Remember Me
    </label><br><br>

    <button type="submit">Register</button>
</form>
</body>
</html>
