<?php
session_start();
require __DIR__ . '/config/db.php'; // ✅ database connection
require __DIR__ . '/config/mailer.php'; // ✅ to send email later if needed

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ List of available topics
$topics = [
    "HTML", "CSS", "Bootstrap", "JavaScript",
    "Python", "C++", "C#", "Software Installation",
    "Windows Installation", "Software Issue",
    "Apps Issue", "Hardware Issue"
];

// ✅ If user submits preferences
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = $_POST['topics'] ?? [];

    // Clear old subscriptions
    $stmt = $pdo->prepare("DELETE FROM subscriptions WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Insert new subscriptions
    $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, topic) VALUES (?, ?)");
    foreach ($selected as $topic) {
        $stmt->execute([$user_id, $topic]);
    }

    $message = "Your subscriptions have been updated successfully!";
}

// ✅ Fetch current subscriptions
$stmt = $pdo->prepare("SELECT topic FROM subscriptions WHERE user_id = ?");
$stmt->execute([$user_id]);
$current_subs = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Topic Subscriptions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>🔔 Email Notification Subscriptions</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow">
        <p>Select the topics you want to be notified about:</p>

        <?php foreach ($topics as $topic): ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="topics[]" value="<?= $topic ?>"
                    <?= in_array($topic, $current_subs) ? 'checked' : '' ?>>
                <label class="form-check-label"><?= $topic ?></label>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-primary mt-3">Save Preferences</button>
    </form>
</div>
</body>
</html>
