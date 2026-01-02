<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $remember = isset($_POST['remember']);
    $role = $_POST['role'] ?? 'student'; // Default to student
    $profile_pic = null;

    if (!$name || !$email || !$phone || !$password || !$confirmPassword) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    } elseif ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    } else {
        // Handle optional profile picture upload
        if (!empty($_FILES['profile_pic']['name'])) {
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $file_name = time() . "_" . basename($_FILES["profile_pic"]["name"]);
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                $profile_pic = $target_file;
            }
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, profile_pic) 
                               VALUES (:name, :email, :phone, :password, :role, :profile_pic)");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':password' => $hashedPassword,
            ':role' => $role,
            ':profile_pic' => $profile_pic
        ]);

        // Remember email
        if ($remember) {
            setcookie('remember_email', $email, time() + (86400 * 30), "/");
        }

        header("Location: log.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('fb.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .form-container {
            max-width: 700px;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            margin: 60px auto;
            border-radius: 12px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.3);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
            color: mediumblue;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>COMPUTER SCIENCE <br> ONLINE DISCUSSION FORUM</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?= implode("<br>", $errors) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" name="name" class="form-control" placeholder="Full Name" required>
            </div>
            <div class="col-md-6">
                <input type="email" name="email" class="form-control" placeholder="Email Address" required 
                       value="<?= htmlspecialchars($_COOKIE['remember_email'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <input type="text" name="phone" class="form-control" placeholder="Phone Number" required>
            </div>
            <div class="col-md-6">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="col-md-6">
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
            </div>

            <!-- Role Selection -->
            <div class="col-md-6">
                <select name="role" class="form-control" required>
                    <option value="">-- Select Role --</option>
                    <option value="student">Student</option>
                    <option value="lecturer">Lecturer</option>
                    <option value="mentor">Mentor</option>
                </select>
            </div>

            <!-- Optional Profile Picture -->
            <div class="col-md-12">
                <input type="file" name="profile_pic" class="form-control" accept="image/*">
            </div>

            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                           <?= isset($_COOKIE['remember_email']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="remember">Remember Me</label>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <a href="log.php" class="btn btn-link">Already have an account?</a>
            <button type="submit" class="btn btn-primary px-4">Register</button>
        </div>
    </form>
</div>

</body>
</html>
