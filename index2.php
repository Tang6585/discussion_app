<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your MySQL password
$dbname = "discussion_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle new discussion form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'new_discussion') {
        $topic = $_POST['topic'];
        $statement = $_POST['statement'];
        if (!empty($topic) && !empty($statement)) {
            $stmt = $conn->prepare("INSERT INTO discussions (topic, statement) VALUES (?, ?)");
            $stmt->bind_param("ss", $topic, $statement);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Handle Like, Retweet, and Reply actions
    if (isset($_POST['discussion_id'])) {
        $discussionId = $_POST['discussion_id'];

        if ($_POST['action'] === 'like') {
            $conn->query("UPDATE discussions SET likes = likes + 1 WHERE id = $discussionId");
        } elseif ($_POST['action'] === 'retweet') {
            $conn->query("UPDATE discussions SET retweets = retweets + 1 WHERE id = $discussionId");
        } elseif ($_POST['action'] === 'reply') {
            $replyText = $_POST['reply_text'];
            $replies[$discussionId][] = $replyText;
        }
    }
}

// Fetch topics
$topicsResult = $conn->query("SELECT DISTINCT topic FROM discussions");
$topics = [];
if ($topicsResult->num_rows > 0) {
    while ($row = $topicsResult->fetch_assoc()) {
        $topics[] = $row['topic'];
    }
}

// Fetch discussions based on selected topic
$selectedTopic = $_GET['topic'] ?? ($topics[0] ?? '');
$discussions = [];
if (!empty($selectedTopic)) {
    $stmt = $conn->prepare("SELECT id, topic, statement, likes, retweets FROM discussions WHERE topic = ?");
    $stmt->bind_param("s", $selectedTopic);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $discussions[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <title>Discussion App</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .navbar {
            margin-bottom: 20px;
        }
        .discussion-form {
            background: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .discussion-list {
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }
        .button {
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
        }
        .like-btn {
            color: red;
        }
        .retweet-btn {
            color: lightblue;
        }
        .navbar-custom {
      background-color: green;
    }
    .navbar-brand {
      color: white !important;
    }
    </style>
</head>
<body>
<div class="container">
    <nav class="navbar navbar-expand-lg navbar-custom mb-4">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Discussion App Review</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link text-white" href="#">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="#">Reviews</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="#">Contact</a>
            </li>
          </ul>
          <button class="btn btn-success ms-auto" onclick="window.location.href='create-discussion.php'">Create Discussion</button>
        </div>
      </div>
    </nav>
  </div>


    <!-- Navigation Bar for Topics -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">

        <ul class="navbar-nav">
            <?php foreach ($topics as $topic): ?>
                <li class="nav-item">
                    <a class="nav-link" href="?topic=<?php echo urlencode($topic); ?>">
                        <?php echo htmlspecialchars($topic); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- Start Discussion Form -->
    <div id="discussionForm" class="discussion-form">
        <form method="POST">
            <input type="hidden" name="action" value="new_discussion">
            <label for="topic">Topic:</label>
            <input type="text" id="topic" name="topic" required style="width: 100%; padding: 8px;"><br><br>

            <label for="statement">Statement:</label>
            <textarea id="statement" name="statement" required style="width: 100%; padding: 8px;"></textarea><br><br>

            <button type="submit" class="button">Submit</button>
        </form>
    </div>

    <h3>Discussions for Topic: <?php echo htmlspecialchars($selectedTopic); ?></h3>

    <?php if (!empty($discussions)): ?>
        <?php foreach ($discussions as $discussion): ?>
            <div class="discussion-list">
                <div><strong><?php echo htmlspecialchars($discussion['topic']); ?></strong></div>
                <div><?php echo htmlspecialchars($discussion['statement']); ?></div>
                <!--  i need reply code here -->
                <div>
                    <!-- Like Form -->
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="discussion_id" value="<?php echo $discussion['id']; ?>">
                        <input type="hidden" name="action" value="like">
                        <button class="button like-btn">Like ❤️ (<?php echo $discussion['likes']; ?>)</button>
                    </form>

                    <!-- Retweet Form -->
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="discussion_id" value="<?php echo $discussion['id']; ?>">
                        <input type="hidden" name="action" value="retweet">
                        <button class="button retweet-btn">Retweet 🔄 (<?php echo $discussion['retweets']; ?>)</button>
                    </form>
                    <div class="reply-section">
                    <h4>Replies:</h4>
                    <form method="POST">
                        <input type="hidden" name="discussion_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="action" value="reply">
                        <textarea name="reply_text" required style="width: 50%; padding: 8px;"></textarea><br><br>
                        <button type="submit" class="button">Submit Reply</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No discussions for this topic.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php
$conn->close();
?>
