<?php
session_start();
require 'db.php';

// only lecturers
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role'] ?? '') !== 'lecturer') {
    header("Location: log.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Helper functions
function safeCount(PDO $pdo, string $sql, array $params = []): int {
    try { $stmt = $pdo->prepare($sql); $stmt->execute($params); return (int) $stmt->fetchColumn(); }
    catch (PDOException $e) { return 0; }
}
function tableExists(PDO $pdo, string $tableName): bool {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
        $stmt->execute([$tableName]);
        return (bool)$stmt->fetchColumn();
    } catch (Exception $e) { return false; }
}

$discussionTable = tableExists($pdo, 'discussions') ? 'discussions' : (tableExists($pdo, 'topics') ? 'topics' : null);
$replyTable = tableExists($pdo, 'replies') ? 'replies' : null;
$likesTable = tableExists($pdo, 'likes') ? 'likes' : null;

$total_discussions = $discussionTable ? safeCount($pdo, "SELECT COUNT(*) FROM `$discussionTable` WHERE user_id = ?", [$user_id]) : 0;
$total_replies     = $replyTable ? safeCount($pdo, "SELECT COUNT(*) FROM `$replyTable` WHERE user_id = ?", [$user_id]) : 0;
$total_likes       = $likesTable ? safeCount($pdo, "SELECT COUNT(*) FROM `$likesTable` WHERE user_id = ?", [$user_id]) : 0;

$recent = [];
if ($discussionTable) {
    try {
        $stmt = $pdo->prepare("SELECT id, topic, statement, created_at FROM `$discussionTable` WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$user_id]);
        $recent = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lecturer Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f8f9fa; font-family: 'Segoe UI', Arial; }
.navbar { background-color: #3F51B5; box-shadow: 0 4px 12px rgba(63,81,181,0.25); }
.navbar-brand { color: #FFD700 !important; font-weight: 700; }
.stat-card { border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.06); padding: 22px; }
.stat-number { font-size: 2rem; font-weight: 700; }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="index7.php">🏠 Back to Home</a>
    <div class="ms-auto text-white">
      👤 <?= htmlspecialchars($_SESSION['name']) ?> | Lecturer
    </div>
  </div>
</nav>

<div class="container mt-4 mb-5">
  <h2>📘 Lecturer Dashboard</h2>
  <p class="text-muted">View your teaching-related discussions and student engagement.</p>

  <div class="row mt-4 g-3">
    <div class="col-md-4"><div class="stat-card bg-white"><div>🗣️ Discussions</div><div class="stat-number text-primary"><?= $total_discussions ?></div></div></div>
    <div class="col-md-4"><div class="stat-card bg-white"><div>💬 Replies</div><div class="stat-number text-success"><?= $total_replies ?></div></div></div>
    <div class="col-md-4"><div class="stat-card bg-white"><div>❤️ Likes</div><div class="stat-number text-danger"><?= $total_likes ?></div></div></div>
  </div>

  <div class="card mt-4">
    <div class="card-body">
      <h5>🧑‍🏫 Your Recent Topics</h5>
      <?php if ($recent): ?>
        <ul class="list-group list-group-flush">
          <?php foreach ($recent as $r): ?>
          <li class="list-group-item">
            <strong><?= htmlspecialchars($r['topic']) ?></strong><br>
            <small class="text-muted"><?= date('Y-m-d H:i', strtotime($r['created_at'] ?? '')) ?></small><br>
            <?= nl2br(htmlspecialchars(substr($r['statement'],0,150))) ?>...
          </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-muted">No recent discussions yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
