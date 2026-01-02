<?php
session_start();

// ✅ Database Connection (consistent with other pages)
$dsn = 'mysql:host=localhost;dbname=discussion_app;charset=utf8mb4';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die('Database Connection Failed: ' . $e->getMessage());
}

// ✅ Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: log.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$role = strtolower($_SESSION['role'] ?? '');
$message = "";
$pass_message = "";

// ✅ Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("❌ User not found in database.");
}

// ✅ Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $profile_pic = $user['profile_pic'] ?? "default.png";

    // Handle image upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $targetDir = __DIR__ . "/uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES['profile_pic']['name']);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFilePath)) {
            $profile_pic = $fileName;
        }
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, profile_pic = ? WHERE id = ?");
    $stmt->execute([$name, $email, $profile_pic, $user_id]);
    $message = "✅ Profile updated successfully!";

    // Refresh user info
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// ✅ Handle password update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password && strlen($new_password) >= 6) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $user_id]);
            $pass_message = "✅ Password updated successfully!";
        } else {
            $pass_message = "⚠️ New passwords do not match or are too short (min 6 chars).";
        }
    } else {
        $pass_message = "❌ Current password is incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 950px; }
        .profile-card {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .profile-pic {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #007bff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="profile-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>👤 <?= htmlspecialchars(ucfirst($role)) ?> Profile</h2>
            <a href="index7.php" class="btn btn-secondary">🏠 Home</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($pass_message): ?>
            <div class="alert alert-info"><?= htmlspecialchars($pass_message) ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Left: Profile Info -->
            <div class="col-md-6">
                <img src="uploads/<?= htmlspecialchars($user['profile_pic'] ?? 'default.png') ?>"
                     class="profile-pic mb-3" alt="Profile Picture">

                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="update_profile" value="1">

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" 
                               value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Profile Picture</label>
                        <input type="file" name="profile_pic" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>

            <!-- Right: Password Change -->
            <div class="col-md-6">
                <h4>Change Password</h4>
                <form method="post">
                    <input type="hidden" name="update_password" value="1">

                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-warning">Update Password</button>
                </form>

                <hr>
                <!-- ✅ Role-Based Dashboard Redirect -->
                <h5>Go to Dashboard</h5>
                <?php
                switch ($role) {
                    case 'admin':
                        $dash = 'admin_dashboard.php';
                        break;
                    case 'lecturer':
                        $dash = 'lecturer_dashboard.php';
                        break;
                    case 'mentor':
                        $dash = 'mentor_dashboard.php';
                        break;
                    case 'student':
                        $dash = 'student_dashboard.php';
                        break;
                    default:
                        $dash = 'dashboard.php';
                }
                ?>
                <a href="<?= $dash ?>" class="btn btn-success mt-2">📊 Go to Dashboard</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
