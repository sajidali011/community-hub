<?php
include "db_connection.php" ?>
<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to join communities.'); window.location.href='login.php';</script>";
    exit;
}

$current_email = $_SESSION['email'];
$community_id = $_POST['community_id'] ?? null;

if (!$community_id) {
    echo "<script>alert('Invalid community.'); window.location.href='community_list.php';</script>";
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user ID
$user_query = "SELECT id FROM register WHERE email = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("s", $current_email);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();
$current_user_id = $user_data['id'];
$stmt->close();

// Add community to user's joined list
$join_query = "INSERT INTO user_communities (user_id, community_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE user_id = user_id";
$stmt = $conn->prepare($join_query);
$stmt->bind_param("ii", $current_user_id, $community_id);
if ($stmt->execute()) {
    echo "<script>alert('You have successfully joined the community!'); window.location.href='my_community.php';</script>";
} else {
    echo "<script>alert('Failed to join the community. Try again later.'); window.location.href='community_list.php';</script>";
}
$stmt->close();
$conn->close();
?>
