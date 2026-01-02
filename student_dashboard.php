<?php
session_start();
require 'db.php'; // adjust path if your db.php is elsewhere

// only allow logged-in students
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role'] ?? '') !== 'student') {
    header("Location: log.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

/**
 * Safe helper to run a COUNT(*) query and return integer 0 on error.
 */
function safeCount(PDO $pdo, string $sql, array $params = []): int {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

/**
 * Check whether a table exists in the current database.
 */
function tableExists(PDO $pdo, string $tableName): bool {
    try {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM information_schema.tables 
             WHERE table_schema = DATABASE() AND table_name = ?"
        );
        $stmt->execute([$tableName]);
        return (bool) $stmt->fetchColumn();
    } catch (Exception $e) {
        return false;
    }
}

// Resolve table names (fall back if some names differ)
$discussionTable = tableExists($pdo, 'discussions') ? 'discussions' : (tableExists($pdo, 'topics') ? 'topics' : null);
$replyTable      = tableExists($pdo, 'replies') ? 'replies' : null;
$likesTable      = tableExists($pdo, 'likes') ? 'likes' : null;

// Get counts safely
$total_discussions = $discussionTable ? safeCount($pdo, "SELECT COUNT(*) FROM `$discussionTable` WHERE user_id = ?", [$user_id]) : 0;
$total_replies     = $replyTable      ? safeCount($pdo, "SELECT COUNT(*) FROM `$replyTable` WHERE user_id = ?", [$user_id]) : 0;
$total_likes       = $likesTable      ? safeCount($pdo, "SELECT COUNT(*) FROM `$likesTable` WHERE user_id = ?", [$user_id]) : 0;

// Last 5 discussions by the student (if table exists)
$recentDiscussions = [];
if ($discussionTable) {
    try {
        $stmt = $pdo->prepare("SELECT id, topic, statement, created_at FROM `$discussionTable` WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$user_id]);
        $recentDiscussions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $recentDiscussions = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Student Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; font-family: "Segoe UI", Arial, sans-serif; }
    .navbar { background-color: #3F51B5; box-shadow: 0 4px 12px rgba(63,81,181,0.25); }
    .navbar-brand { color: #FFD700 !important; font-weight: 700; }
    .stat-card { border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.06); padding: 22px; }
    .stat-number { font-size: 2rem; font-weight: 700; }
    .recent-card { border-radius: 10px; }
  </style>
</head>
<body>
  <!-- Navbar with back-to-home -->
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="index7.php">🏠 Back to Home</a>
      <div class="ms-auto text-white">
        👤 <?= htmlspecialchars($_SESSION['name'] ?? 'Student') ?> &nbsp; | &nbsp; Student
      </div>
    </div>
  </nav>

  <div class="container mt-4 mb-5">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h2 class="mb-0">🎓 Student Dashboard</h2>
        <p class="text-muted">Welcome back — here is a quick summary of your activity.</p>
      </div>
      <div class="col-md-4 text-md-end">
        <a href="profile.php" class="btn btn-outline-primary">View Profile</a>
      </div>
    </div>

    <div class="row mt-4 g-3">
      <div class="col-md-4">
        <div class="stat-card bg-white">
          <div class="text-muted">🗣️ Discussions</div>
          <div class="stat-number text-primary"><?= $total_discussions ?></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card bg-white">
          <div class="text-muted">💬 Replies</div>
          <div class="stat-number text-success"><?= $total_replies ?></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card bg-white">
          <div class="text-muted">❤️ Likes</div>
          <div class="stat-number text-danger"><?= $total_likes ?></div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <div class="card recent-card">
          <div class="card-body">
            <h5 class="card-title mb-3">Your recent discussions</h5>

            <?php if (!empty($recentDiscussions)): ?>
              <ul class="list-group list-group-flush">
                <?php foreach ($recentDiscussions as $d): ?>
                  <li class="list-group-item">
                    <strong><?= htmlspecialchars($d['topic']) ?></strong>
                    <div class="small text-muted"><?= date('Y-m-d H:i', strtotime($d['created_at'] ?? 'now')) ?></div>
                    <div class="mt-2"><?= nl2br(htmlspecialchars(substr($d['statement'], 0, 220))) ?><?= strlen($d['statement']) > 220 ? '...' : '' ?></div>
                    <div class="mt-2">
                      <a href="view_topic.php?id=<?= (int)$d['id'] ?>" class="btn btn-sm btn-outline-secondary">View</a>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p class="text-muted mb-0">You haven't started any discussions yet. <a href="index7.php#new-discussion">Post your first question</a>.</p>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- bootstrap bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
