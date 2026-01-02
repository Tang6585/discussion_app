<?php
session_start();
require_once 'db.php'; // Use your existing DB connection file

$errors = [];
$name = $email = $phone = $password = $confirmPassword = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $remember = isset($_POST['remember']);

    // Validate input
    if (!$name) $errors[] = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (!preg_match('/^\d{10,15}$/', $phone)) $errors[] = "Phone number must be 10-15 digits.";
    if (!$password) $errors[] = "Password is required.";
    if ($password !== $confirmPassword) $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email is already registered.";
        } else {
            // Hash the password and save user
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $passwordHash]);

            // Log the user in
            $_SESSION['user'] = [
                'id' => $pdo->lastInsertId(),
                'name' => $name,
                'email' => $email,
            ];

            // Optional: set a cookie if remember me is checked
            if ($remember) {
                setcookie('user_email', $email, time() + (86400 * 30), "/"); // 30 days
            }

            // Redirect to discussion app
            header("Location: discussion_app.php");
            exit;
        }
    }
}
?>
