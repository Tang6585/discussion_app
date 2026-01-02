<?php
session_start();
require_once 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required.";
    } else {
        // Fetch user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $errors[] = "Invalid email or password.";
        } else {
            $isAdmin = strtolower($user['role']) === 'admin';
            $adminHash = hash('sha256', 'Tango360');

            $isPasswordValid = $isAdmin
                ? ($adminHash === $user['password'])
                : password_verify($password, $user['password']);

            if ($isPasswordValid) {
                // Store session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = strtolower($user['role']);
                $_SESSION['profile_pic'] = $user['profile_pic'] ?? 'default.png';

                // ✅ Redirect ALL users to index7.php (Home Page)
                header("Location: index7.php");
                exit;
            } else {
                $errors[] = "Invalid email or password.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Discussion Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('fb.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
        }
        .form-container {
            max-width: 480px;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            margin: 80px auto;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
            color: mediumblue;
        }
        .btn-primary {
            background-color: mediumblue;
            border: none;
        }
        .btn-primary:hover {
            background-color: darkblue;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>LOGIN TO DISCUSSION FORUM</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><?= implode("<br>", $errors) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <input type="text" name="email" class="form-control" placeholder="Email Address" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="text-end">
            <a href="register.php" class="btn btn-link">Create Account</a>
            <button type="submit" class="btn btn-primary px-4">Login</button>
        </div>
    </form>
</div>

</body>
</html>
