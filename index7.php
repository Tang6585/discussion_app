<?php
// db.php - Database Connection
$dsn = 'mysql:host=localhost;dbname=discussion_item;charset=utf8mb4';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die('Database Connection Failed: ' . $e->getMessage());
    echo "<pre>";
print_r($_POST);
echo "</pre>";
}
// ✅ Handle Subscription Before Other POST Actions
$subscriptionMessage = '';

if (isset($_POST['subscribe'])) {
    $email = trim($_POST['email'] ?? '');
    $topic = trim($_POST['topic'] ?? '');

    if ($email && $topic) {
        try {
            $stmt = $pdo->prepare("INSERT INTO subscriptions (email, topic) VALUES (?, ?)");
            $stmt->execute([$email, $topic]);

            // Optional email sending
            // require_once __DIR__ . '/config/mailer.php';
            // sendMail($email, "Subscription Confirmation", "You subscribed to: $topic");

            $subscriptionMessage = "
                <div class='alert alert-success mt-3'>
                    ✅ <strong>Subscribed!</strong> Confirmation for <b>$topic</b> has been saved.
                </div>";
        } catch (PDOException $e) {
            $subscriptionMessage = "
                <div class='alert alert-danger mt-3'>
                    ❌ Database error: " . htmlspecialchars($e->getMessage()) . "
                </div>";
        }
    } else {
        $subscriptionMessage = "
            <div class='alert alert-warning mt-3'>
                ⚠️ Please select a topic and enter a valid email address.
            </div>";
    }
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

$subscriptionMessage = '';

if (isset($_POST['subscribe'])) {
    $email = trim($_POST['email']);
    $topic = trim($_POST['topic']);

    // Save subscription
    $stmt = $pdo->prepare("INSERT INTO subscriptions (email, topic) VALUES (?, ?)");
    $stmt->execute([$email, $topic]);

    // Send confirmation email
    require_once __DIR__ . '/config/mailer.php';
    sendMail(
        $email,
        "Subscription Confirmation",
        "Thank you for subscribing to updates about <b>$topic</b>!"
    );

    // Success message to show on page
    $subscriptionMessage = "
        <div class='alert alert-success alert-dismissible fade show mt-3' role='alert'>
            <strong>Subscribed successfully!</strong> A confirmation email has been sent to <b>$email</b>.
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
    ";
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
                if ($action === 'subscribe') {
    $email = trim($_POST['email'] ?? '');
    $topic = trim($_POST['topic'] ?? '');
    if ($email && $topic) {
        $stmt = $pdo->prepare("INSERT INTO subscriptions (email, topic) VALUES (:email, :topic)");
        $stmt->execute([
            ':email' => $email,
            ':topic' => $topic,
        ]);
    }
}

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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
/* 🌟 Global Styling */
body {
  background: url('fb.jpg') no-repeat center center fixed;
  background-size: cover;
  backdrop-filter: blur(3px);
  color: #FFD700; /* default gold text */
  font-family: 'Segoe UI', sans-serif;
}
 .main-wrapper {
            background-color:rgba(63,81,181,0.9);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.25);
        }

/* Headings and important labels */
h1, h2, h3, h4, h5, h6, strong, b, label {
  color: #FFD700 !important;
}


/* Navbar and Sidebar */
.navbar {
  background-color: #3F51B5 !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.navbar-brand, .navbar-text, .nav-link, .offcanvas-title, .sidebar-link {
  color: #FFD700 !important;
  text-shadow: 0 0 3px rgba(0,0,0,0.5);
}

.nav-link:hover, .sidebar-link:hover {
  color: #fff !important;
}

/* Sidebar background */
#sidebarMenu {
  background-color: #3F51B5 !important;
}

/* Discussion and reply boxes */
.discussion-box, .card, section, .reply-box {
  background: rgba(63, 81, 181, 0.3) !important;
  border: 1px solid rgba(255, 215, 0, 0.4);
  border-radius: 8px;
  padding: 10px;
  color: #FFD700 !important;
}

/* Discussion text and reply content */
p, .reply-text, .discussion-reply, .discussion-statement {
  color: #fff !important;  /* white text for clarity */
  text-shadow: 0 0 3px rgba(0,0,0,0.7);
}

/* Buttons */
button, .btn {
  background-color: #FFD700 !important;
  color: #3F51B5 !important;
  font-weight: bold;
  border: none;
  border-radius: 6px;
  transition: 0.3s ease;
}

button:hover, .btn:hover {
  background-color: #ffeb3b !important;
  color: #000 !important;
}

/* Topic tabs */
.nav-pills .nav-link {
  color: #FFD700 !important;
  background: transparent !important;
  border: 1px solid rgba(255, 215, 0, 0.4);
}

.nav-pills .nav-link.active,
.nav-pills .nav-link:hover {
  background-color: rgba(255, 215, 0, 0.3) !important;
  color: #fff !important;
}
/* Sidebar links */
.sidebar-link {
  color: #FFD700 !important;
  text-decoration: none;
}
.sidebar-link:hover {
  background: rgba(255,255,255,0.15);
  color: #fff !important;
}

/* Inputs */
input, textarea, select {
  background: rgba(255,255,255,0.95);
  color: #000;
  border: 1px solid #ccc;
  border-radius: 6px;
}

