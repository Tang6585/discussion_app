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

// Handle form submission for new discussions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['topic'])) {
    $topic = $_POST['topic'];
    $statement = $_POST['statement'];
    if (!empty($topic) && !empty($statement)) {
        $stmt = $conn->prepare("INSERT INTO discussions (topic, statement) VALUES (?, ?)");
        $stmt->bind_param("ss", $topic, $statement);
        $stmt->execute();
        $stmt->close();
    }
}

// Handle reply, like, and retweet
if (isset($_POST['action'])) {
    $discussionId = $_POST['discussion_id'];

    if ($_POST['action'] === 'like') {
        // Handle Like action
        echo json_encode(['status' => 'liked']);
        exit;
    }

    if ($_POST['action'] === 'retweet') {
        echo json_encode(['status' => 'retweeted']);
        exit;
    }

    if ($_POST['action'] === 'reply') {
        $replyText = $_POST['reply_text'];
        echo json_encode(['status' => 'replied', 'text' => $replyText]);
        exit;
    }
}

// Fetch discussions from the database
$result = $conn->query("SELECT id, topic, statement FROM discussions");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussion App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
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
        .like-btn, .retweet-btn, .reply-btn {
            background: none;
            border: none;
            cursor: pointer;
            margin-right: 10px;
        }
        .like-btn.liked {
            color: red;
        }
        .retweet-btn.retweeted {
            color: lightblue;
        }
    </style>
    <script>
        // Handle like, retweet, and reply events
        function handleAction(action, discussionId) {
            if (action === 'reply') {
                let replyText = prompt('Enter your reply:');
                if (!replyText) return;
            }

            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: action,
                    discussion_id: discussionId,
                    reply_text: action === 'reply' ? replyText : ''
                })
            })
            .then(response => response.json())
            .then(data => {
                if (action === 'like') {
                    document.querySelector(`#like-btn-${discussionId}`).classList.toggle('liked');
                } else if (action === 'retweet') {
                    document.querySelector(`#retweet-btn-${discussionId}`).classList.toggle('retweeted');
                } else if (action === 'reply') {
                    const replyContainer = document.querySelector(`#replies-${discussionId}`);
                    const replyElement = document.createElement('div');
                    replyElement.textContent = data.text;
                    replyContainer.appendChild(replyElement);
                }
            });
        }
    </script>
</head>
<body>

<div class="container">
    <h2>Discussion App</h2>

    <!-- Button to show the form -->
    <button class="button" onclick="document.getElementById('discussionForm').style.display='block'">
        Start Discussion
    </button>

    <!-- Discussion Form -->
    <div id="discussionForm" class="discussion-form" style="display:none;">
        <form method="POST">
            <label for="topic">Topic:</label>
            <input type="text" id="topic" name="topic" required style="width: 100%; padding: 8px;"><br><br>
            
            <label for="statement">Statement:</label>
            <textarea id="statement" name="statement" required style="width: 100%; padding: 8px;"></textarea><br><br>
            
            <button type="submit" class="button">Submit</button>
        </form>
    </div>

    <h3>Discussions</h3>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="discussion-list">
                <div><strong><?php echo htmlspecialchars($row['topic']); ?></strong></div>
                <div><?php echo htmlspecialchars($row['statement']); ?></div>
                <div>
                    <button id="like-btn-<?php echo $row['id']; ?>" class="like-btn" onclick="handleAction('like', <?php echo $row['id']; ?>)">Like ❤️</button>
                    <button id="retweet-btn-<?php echo $row['id']; ?>" class="retweet-btn" onclick="handleAction('retweet', <?php echo $row['id']; ?>)">Retweet 🔄</button>
                    <button class="reply-btn" onclick="handleAction('reply', <?php echo $row['id']; ?>)">Reply 💬</button>
                </div>
                <div id="replies-<?php echo $row['id']; ?>" style="margin-top: 10px;"></div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No discussions yet.</p>
    <?php endif; ?>

</div>

</body>
</html>

<?php
$conn->close();
?>
