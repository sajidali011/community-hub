<?php
include "db_connection.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to view the post.'); window.location.href='login.php';</script>";
    exit;
}

// Get the logged-in user's email from session
$current_email = $_SESSION['email'];

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the logged-in user's ID
$sql_user = "SELECT id FROM register WHERE email = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $current_email);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
$stmt_user->close();

if (!$user) {
    echo "<script>alert('User not found.'); window.location.href='login.php';</script>";
    exit;
}
$user_id = $user['id'];

// Get the community ID and post ID from URL
$community_id = $_GET['community_id'] ?? null;
$post_id = $_GET['post_id'] ?? null;

if (!$community_id || !$post_id) {
    echo "<script>alert('Invalid post or community ID. Redirecting to home.'); window.location.href='index.php';</script>";
    exit;
}

// Fetch the post details from the database
$sql_post = "SELECT p.id, p.content, p.image, p.created_at, r.username AS author, c.name AS community_name
             FROM posts p
             JOIN register r ON p.user_id = r.id
             JOIN communities c ON p.community_id = c.id
             WHERE p.id = ? AND p.community_id = ?";
$stmt_post = $conn->prepare($sql_post);
$stmt_post->bind_param("ii", $post_id, $community_id);
$stmt_post->execute();
$result_post = $stmt_post->get_result();
$post = $result_post->fetch_assoc();
$stmt_post->close();

if (!$post) {
    echo "<script>alert('Post not found.'); window.location.href='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['community_name']) ?> - Post Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .post-container {
            width: 100%;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .post-card {
            width: 100%;
            max-width: 1500px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .post-image {
            max-width: 100%;
            height: auto;
            max-height: 500px;
            border-bottom: 1px solid #ddd;
        }
        .post-content {
            padding: 20px;
        }
        .post-title {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .post-meta {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        .content-text {
            font-size: 1.1rem;
            line-height: 1.6;
        }
    </style>
</head>
<body>

<div class="post-container">
        <div class="post-content">
            <h1 class="post-title">Community: <?= htmlspecialchars($post['community_name']) ?></h1>
            <p class="post-meta">By <?= htmlspecialchars($post['author']) ?> | Posted on <?= htmlspecialchars($post['created_at']) ?></p> <hr>
            <div class="content-text">
                <?= $post['content'] ?>
            </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
