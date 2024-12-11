<?php
include 'db_connection.php';
session_start();

// Simulate logged-in user and community
$user_email = $_SESSION['email'] ?? 'test@example.com';
$community_id = 1; // Current community ID

// Fetch user ID and existing profile image from register table
$userQuery = "SELECT id, imgupload FROM register WHERE email = '$user_email'";
$userResult = mysqli_query($conn, $userQuery);
if (!$userResult) {
    die('Error fetching user data: ' . mysqli_error($conn));
}
$userData = mysqli_fetch_assoc($userResult);
$user_id = $userData['id'];
$defaultProfileImage = $userData['imgupload'];

// Fetch existing community profile
$communityQuery = "SELECT * FROM community_profile WHERE user_id = $user_id AND community_id = $community_id";
$communityResult = mysqli_query($conn, $communityQuery);
if (!$communityResult) {
    die('Error fetching community profile: ' . mysqli_error($conn));
}
$communityProfile = mysqli_fetch_assoc($communityResult);

$name = $communityProfile['name'] ?? 'Guest User';
$bio = $communityProfile['bio'] ?? 'No bio available.';
$profileImage = $communityProfile['profile_image'] ?? $defaultProfileImage;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow p-4 text-center">
            <h1>Welcome, <?= htmlspecialchars($name) ?>!</h1>
            <div class="my-3">
                <img src="<?= htmlspecialchars($profileImage) ?>" 
                     alt="Profile Image" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px;">
            </div>
            <!-- <p><strong>Username:</strong> <?= htmlspecialchars($user_email) ?></p> -->
            <p><h4><b> <?= htmlspecialchars($name) ?></h4></b></p>
            <p><h6><strong>Bio:</strong> <?= nl2br(htmlspecialchars($bio)) ?></h6></p>
            <div class="mt-4">
                <a href="Edit_community_profile.php" class="btn btn-primary">Edit Profile</a>
                <a href="home.php" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
