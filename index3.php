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
            // Save replies in a simple array for now (could use a separate table)
            $replies[$discussionId][] = $replyText;
        }
    }
}

// Fetch discussions
$result = $conn->query("SELECT id, topic, statement, likes, retweets FROM discussions");
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
            max-width: 800px;
            margin: auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .discussion-form {
            background: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            grid-column: span 2;
        }
        .topic-list, .statement-list {
            border: 1px solid #ddd;
            padding: 10px;
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
    </style>
</head>
<body>

<div class="container">
    <!-- Start Discussion Form -->
    <div id="discussionForm" class="discussion-form">
        <h2>Start a New Discussion</h2>
        <form method="POST">
            <input type="hidden" name="action" value="new_discussion">
            <label for="topic">Topic:</label>
            <input type="text" id="topic" name="topic" required style="width: 100%; padding: 8px;"><br><br>
            
            <label for="statement">Statement:</label>
            <textarea id="statement" name="statement" required style="width: 100%; padding: 8px;"></textarea><br><br>
            
            <button type="submit" class="button">Submit</button>
        </form>
    </div>

    <!-- Topic List -->
    <div class="topic-list">
        <h3>Topics</h3>
        <ul>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li><strong><?php echo htmlspecialchars($row['topic']); ?></strong></li>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No discussions yet.</p>
        <?php endif; ?>
        </ul>
    </div>

    <!-- Statement List -->
    <div class="statement-list">
        <h3>Statements</h3>
        <ul>
        <?php $result->data_seek(0); while ($row = $result->fetch_assoc()): ?>
            <li><?php echo htmlspecialchars($row['statement']); ?></li>
        <?php endwhile; ?>
        </ul>
    </div>
</div>

</body>
</html>

<?php
$conn->close();
?>
