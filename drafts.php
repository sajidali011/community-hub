<?php
include "db_connection.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to view drafts.'); window.location.href='login.php';</script>";
    exit;
}

// Get the logged-in user's email
$current_email = $_SESSION['email'];

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the logged-in user's ID
$sql_user = "SELECT id, imgupload FROM register WHERE email = ?";
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
$user_imgupload = $user['imgupload'];

// Optional: Get the community ID from URL or default to NULL
$community_id = $_GET['community_id'] ?? null;

// Build the query based on the presence of community_id
if ($community_id) {
    $sql_drafts = "SELECT p.content, p.image, p.created_at, p.id AS post_id 
                   FROM posts p 
                   WHERE p.user_id = ? AND p.community_id = ? AND p.status = 'draft'
                   ORDER BY p.created_at DESC";
    $stmt_drafts = $conn->prepare($sql_drafts);
    $stmt_drafts->bind_param("ii", $user_id, $community_id);
} else {
    $sql_drafts = "SELECT p.content, p.image, p.created_at, p.id AS post_id 
                   FROM posts p 
                   WHERE p.user_id = ? AND p.status = 'draft'
                   ORDER BY p.created_at DESC";
    $stmt_drafts = $conn->prepare($sql_drafts);
    $stmt_drafts->bind_param("i", $user_id);
}

$stmt_drafts->execute();
$result_drafts = $stmt_drafts->get_result();
$drafts = $result_drafts->fetch_all(MYSQLI_ASSOC);
$stmt_drafts->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Saved Drafts</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/explore_community.css">
  <style>
    .posts-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .post-card {
        background-color: #fff;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .post-card:hover {
        transform: translateY(-5px);
    }

    .post-card img,
    .post-card video {
        max-width: 100%;
        border-radius: 8px;
        margin-top: 10px;
    }

    .post-card h4 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .post-card p {
        color: #555;
    }

    .post-card .timestamp {
        font-size: 12px;
        color: #999;
    }

    .post-card-header {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .post-card-header img {
        border-radius: 50%;
        width: 40px;
        height: 40px;
        margin-right: 10px;
    }
  </style>
</head>
<body>
<div class="content">
  <div id="drafts" class="content-item active">
    <h3>Saved Drafts</h3>
    
    <div class="posts-container">
        <?php if (!empty($drafts)): ?>
            <?php foreach ($drafts as $draft): ?>
                <div class="post-card" onclick="window.location.href='edit_draft.php?draft_id=<?= $draft['post_id'] ?>'">
                    <div class="post-card-header">
                        <img src="<?= htmlspecialchars($user_imgupload) ?>" alt="User Profile Image">
                        <h4><?= htmlspecialchars($current_email) ?></h4>
                    </div>
                    <p><?= htmlspecialchars(mb_substr(strip_tags($draft['content']), 0, 100)) ?>...</p>
                    <div class="timestamp"><?= htmlspecialchars($draft['created_at']) ?></div>

                    <?php if (!empty($draft['image'])): ?>
                        <?php if (preg_match('/\.(mp4|webm|ogg)$/i', $draft['image'])): ?>
                            <video controls>
                                <source src="<?= htmlspecialchars($draft['image']) ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        <?php else: ?>
                            <img src="<?= htmlspecialchars($draft['image']) ?>" alt="Post Image">
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No drafts available.</p>
        <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
