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
    </style>
</head>
<body>

<div class="container">
    <h2>Submit Discussion</h2>
    

    <!-- Navigation Bar for Topics -->
    
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
</div>
</body>