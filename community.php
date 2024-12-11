<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to view this page.'); window.location.href='login.php';</script>";
    exit;
}

$community_id = $_GET['id'] ?? null;
if (!$community_id) {
    echo "<script>alert('Invalid community ID.'); window.location.href='index.php';</script>";
    exit;
}

// Fetch community details
$stmt = $conn->prepare("SELECT name FROM communities WHERE id = ?");
$stmt->bind_param("i", $community_id);
$stmt->execute();
$community_result = $stmt->get_result();
$community_data = $community_result->fetch_assoc();
$stmt->close();

if (!$community_data) {
    echo "<script>alert('Community not found.'); window.location.href='index.php';</script>";
    exit;
}

$community_name = $community_data['name'];

// Fetch published posts for the community
$stmt = $conn->prepare("SELECT * FROM posts WHERE community_id = ? AND status = 'published' ORDER BY created_at DESC");
$stmt->bind_param("i", $community_id);
$stmt->execute();
$posts_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($community_name) ?> Community</title>
</head>
<body>
    <div class="container">
        <h1>Welcome to <?= htmlspecialchars($community_name) ?> Community</h1>
        
        <a href="create_post.php?community_id=<?= $community_id ?>" class="btn btn-primary">Create Post</a>

        <h2>Posts</h2>
        <?php while ($post = $posts_result->fetch_assoc()): ?>
            <div class="post-card">
                <?php if (strpos($post['image'], '.mp4') !== false): ?>
                    <video controls>
                        <source src="<?= htmlspecialchars($post['image']) ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php else: ?>
                    <img src="<?= htmlspecialchars($post['image']) ?>" alt="Post Image" class="post-image">
                <?php endif; ?>
                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                <a href="view_post.php?id=<?= $post['id'] ?>" class="btn btn-info">View Full Post</a>
            </div>
        <?php endwhile; ?>

        <?php if ($posts_result->num_rows == 0): ?>
            <p>No posts available in this community.</p>
        <?php endif; ?>
    </div>
</body>
</html>