/* Time, like, and retweet info */
.timestamp, .like-count, .retweet-count {
  color: #FFD700 !important;
  font-size: 0.9rem;
}

/* Alerts & subscription box */
.alert, .subscribe-box {
  background: rgba(63,81,181,0.7) !important;
  color: #FFD700 !important;
  border: 1px solid rgba(255,215,0,0.3);
}

/* Add spacing */
section, .discussion-box, .subscribe-box {
  margin-bottom: 20px;
}

/* Sidebar logout button */
.sidebar-link.bg-white.text-danger {
  background-color: #FFD700 !important;
  color: #3F51B5 !important;
  font-weight: bold;
}
</style>

</head>
<body>

<div class="offcanvas offcanvas-start show" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel"
     data-bs-backdrop="false" style="background-color: #3F51B5; color: white; width: 250px; display: flex; flex-direction: column; height: 100vh;">
  
  <div class="offcanvas-header border-bottom border-light">
    <h5 class="offcanvas-title fw-bold" id="sidebarMenuLabel">Menu</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <div class="offcanvas-body d-flex flex-column justify-content-between" style="flex: 1; overflow-y: auto;">
    <!-- Top links -->
    <div>
      <a href="index7.php" class="sidebar-link text-white mb-2 d-block">🏠 Home</a>
      <a href="profile.php" class="sidebar-link text-white mb-2 d-block">👤 Profile</a>
      <a href="dashboard.php" class="sidebar-link text-white mb-2 d-block">📊 Dashboard</a>
    </div>

    <!-- Always visible logout button -->
    <div class="border-top border-light pt-3 mt-3 text-center">
      <a href="logout.php" class="sidebar-link bg-white text-danger fw-bold px-3 py-2 rounded d-inline-block w-75">
        🚪 Logout
      </a>
    </div>
  </div>
</div>
<div class="container py-4 main-wrapper">
    <nav class="navbar navbar-expand-lg navbar-dark"
     style="background-color: #3F51B5; box-shadow: 0 2px 10px rgba(103,58,183,0.6);">
        <div class="container-fluid">
            <!-- Three-dot menu button -->
            <button class="btn btn-light me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
    ☰
</button>
            <a class="navbar-brand mx-auto text-center" href="#">🗣️ Welcome to the Computer Science online Discussion Forum!</a>
        </div>
    </nav>

    <!-- Topics Navigation -->
    <ul class="nav nav-pills mb-4" style="color:rgb(255, 215, 0);">
        <?php foreach ($topics as $topic): ?>
            <li class="nav-item">
                <a class="nav-link <?= $topic === $selectedTopic ? 'active' : '' ?>" href="?topic=<?= urlencode($topic) ?>">
                    <?= htmlspecialchars($topic) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<!-- Subscription Section -->
<div class="card mb-4 shadow-sm">
    <div class="card-body" style="background-color:rgb(63,81,181)">
        <h5 class="card-title" style="color:#FFD700;">🔔 Subscribe to Notifications</h5>
        <form method="POST" action="" class="row g-3">
            <div class="col-6">
                <select name="topic" class="form-select" required>
                    <option value="">-- Select Topic --</option>
                    <?php foreach ($topics as $topic): ?>
                        <option value="<?= htmlspecialchars($topic) ?>"><?= htmlspecialchars($topic) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6">
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="col-12">
                <button type="submit" name="subscribe" class="btn btn-warning">Subscribe</button>
            </div>
        </form>

        <!-- Show Success/Error Message -->
        <?= $subscriptionMessage ?? '' ?>
    </div>
</div>

    <!-- New Discussion Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body" style="background-color:rgba(63, 81, 181, 0.9);">
            <h5 class="card-title" style="color:rgb(255, 215, 0);">Start a New Discussion</h5>
            <form method="POST" class="row g-3">
                <input type="hidden" name="action" value="new_discussion">
                <div class="col-12">
                    <input type="text" name="topic" class="form-control" placeholder="Topic" required>
                </div>
                <div class="col-12">
                    <textarea name="statement" class="form-control" placeholder="Statement" rows="3" required></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success" style="color:rgb(255, 215, 0);">Post Discussion</button>
                </div>
            </form>
        </div>
    </div>

    <h4 class="mb-3" style="color:rgb(255, 215, 0);">🧵 Discussions on: <span class="text-primary"><?= htmlspecialchars($selectedTopic) ?></span></h4>

    <?php foreach ($discussions as $discussion): ?>
        <div class="card mb-3 shadow-sm">
            <div class="card-body" style="background-color: rgba(63, 81, 181, 0.9);">
                <h5><?= htmlspecialchars($discussion['topic']) ?></h5>
                <p><?= nl2br(htmlspecialchars($discussion['statement'])) ?></p>
                <span class="badge badge-timestamp mb-2">Posted at: <?= date('Y-m-d H:i', strtotime($discussion['created_at'] ?? 'now')) ?></span>

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
                        <?= htmlspecialchars($reply) ?>
                        <div class="text-end text-muted" style="font-size: 0.75rem;">Just now</div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($discussions)): ?>
        <div class="alert alert-warning">No discussions available for this topic yet.</div>
    <?php endif; ?>
</div>
</body>
</html>