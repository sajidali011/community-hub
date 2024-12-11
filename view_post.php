<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to view the post.'); window.location.href='login.php';</script>";
    exit;
}

// Get the post ID from the URL
$post_id = $_GET['id'] ?? null;
if (!$post_id) {
    echo "<script>alert('Invalid post ID.'); window.location.href='index.php';</script>";
    exit;
}

// Get the post details from the database
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND status = 'published'");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$post_result = $stmt->get_result();
$post = $post_result->fetch_assoc();
$stmt->close();

// Check if the post exists
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
    <title>View Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 900px; margin: 30px auto; }
        .post-content img, .post-content video { width: 100%; max-height: 500px; object-fit: cover; }
    </style>
</head>
<body>
    <div class="container">
        <h2><?= htmlspecialchars($post['title']) ?></h2>
        <p><strong>Posted by User ID: <?= htmlspecialchars($post['user_id']) ?></strong></p>
        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        
        <?php if (strpos($post['image'], '.mp4') !== false): ?>
            <video controls>
                <source src="<?= htmlspecialchars($post['image']) ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        <?php elseif ($post['image']): ?>
            <img src="<?= htmlspecialchars($post['image']) ?>" alt="Post Image">
        <?php endif; ?>
        
        <br>
        <a href="community.php?id=<?= $post['community_id'] ?>" class="btn btn-primary">Back to Community</a>
    </div>
</body>
</html>
