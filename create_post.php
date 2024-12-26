<?php
include "db_connection.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to create a post.'); window.location.href='login.php';</script>";
    exit;
}

// Get the logged-in user's email
$current_email = $_SESSION['email'];

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);
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

// Get and validate the community ID
$community_id = isset($_GET['community_id']) ? intval($_GET['community_id']) : null;
if (!$community_id) {
    echo "<script>alert('Community ID is missing. Redirecting to home.'); window.location.href='home.php';</script>";
    exit;
}

// Validate that the community exists
$sql_community = "SELECT name FROM communities WHERE id = ?";
$stmt_community = $conn->prepare($sql_community);
$stmt_community->bind_param("i", $community_id);
$stmt_community->execute();
$result_community = $stmt_community->get_result();
$community = $result_community->fetch_assoc();
$stmt_community->close();

if (!$community) {
    echo "<script>alert('Community not found. Redirecting to home.'); window.location.href='home.php';</script>";
    exit;
}
$community_name = $community['name'];

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? 'Untitled Post';
    $content = $_POST['content'];
    $image = $_FILES['image']['name'] ?? null;

    // Handle image upload
    $target_file = null;
    if ($image) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (getimagesize($_FILES["image"]["tmp_name"]) === false) {
            echo "<script>alert('File is not an image.'); window.location.href='create_post.php?community_id=$community_id';</script>";
            exit;
        }
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            echo "<script>alert('Failed to upload image.'); window.location.href='create_post.php?community_id=$community_id';</script>";
            exit;
        }
    }

    // Insert the post into the database
    $sql_post = "INSERT INTO posts (user_id, community_id, title, content, image, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt_post = $conn->prepare($sql_post);
    $stmt_post->bind_param("iisss", $user_id, $community_id, $title, $content, $target_file);

    if (!$stmt_post->execute()) {
        echo "<script>alert('Failed to insert post.'); window.location.href='create_post.php?community_id=$community_id';</script>";
        exit;
    }
    $stmt_post->close();

    // Redirect to the community page
    header("Location: explore_community.php?community_id=$community_id");
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Post - <?= htmlspecialchars($community_name); ?></title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.tiny.cloud/1/l0srg35kfmvqsfcfvv3bcttplkbaoxrmgjbote9penzenwx1/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

  <script>
    tinymce.init({
      selector: 'textarea',
      plugins: 'image media link table charmap code emoticons',
      toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | link image media | bullist numlist | table charmap | code emoticons',
      height: 400,
      image_title: true,
      automatic_uploads: true,
      file_picker_types: 'image',
      file_picker_callback: function (callback, value, meta) {
        var input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.onchange = function () {
          var file = input.files[0];
          var reader = new FileReader();
          reader.onload = function () {
            callback(reader.result, { alt: file.name });
          };
          reader.readAsDataURL(file);
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

<div class="container-fluid my-5">
  <div class="row">
    <div class="col-12">
      <div class="card p-4 shadow w-100">
        <h2 class="card-title mb-4">Create a Post in <?= htmlspecialchars($community_name); ?></h2>
        <form action="create_post.php?community_id=<?= $community_id; ?>" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <input type="text" class="form-control" name="title" placeholder="Enter post title" required>
          </div>
          <div class="form-group">
            <textarea class="form-control" name="content" placeholder="Write your post here..." required></textarea>
          </div>
          <div class="form-group">
            <label for="image">Choose Image:</label>
            <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
          </div>
          <button type="submit" class="btn btn-primary">Publish</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
