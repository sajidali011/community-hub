<?php
include "db_connection.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to create a post.'); window.location.href='login.php';</script>";
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

// Get the community ID from URL
$community_id = $_GET['community_id'] ?? null;
if (!$community_id) {
    echo "<script>alert('Community ID is missing. Redirecting to home.'); window.location.href='home.php';</script>";
    exit;
}

// Fetch community details
$sql_community = "SELECT name FROM communities WHERE id = ?";
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

// Close the connection
$conn->close();

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'];
    $image = $_FILES['image']['name'] ?? null;

    // Handle image upload
    if ($image) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);

        // Check if the file is a valid image
        if (getimagesize($_FILES["image"]["tmp_name"]) === false) {
            echo "<script>alert('File is not an image.'); window.location.href='create_post.php?community_id=" . $community_id . "';</script>";
            exit;
        }

        // Move the uploaded image file to the target directory
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            echo "<script>alert('Failed to upload image.'); window.location.href='create_post.php?community_id=" . $community_id . "';</script>";
            exit;
        }
    }

    // Insert the post into the database
    $conn = new mysqli($servername, $username, $password, $dbname);
    $sql_post = "INSERT INTO posts (user_id, community_id, content, image, created_at) 
                 VALUES (?, ?, ?, ?, NOW())";
    $stmt_post = $conn->prepare($sql_post);
    $stmt_post->bind_param("iiss", $user_id, $community_id, $content, $target_file);
    $stmt_post->execute();
    $stmt_post->close();

    // Redirect to the community page
    header("Location: explore_community.php?community_id=" . $community_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Post - <?= htmlspecialchars($community_name); ?></title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/create_post.css">

  <!-- Include TinyMCE -->
  <script src="https://cdn.tiny.cloud/1/l0srg35kfmvqsfcfvv3bcttplkbaoxrmgjbote9penzenwx1/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

  <script>
   tinymce.init({
  selector: 'textarea',
  plugins: 'image media link table charmap code emoticons',
  toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | link image media | bullist numlist | table charmap | code emoticons',
  height: 400,
  image_title: true,
  automatic_uploads: true, // Ensure automatic uploads are enabled
  file_picker_types: 'image',
  file_picker_callback: function (callback, value, meta) {
    var input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');
    input.onchange = function () {
      var file = input.files[0];
      var reader = new FileReader();
      reader.onload = function () {
        // Send the file to your server here to upload and get the URL
        var formData = new FormData();
        formData.append('image', file);
        fetch('upload_image.php', {
          method: 'POST',
          body: formData,
        })
        .then(response => response.json())
        .then(data => {
          // Pass the uploaded image URL to TinyMCE
          callback(data.file_url, { alt: file.name });
        })
        .catch(error => console.error('Error uploading image:', error));
      };
      reader.readAsDataURL(file);  // Read file as base64 (to display preview)
    };
    input.click();
  },
  setup: function (editor) {
    editor.on('change', function () {
      tinymce.triggerSave();
    });
  }
});

  </script>
</head>
<body>

<!-- Create Post Form -->
<div class="create-post-container">
  <h2>Create a Post in <?= htmlspecialchars($community_name); ?></h2>
  <form action="create_post.php?community_id=<?= $community_id; ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="user_id" value="<?= $user_id; ?>">
    <input type="hidden" name="community_id" value="<?= $community_id; ?>">

    <!-- TinyMCE Editor Textarea -->
    <textarea name="content" placeholder="Write your post here..." required></textarea><br><br>

    <!-- File upload (for optional image upload) -->
    <label for="image">Upload an image (optional):</label>
    <input type="file" name="image" id="image"><br><br>

    <button type="submit">Post</button>
  </form>
</div>

<script src="create_post.js"></script>
</body>
</html>
