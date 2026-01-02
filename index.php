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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic = $_POST['topic'];
    $statement = $_POST['statement'];

    if (!empty($topic) && !empty($statement)) {
        $stmt = $conn->prepare("INSERT INTO discussions (topic, statement) VALUES (?, ?)");
        $stmt->bind_param("ss", $topic, $statement);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch discussions from the database
$result = $conn->query("SELECT topic, statement FROM discussions");
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
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }
    </style>
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

    <h3>categories</h3>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="discussion-list">
                <div><strong><?php echo htmlspecialchars($row['topic']); ?></strong></div>
                <div><?php echo htmlspecialchars($row['statement']); ?></div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No discussions yet.</p>
    <?php endif; ?>

</div>

<script>
    // Hide form after submission
    if (window.location.search.indexOf('submitted') > -1) {
        document.getElementById('discussionForm').style.display = 'none';
    }
</script>

</body>
</html>

<?php
$conn->close();
?>
