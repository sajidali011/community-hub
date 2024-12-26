<?php
include "db_connection.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to view the community.'); window.location.href='login.php';</script>";
    exit;
}

$current_email = $_SESSION['email'];

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

// Get the community ID from URL
$community_id = $_GET['community_id'] ?? null;
if (!$community_id) {
    echo "<script>alert('Community ID is missing. Redirecting to home.'); window.location.href='index.php';</script>";
    exit;
}

// Fetch community details
$sql_community = "SELECT name, logo FROM communities WHERE id = ?";
$stmt_community = $conn->prepare($sql_community);
$stmt_community->bind_param("i", $community_id);
$stmt_community->execute();
$result_community = $stmt_community->get_result();
$community = $result_community->fetch_assoc();
$stmt_community->close();

if (!$community) {
    echo "<script>alert('Community not found.'); window.location.href='home.php';</script>";
    exit;
}

$community_name = $community['name'];
$community_logo = $community['logo']; // Optional, not used yet

// Fetch posts for the current community
$sql_posts = "SELECT p.content, p.image, p.created_at, r.username AS author, p.id AS post_id 
              FROM posts p
              JOIN register r ON p.user_id = r.id
              WHERE p.community_id = ?
              ORDER BY p.created_at DESC";
$stmt_posts = $conn->prepare($sql_posts);
$stmt_posts->bind_param("i", $community_id);
$stmt_posts->execute();
$result_posts = $stmt_posts->get_result();
$posts = $result_posts->fetch_all(MYSQLI_ASSOC);
$stmt_posts->close();

// Fetch user's profile image
$sql_user_profile = "SELECT profile_image FROM community_profile WHERE user_id = ?";
$stmt_user_profile = $conn->prepare($sql_user_profile);
$stmt_user_profile->bind_param("i", $user_id);
$stmt_user_profile->execute();
$result_user_profile = $stmt_user_profile->get_result();
$user_profile = $result_user_profile->fetch_assoc();
$stmt_user_profile->close();

$user_imgupload = !empty($user_profile['profile_image']) ? $user_profile['profile_image'] : 'uploads/default-profile.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Dashboard</title>
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
            height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .post-card:hover {
            transform: translateY(-5px);
        }

        .post-card img,
        .post-card video {
            max-width: 100%;
            max-height: 200px;
            object-fit: cover;
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
            flex-grow: 1;
        }

        .post-card .timestamp {
            font-size: 12px;
            color: #999;
            margin-top: auto;
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
<div class="menubar">
    <div class="dropdown">
        <img src="<?= htmlspecialchars($user_imgupload) ?>" alt="Profile Image" class="profile-img">
        <div class="dropdown-content">
            <a href="community_profile.php">Profile</a>
            <a href="create_post.php?community_id=<?= $community_id ?>">Create Post</a>
            <a href="drafts.php">Draft</a>
            <a href="community_list.php">Back</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>

<div class="sidebar">
    <div class="community-header">
        <img src="<?= htmlspecialchars($community_logo); ?>" alt="Community Logo" class="community-logo">
        <h2><?= htmlspecialchars($community_name); ?></h2>
    </div>
    <ul>
        <li><a href="#home" onclick="showContent('home')"><i class="fas fa-home icon"></i> Home</a></li>
        <li><a href="create_post.php?community_id=<?= $community_id ?>" onclick="showContent('create-post')"><i class="fas fa-pen icon"></i> Create Post</a></li>
        <li><a href="drafts.php" onclick="showContent('drafts')"><i class="fas fa-file-alt icon"></i> Drafts</a></li>
        <li><a href="#community" onclick="showContent('community')"><i class="fas fa-users icon"></i> Community Overview</a></li>
        <li><a href="#posts" onclick="showContent('posts')"><i class="fas fa-newspaper icon"></i> Recent Posts</a></li>
        <li><a href="#guidelines" onclick="showContent('guidelines')"><i class="fas fa-book icon"></i> Guidelines</a></li>
    </ul>
</div>

<div class="content">
    <div id="posts" class="content-item active">
        <h3>Recent Posts</h3>
        <div class="posts-container">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card" onclick="window.location.href='show_post.php?community_id=<?= $community_id ?>&post_id=<?= $post['post_id'] ?>'">
                        <div class="post-card-header">
                            <img src="<?= htmlspecialchars($user_imgupload) ?>" alt="User Profile Image">
                            <h4><?= htmlspecialchars($post['author']) ?></h4>
                        </div>
                        <p><?= htmlspecialchars(mb_substr(strip_tags($post['content']), 0, 100)) ?>...</p>
                        <div class="timestamp"><?= htmlspecialchars($post['created_at']) ?></div>
                        <div class="div"></div>
                        <?php if (!empty($post['image'])): ?>
                            <?php if (preg_match('/\.(mp4|webm|ogg)$/i', $post['image'])): ?>
                                <video controls>
                                    <source src="<?= htmlspecialchars($post['image']) ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            <?php else: ?>
                                <img src="<?= htmlspecialchars($post['image']) ?>" alt="Post Image">
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No posts available.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="explore_community.js"></script>
</body>
</html>
