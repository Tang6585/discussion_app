<?php
$host = "localhost";
$user = "root";  // Change if using different user
$password = "";
$dbname = "discussion_app";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>