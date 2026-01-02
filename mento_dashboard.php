<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role'] ?? '') !== 'mentor') {
    header("Location: log.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
function safeCount($pdo,$sql,$params=[]){try{$stmt=$pdo->prepare($sql);$stmt->execute($params);return(int)$stmt->fetchColumn();}catch(Exception $e){return 0;}}
function tableExists($pdo,$table){$s=$pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name=?");$s->execute([$table]);return(bool)$s->fetchColumn();}
$discussionTable=tableExists($pdo,'discussions')?'discussions':(tableExists($pdo,'topics')?'topics':null);
$replyTable=tableExists($pdo,'replies')?'replies':null;

$total_discussions=$discussionTable?safeCount($pdo,"SELECT COUNT(*) FROM `$discussionTable` WHERE user_id=?",[$user_id]):0;
$total_replies=$replyTable?safeCount($pdo,"SELECT COUNT(*) FROM `$replyTable` WHERE user_id=?",[$user_id]):0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Mentor Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f8f9fa;font-family:'Segoe UI';}
.navbar{background:#3F51B5;box-shadow:0 4px 12px rgba(63,81,181,0.25);}
.navbar-brand{color:#FFD700!important;font-weight:700;}
.stat-card{border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06);padding:22px;}
.stat-number{font-size:2rem;font-weight:700;}
</style>
</head>
<body>
<nav class="navbar">
  <div class="container">
    <a class="navbar-brand" href="index7.php">🏠 Back to Home</a>
    <div class="ms-auto text-white">👤 <?=htmlspecialchars($_SESSION['name'])?> | Mentor</div>
  </div>
</nav>

<div class="container mt-4">
  <h2>🌱 Mentor Dashboard</h2>
  <p class="text-muted">Monitor your mentorship discussions and student guidance.</p>

  <div class="row mt-4 g-3">
    <div class="col-md-6"><div class="stat-card bg-white"><div>🗣️ Mentorship Discussions</div><div class="stat-number text-primary"><?=$total_discussions?></div></div></div>
    <div class="col-md-6"><div class="stat-card bg-white"><div>💬 Replies to Students</div><div class="stat-number text-success"><?=$total_replies?></div></div></div>
  </div>
</div>
</body>
</html>
