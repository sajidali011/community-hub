<?php
include "db_connection.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to post.'); window.location.href='login.php';</script>";
    exit;
}

// Get the form data
$user_id = $_POST['user_id'] ?? null;
$community_id = $_POST['community_id'] ?? null;
$content = $_POST['content'] ?? null;
$image = $_FILES['image'] ?? null;

// Check if the necessary data exists
if (!$user_id || !$community_id || !$content) {
    die("Error: Missing required fields.");
}

// Validate the content
if (empty($content)) {
    die("Error: Content is required.");
}

// Handle image upload if an image is provided
$image_path = null;
if ($image && $image['error'] === UPLOAD_ERR_OK) {
    $target_dir = "uploads/post_images/";
    $image_name = uniqid() . "_" . basename($image["name"]);
    $target_file = $target_dir . $image_name;

    // Ensure the directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Move the uploaded file
    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        $image_path = $target_file;
    } else {
        die("Error: Unable to upload image.");
    }
}

// Insert the data into the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the insert statement
$sql = "INSERT INTO posts (user_id, community_id, content, image) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $user_id, $community_id, $content, $image_path);

// Execute the query
if ($stmt->execute()) {
    echo "<script>alert('Post created successfully!'); window.location.href='explore_community.php?community_id=$community_id#posts';</script>";
} else {
    echo "<script>alert('Error: Unable to create post.'); window.location.href='create_post.php?community_id=$community_id';</script>";
}

$stmt->close();
$conn->close();
?>
