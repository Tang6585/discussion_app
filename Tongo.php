<?php
// db.php - Database Connection
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

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'new_discussion') {
        $topic = trim($_POST['topic'] ?? '');
        $statement = trim($_POST['statement'] ?? '');

        if ($topic && $statement) {
            $stmt = $pdo->prepare("INSERT INTO discussions (topic, statement) VALUES (:topic, :statement)");
            $stmt->execute([
                ':topic' => $topic,
                ':statement' => $statement,
            ]);
        }
    }

    if (isset($_POST['discussion_id'])) {
        $discussionId = (int)$_POST['discussion_id'];

        if ($action === 'like') {
            $pdo->exec("UPDATE discussions SET likes = likes + 1 WHERE id = $discussionId");
        } elseif ($action === 'retweet') {
            $pdo->exec("UPDATE discussions SET retweets = retweets + 1 WHERE id = $discussionId");
        } elseif ($action === 'reply') {
            $replyText = trim($_POST['reply_text'] ?? '');
            if ($replyText) {
                $stmt = $pdo->prepare("INSERT INTO replies (discussion_id, reply_text) VALUES (:discussion_id, :reply_text)");
                $stmt->execute([
                    ':discussion_id' => $discussionId,
                    ':reply_text' => $replyText,
                ]);
            }
        }
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Fetch Topics
$topics = $pdo->query("SELECT DISTINCT topic FROM discussions")->fetchAll(PDO::FETCH_COLUMN);

// Fetch Discussions by Topic
$selectedTopic = $_GET['topic'] ?? ($topics[0] ?? '');
$discussions = [];
if ($selectedTopic) {
    $stmt = $pdo->prepare("SELECT * FROM discussions WHERE topic = :topic ORDER BY id DESC");
    $stmt->execute([':topic' => $selectedTopic]);
    $discussions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchReplies(PDO $pdo, int $discussionId): array {
    $stmt = $pdo->prepare("SELECT reply_text, created_at FROM replies WHERE discussion_id = :discussion_id ORDER BY id DESC");
    $stmt->execute([':discussion_id' => $discussionId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function timeAgo($datetime): string {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) return $diff . " seconds ago";
    if ($diff < 3600) return floor($diff / 60) . " minutes ago";
    if ($diff < 86400) return floor($diff / 3600) . " hours ago";
    if ($diff < 604800) return floor($diff / 86400) . " days ago";
    return date('Y-m-d H:i', $time);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modern Discussion App</title>
    <link rel="stylesheet" href="bootstrap-5.0.2/css/bootstrap.min.css">
    <style>
        body {
            background: url('fb.jpg') no-repeat center center fixed;
            background-size: cover;
            backdrop-filter: blur(3px);
            color: #212529;
            font-family: 'Segoe UI', sans-serif;
        }
        .main-wrapper {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.25);
        }
        .navbar {
            background-color: #1E2A38 !important;
        }
        .navbar-brand {
            font-weight: bold;
            color: #FFD700 !important;
        }
        .interaction-buttons button {
            background: none;
            border: none;
            padding: 0;
            color: #6c757d;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 1rem;
            transition: color 0.2s;
        }
        .interaction-buttons button:hover {
            color: #007BFF;
        }
        .reply-form {
            margin-top: 10px;
        }
        .badge-timestamp {
            font-size: 0.75rem;
            background-color: #f8f9fa;
            color: #6c757d;
            padding: 0.3em 0.6em;
            border-radius: 0.25rem;
        }
        .card-title {
            color: #1E2A38;
        }
        .card-body p {
            color: #333;
        }
    </style>
</head>
<body>

<div class="container py-4 main-wrapper">
    <nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4 rounded">
        <div class="container-fluid">
            <a class="navbar-brand mx-auto text-center" href="#">🗣️ Welcome to the Computer Science online Discussion Forum!</a>
        </div>
    </nav>

    <!-- Topics Navigation -->
    <ul class="nav nav-pills mb-4">
        <?php foreach ($topics as $topic): ?>
            <li class="nav-item">
                <a class="nav-link <?= $topic === $selectedTopic ? 'active' : '' ?>" href="?topic=<?= urlencode($topic) ?>">
                    <?= htmlspecialchars($topic) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- New Discussion Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Start a New Discussion</h5>
            <form method="POST" class="row g-3">
                <input type="hidden" name="action" value="new_discussion">
                <div class="col-12">
                    <input type="text" name="topic" class="form-control" placeholder="Topic" required>
                </div>
                <div class="col-12">
                    <textarea name="statement" class="form-control" placeholder="Statement" rows="3" required></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success">Post Discussion</button>
                </div>
            </form>
        </div>
    </div>

    <h4 class="mb-3">🧵 Discussions on: <span class="text-primary"><?= htmlspecialchars($selectedTopic) ?></span></h4>

    <?php foreach ($discussions as $discussion): ?>
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h5><?= htmlspecialchars($discussion['topic']) ?></h5>
                <p><?= nl2br(htmlspecialchars($discussion['statement'])) ?></p>
                <span class="badge badge-timestamp mb-2">Posted: <?= timeAgo($discussion['created_at'] ?? 'now') ?></span>

                <div class="interaction-buttons d-flex justify-content-start gap-4 my-2">

                    <!-- Like -->
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="discussion_id" value="<?= $discussion['id'] ?>">
                        <input type="hidden" name="action" value="like">
                        <button type="submit">❤️ <span><?= $discussion['likes'] ?></span></button>
                    </form>

                    <!-- Retweet -->
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="discussion_id" value="<?= $discussion['id'] ?>">
                        <input type="hidden" name="action" value="retweet">
                        <button type="submit">🔁 <span><?= $discussion['retweets'] ?></span></button>
                    </form>

                    <!-- Reply -->
                    <button type="button" data-bs-toggle="collapse" data-bs-target="#replyForm<?= $discussion['id'] ?>">
                        💬 Reply
                    </button>

                </div>

                <!-- Reply Form -->
                <div class="collapse reply-form" id="replyForm<?= $discussion['id'] ?>">
                    <form method="POST" class="mt-2">
                        <input type="hidden" name="discussion_id" value="<?= $discussion['id'] ?>">
                        <input type="hidden" name="action" value="reply">
                        <div class="input-group">
                            <input type="text" name="reply_text" class="form-control" placeholder="Write a reply..." required>
                            <button class="btn btn-outline-success" type="submit">Reply</button>
                        </div>
                    </form>
                </div>

                <!-- Replies -->
                <?php foreach (fetchReplies($pdo, $discussion['id']) as $reply): ?>
                    <div class="alert alert-light border p-2 my-1">
                        <?= htmlspecialchars($reply['reply_text']) ?>
                        <div class="text-end text-muted" style="font-size: 0.75rem;">
                            <?= timeAgo($reply['created_at'] ?? 'now') ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($discussions)): ?>
        <div class="alert alert-warning">No discussions available for this topic yet.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
