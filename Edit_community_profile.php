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

$name = $communityProfile['name'] ?? '';
$bio = $communityProfile['bio'] ?? '';
$profileImage = $communityProfile['profile_image'] ?? $defaultProfileImage;

$alertMessage = ""; // To store SweetAlert message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);

    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $uploadDir = "uploads/";
        $profileImage = $uploadDir . uniqid() . "-" . basename($_FILES['profile_image']['name']);
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $profileImage);
    }

    // Insert or Update Community Profile
    if ($communityProfile) {
        $updateQuery = "UPDATE community_profile
                        SET name = '$name', bio = '$bio', profile_image = '$profileImage'
                        WHERE user_id = $user_id AND community_id = $community_id";
        $updateResult = mysqli_query($conn, $updateQuery);
        if (!$updateResult) {
            die('Error updating community profile: ' . mysqli_error($conn));
        }
        $alertMessage = "Profile updated successfully!";
    } else {
        $insertQuery = "INSERT INTO community_profile (user_id, community_id, name, bio, profile_image)
                        VALUES ($user_id, $community_id, '$name', '$bio', '$profileImage')";
        $insertResult = mysqli_query($conn, $insertQuery);
        if (!$insertResult) {
            die('Error inserting community profile: ' . mysqli_error($conn));
        }
        $alertMessage = "Profile created successfully!";
    }

    // Pass the alert message to JavaScript
    $_SESSION['alertMessage'] = $alertMessage;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow p-4">
            <h1 class="text-center mb-4">Edit Profile</h1>
            <form action="" method="POST" enctype="multipart/form-data">
                <!-- Name Field -->
                <div class="mb-3">
                    <label for="name" class="form-label">Name:</label>
                    <input type="text" name="name" id="name" 
                           value="<?= htmlspecialchars($name) ?>" 
                           class="form-control" placeholder="Enter your name" required>
                </div>

                <!-- Bio Field -->
                <div class="mb-3">
                    <label for="bio" class="form-label">Bio:</label>
                    <textarea name="bio" id="bio" class="form-control" rows="4" 
                              placeholder="Write something about yourself"><?= htmlspecialchars($bio) ?></textarea>
                </div>

                <!-- Profile Image Upload -->
                <div class="mb-3">
                    <label for="profile_image" class="form-label">Profile Image:</label>
                    <input type="file" name="profile_image" id="profile_image" class="form-control">
                </div>

                <!-- Display Current Profile Image -->
                <div class="mb-3 text-center">
                    <img src="<?= htmlspecialchars($profileImage) ?>" 
                         alt="Profile Image" class="img-thumbnail" style="width: 150px; height: 150px;">
                </div>

                <!-- Save Button -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary w-100">Save Profile</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (isset($_SESSION['alertMessage'])): ?>
    <script>
        Swal.fire({
            title: 'Success!',
            text: '<?= $_SESSION['alertMessage'] ?>',
            icon: 'success'
        }).then(function() {
            window.location.href = 'community_profile.php'; // Redirect after alert closes
        });
    </script>
    <?php 
    unset($_SESSION['alertMessage']); // Clear the session message after use
    endif; ?>
</body>
</html>
