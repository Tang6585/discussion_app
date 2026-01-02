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
</head>
<body class="bg-light">

<div class="container py-4">
    <nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Discussion App</a>
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
    <div class="card mb-4">
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
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <h3>Discussions on: <?= htmlspecialchars($selectedTopic) ?></h3>

    <?php foreach ($discussions as $discussion): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5><?= htmlspecialchars($discussion['topic']) ?></h5>
                <p><?= nl2br(htmlspecialchars($discussion['statement'])) ?></p>

                <div class="d-flex gap-2 mb-2">
                    <form method="POST">
                        <input type="hidden" name="discussion_id" value="<?= $discussion['id'] ?>">
                        <input type="hidden" name="action" value="like">
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            ❤️ Like (<?= $discussion['likes'] ?>)
                        </button>
                    </form>

                    <form method="POST">
                        <input type="hidden" name="discussion_id" value="<?= $discussion['id'] ?>">
                        <input type="hidden" name="action" value="retweet">
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            🔄 Retweet (<?= $discussion['retweets'] ?>)
                        </button>
                    </form>
                </div>

                <!-- Replies -->
                <div class="mt-3">
                    <h6>Replies:</h6>
                    <?php foreach (fetchReplies($pdo, $discussion['id']) as $reply): ?>
                        <div class="alert alert-secondary p-2 mb-1"><?= htmlspecialchars($reply) ?></div>
                    <?php endforeach; ?>

                    <form method="POST" class="mt-2">
                        <input type="hidden" name="discussion_id" value="<?= $discussion['id'] ?>">
                        <input type="hidden" name="action" value="reply">
                        <div class="input-group">
                            <input type="text" name="reply_text" class="form-control" placeholder="Add a reply..." required>
                            <button class="btn btn-outline-success" type="submit">Reply</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($discussions)): ?>
        <div class="alert alert-info">No discussions available for this topic yet.</div>
    <?php endif; ?>
</div>

</body>
</html>
