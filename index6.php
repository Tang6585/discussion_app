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
    $stmt = $pdo->prepare("SELECT * FROM discussions WHERE topic = :topic");
    $stmt->execute([':topic' => $selectedTopic]);
    $discussions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchReplies(PDO $pdo, int $discussionId): array {
    $stmt = $pdo->prepare("SELECT reply_text FROM replies WHERE discussion_id = :discussion_id ORDER BY id DESC");
    $stmt->execute([':discussion_id' => $discussionId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modern Discussion App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: url('fb.jpg') no-repeat center center fixed;
            background-size: cover;
            backdrop-filter: blur(3px);
            font-family: 'Segoe UI', sans-serif;
        }

        .main-wrapper {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.25);
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
    </style>
</head>
<body>
<div class="container py-4 main-wrapper">
    <nav class="navbar navbar-dark bg-dark mb-4 rounded shadow-sm">
        <div class="container justify-content-between">
            <span class="navbar-brand mb-0 h1 text-warning">
                🗣️ Computer Science Discussion Forum
            </span>
            <div class="form-check form-switch text-light">
                <input class="form-check-input" type="checkbox" id="darkModeToggle">
                <label class="form-check-label" for="darkModeToggle">Dark Mode</label>
            </div>
        </div>
    </nav>

    <ul class="nav nav-tabs mb-4">
        <?php foreach ($topics as $topic): ?>
            <li class="nav-item">
                <a class="nav-link <?= $topic === $selectedTopic ? 'active' : '' ?>" href="?topic=<?= urlencode($topic) ?>">
                    <?= htmlspecialchars($topic) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="card mb-4 shadow-sm fade-in">
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
        <div class="card mb-3 shadow-sm fade-in">
            <div class="card-body">
                <h5><?= htmlspecialchars($discussion['topic']) ?></h5>
                <p><?= nl2br(htmlspecialchars($discussion['statement'])) ?></p>
                <span class="badge bg-light text-dark mb-2">Posted at: <?= date('Y-m-d H:i', strtotime($discussion['created_at'] ?? 'now')) ?></span>

                <div class="interaction-buttons d-flex justify-content-start gap-4 my-2">
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="discussion_id" value="<?= $discussion['id'] ?>">
                        <input type="hidden" name="action" value="like">
                        <button type="submit"><i class="bi bi-hand-thumbs-up"></i> <?= $discussion['likes'] ?></button>
                    </form>

                    <form method="POST" class="d-inline">
                        <input type="hidden" name="discussion_id" value="<?= $discussion['id'] ?>">
                        <input type="hidden" name="action" value="retweet">
                        <button type="submit"><i class="bi bi-arrow-repeat"></i> <?= $discussion['retweets'] ?></button>
                    </form>

                    <button type="button" data-bs-toggle="collapse" data-bs-target="#replyForm<?= $discussion['id'] ?>">
                        <i class="bi bi-chat-dots"></i> Reply
                    </button>
                </div>

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

                <?php foreach (fetchReplies($pdo, $discussion['id']) as $reply): ?>
                    <div class="alert alert-light d-flex align-items-start gap-2 p-2">
                        <span class="badge bg-primary rounded-circle">👤</span>
                        <div>
                            <div><?= htmlspecialchars($reply) ?></div>
                            <small class="text-muted">Just now</small>
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
<script>
document.getElementById('darkModeToggle').addEventListener('change', function () {
    document.body.classList.toggle('bg-dark');
    document.body.classList.toggle('text-light');
    document.querySelectorAll('.card').forEach(card => {
        card.classList.toggle('bg-secondary');
        card.classList.toggle('text-light');
    });
});
</script>
</body>
</html>
