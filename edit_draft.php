<?php
include "db_connection.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to edit a post.'); window.location.href='login.php';</script>";
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

// Get and validate the draft ID
$draft_id = isset($_GET['draft_id']) ? intval($_GET['draft_id']) : null;
if (!$draft_id) {
    echo "<script>alert('Draft ID is missing. Redirecting to home.'); window.location.href='home.php';</script>";
    exit;
}

// Fetch the draft details
$sql_draft = "SELECT * FROM posts WHERE id = ? AND user_id = ? AND status = 'draft'";
$stmt_draft = $conn->prepare($sql_draft);
$stmt_draft->bind_param("ii", $draft_id, $user_id);
$stmt_draft->execute();
$result_draft = $stmt_draft->get_result();
$draft = $result_draft->fetch_assoc();
$stmt_draft->close();

if (!$draft) {
    echo "<script>alert('Draft not found or you do not have permission to edit this draft.'); window.location.href='home.php';</script>";
    exit;
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'];
    $image = $_FILES['image']['name'] ?? null;
    $action = $_POST['action'];

    // Handle image upload if a new image is provided
    $target_file = $draft['image'];
    if ($image) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (getimagesize($_FILES["image"]["tmp_name"]) === false) {
            echo "<script>alert('File is not an image.'); window.location.href='edit_draft.php?draft_id=$draft_id';</script>";
            exit;
        }
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            echo "<script>alert('Failed to upload image.'); window.location.href='edit_draft.php?draft_id=$draft_id';</script>";
            exit;
        }
    }

    // Update the draft in the database
    $status = ($action === "draft") ? "draft" : "published";
    $sql_update = "UPDATE posts SET content = ?, image = ?, status = ?, updated_at = NOW() WHERE id = ? AND user_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssii", $content, $target_file, $status, $draft_id, $user_id);
    $stmt_update->execute();
    $stmt_update->close();

    // Redirect based on action
    $redirect_url = ($action === "draft") ? "drafts.php" : "explore_community.php?community_id=" . $draft['community_id'];
    header("Location: $redirect_url");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Draft</title>
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
            var formData = new FormData();
            formData.append('image', file);
            fetch('upload_image.php', {
              method: 'POST',
              body: formData,
            })
            .then(response => response.json())
            .then(data => {
              callback(data.file_url, { alt: file.name });
            })
            .catch(error => console.error('Error uploading image:', error));
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
        <h2 class="card-title mb-4">Edit Draft</h2>
        <form action="edit_draft.php?draft_id=<?= $draft_id; ?>" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <textarea class="form-control" name="content" required><?= htmlspecialchars($draft['content']); ?></textarea>
          </div>

          <div class="form-group">
            <label for="image">Choose Image (optional):</label>
            <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
          </div>

          <button type="submit" name="action" value="publish" class="btn btn-primary">Publish</button>
          <button type="submit" name="action" value="draft" class="btn btn-secondary">Save as Draft</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
