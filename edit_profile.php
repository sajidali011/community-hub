<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "community_hub";

// Start session to get logged-in user's information
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to view this page.'); window.location.href='login.php';</script>";
    exit;
}
// Get the current email from session
$current_email = $_SESSION['email'];

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch current user data from the 'register' table using email
$sql = "SELECT imgupload, firstname, username, bio FROM register WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $current_email); // Bind the email parameter
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the user's data
    $row = $result->fetch_assoc();
    $imgupload = $row['imgupload'] ? $row['imgupload'] : 'default-avatar.png'; // Default if no image
    $firstname = $row['firstname'];
    $username = $row['username'];
    $bio = $row['bio'];
} else {
    echo "<script>alert('No data found for this user!'); window.location.href='login.php';</script>";
    exit;
}

// Update user data after form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_username = $_POST['username'];
    $updated_firstname = $_POST['firstname'];
    $updated_bio = $_POST['bio'];

    // Handle file upload
    $profile_image = $_FILES['profile_image']['name'];
    if ($profile_image) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);
    } else {
        $target_file = $imgupload; // Use existing image if none uploaded
    }

    // Update the database with new data
    $update_query = "UPDATE register SET username = ?, firstname = ?, bio = ?, imgupload = ? WHERE email = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('sssss', $updated_username, $updated_firstname, $updated_bio, $target_file, $current_email);
    $stmt->execute();
    $stmt->close();

    // Update session with new user data
    $_SESSION['firstname'] = $updated_firstname;
    $_SESSION['imgupload'] = $target_file;

    // Redirect to the profile page after updating
    header('Location: user_profile.php');
    exit();
}

// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 450px;
            padding: 40px;
            text-align: center;
        }

        .card h1 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 4px solid #2575fc;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-top: 8px;
            background-color: #fff;
            color: #333;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #6a11cb;
            box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
        }

        .file-input {
            display: block;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0; /* Hide the file input */
            cursor: pointer;
        }

        .image-preview {
            position: relative;
            display: inline-block;
            width: 120px;
            height: 120px;
        }

        .image-preview .upload-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            font-size: 30px;
        }

        .upload-container {
            position: relative;
        }

        .upload-container:hover .image-preview {
            opacity: 0.9;
        }

        .btn-primary {
            background-color: #2575fc;
            border: none;
            padding: 12px 25px;
            color: white;
            font-size: 18px;
            border-radius: 8px;
            margin-top: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #6a11cb;
        }

        .cancel-btn {
            background-color: #e0e0e0;
            color: #333;
            padding: 12px 25px;
            font-size: 18px;
            border-radius: 8px;
            margin-top: 15px;
            text-decoration: none;
        }

        .cancel-btn:hover {
            background-color: #c4c4c4;
        }
    </style>
</head>

<body>

    <div class="card">
        <h1>Edit Profile</h1>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <div class="upload-container">
                    <div class="image-preview">
                        <img id="profileImage" src="<?php echo $imgupload; ?>" alt="Profile Image" class="profile-image">
                        <div class="upload-icon">
                            <i class="fas fa-camera"></i>
                        </div>
                    </div>
                    <input type="file" class="file-input" name="profile_image" id="imgupload" accept="image/*">
                </div>
            </div>

            <div class="form-group">
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Username">
            </div>

            <div class="form-group">
                <input type="text" name="firstname" value="<?php echo htmlspecialchars($firstname); ?>" placeholder="First Name">
            </div>

            <div class="form-group">
                <textarea name="bio" rows="4" placeholder="Bio"><?php echo htmlspecialchars($bio); ?></textarea>
            </div>

            <button type="submit" class="btn-primary">Save Changes</button>
            <a href="user_profile.php" class="cancel-btn">Cancel</a>
        </form>
    </div>

    <script>
        // JavaScript to show the selected image in the image preview
        const fileInput = document.getElementById('imgupload');
        const profileImage = document.getElementById('profileImage');

        fileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    profileImage.src = event.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>

</body>

</html>
