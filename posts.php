<?php
// posts.php
session_start();
include('db_connection.php');

// Fetch posts from the database
$query = "SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

$posts = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts - CommunityHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="index.php">CommunityHub</a>
            <div class="navbar-nav">
                <a class="nav-item nav-link" href="explore_community.php">Explore Community</a>
                <a class="nav-item nav-link active" href="posts.php">All Posts</a>
            </div>
        </nav>
    </header>

    <div class="container mt-4">
        <h1>All Posts</h1>

        <?php if (count($posts) > 0): ?>
            <div class="row">
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-4">
                        <div class="post-card mb-3">
                            <?php if ($post['image']): ?>
                                <img src="<?= htmlspecialchars($post['image']); ?>" alt="Post Image" class="card-img-top">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($post['title']); ?></h5>
                                <p class="card-text"><?= nl2br(htmlspecialchars($post['content'])); ?></p>
                                <small>Posted on <?= date('F j, Y, g:i a', strtotime($post['created_at'])); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No posts available.</p>
        <?php endif; ?>
    </div>

    <div class="footer mt-4">
        <p>&copy; 2024 CommunityHub. All rights reserved.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
