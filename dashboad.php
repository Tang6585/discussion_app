<?php
session_start();
require 'db.php';

// Protect route
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Count topics, replies, likes, retweets
$total_topics = $pdo->query("SELECT COUNT(*) FROM topics")->fetchColumn();
$total_replies = $pdo->query("SELECT COUNT(*) FROM replies")->fetchColumn();
$total_likes = $pdo->query("SELECT COUNT(*) FROM likes")->fetchColumn();
$total_retweets = $pdo->query("SELECT COUNT(*) FROM retweets")->fetchColumn();

// Users stats
$users_stmt = $pdo->query("
    SELECT u.id, u.name, u.email, u.role,
           (SELECT COUNT(*) FROM topics t WHERE t.user_id = u.id) AS topics_count,
           (SELECT COUNT(*) FROM replies r WHERE r.user_id = u.id) AS replies_count,
           (SELECT COUNT(*) FROM likes l WHERE l.user_id = u.id) AS likes_count
    FROM users u
");
$users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .stat-card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        text-align: center;
        padding: 20px;
    }
    .stat-card h2 {
        margin: 0;
        font-size: 2.2rem;
        color: #007bff;
    }
    .stat-card p {
        margin: 0;
        font-size: 1.1rem;
        color: #555;
    }
  </style>
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2 class="mb-4">👑 Admin Dashboard</h2>

  <div class="row mb-4">
    <div class="col-md-3">
      <div class="stat-card bg-white">
        <p>Topics</p>
        <h2><?= $total_topics ?></h2>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card bg-white">
        <p>Replies</p>
        <h2><?= $total_replies ?></h2>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card bg-white">
        <p>Likes</p>
        <h2><?= $total_likes ?></h2>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card bg-white">
        <p>Retweets</p>
        <h2><?= $total_retweets ?></h2>
      </div>
    </div>
  </div>

  <h4 class="mb-3">👥 Users Statistics</h4>
  <div class="table-responsive bg-white p-3 rounded shadow-sm">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Topics</th>
          <th>Replies</th>
          <th>Likes</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td><?= $u['topics_count'] ?></td>
            <td><?= $u['replies_count'] ?></td>
            <td><?= $u['likes_count'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
