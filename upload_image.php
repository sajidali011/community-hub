<?php
include 'db_connection.php'; // Database connection file
$target_dir = "uploads/post_images/";
$response = [];

if ($_FILES["image"]["error"] == UPLOAD_ERR_OK) {
    $image = $_FILES["image"];
    $image_name = uniqid() . "_" . basename($image["name"]);
    $target_file = $target_dir . $image_name;

    // Ensure the upload directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Move the uploaded image to the target directory
    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        // Image uploaded successfully, send the file URL
        $response["file_url"] = $target_file;
    } else {
        $response["error"] = "Error uploading image.";
    }
} else {
    $response["error"] = "Error: " . $_FILES["image"]["error"];
}

// Return response as JSON
echo json_encode($response);
?>
