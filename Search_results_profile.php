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

// Get the searched username from the query string
$searched_username = isset($_GET['username']) ? trim($_GET['username']) : '';

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch data from the 'register' table using username
$sql = "SELECT imgupload, firstname, username, bio FROM register WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $searched_username); // Bind the username parameter
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the user's data
    $row = $result->fetch_assoc();
    $imgupload = $row['imgupload'] ? $row['imgupload'] : 'default-avatar.png'; // Default if no image
    $firstname = $row['firstname'];
    $username = $row['username'];
    $bio = $row['bio'] ? $row['bio'] : 'No Bio'; // Default if no bio
} else {
    echo "<script>alert('No profile found for this username!'); window.location.href='search.php';</script>";
    exit;
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
    <title>User Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            transition: background-color 0.5s, color 0.5s;
        }

        .profile-container {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 360px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .profile-container:hover {
            transform: translateY(-5px);
        }

        .profile-container h1 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-top: 20px;
            transition: color 0.5s ease;
        }

        .cover-pic {
            display: block;
            width: 100%;
            max-height: 180px;
            object-fit: cover;
            border-radius: 15px 15px 0 0;
            background-position: center;
        }

        .profile-image {
            margin-top: -60px;
        }

        .profile-image img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
        }

        .profile-info {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .profile-info p {
            font-size: 18px;
            color: #333;
            margin: 5px 0;
            transition: color 0.5s ease;
        }

        .profile-info p strong {
            color: #4e73df;
            transition: color 0.5s ease;
        }

        .bio-info {
            margin: 0 15px 20px;
            background-color: #e7e8dc;
            padding: 10px;
            border-radius: 15px;
            font-size: 14px;
            color: #333;
            transition: color 0.5s ease;
        }

        /* Light theme styles */
        .light {
            background-color: white;
            color: black;
        }

        /* Dark theme styles */
        .dark {
            background-color: black;
            color: white !important;
        }

        .profile-container.dark {
            background: #333;
            color: white;
        }

        .profile-container.dark .bio-info {
            background-color: #555;
        }

        .profile-container.dark p strong {
            color: yellow;
        }
    </style>
</head>

<body>
    <div class="profile-container toggle-button light" id="themeToggle">
        <!-- Cover Image -->
        <img src="<?php echo htmlspecialchars($imgupload); ?>" alt="Cover Image" class="cover-pic">

        <!-- Profile Image -->
        <div class="profile-image">
            <img src="<?php echo htmlspecialchars($imgupload); ?>" alt="Profile Image">
        </div>

        <!-- User Info -->
        <h1 id="welcomeText">Welcome, <?php echo htmlspecialchars($firstname); ?>!</h1>
        <div class="profile-info">
            <p id="usernameText"><strong><?php echo htmlspecialchars($firstname); ?></strong></p>
            <p id="userHandle"><b>Username:</b> <strong>@<?php echo htmlspecialchars($username); ?></strong></p>
        </div>

        <!-- Bio -->
        <div class="bio-info" id="bioText">
            <p><strong>Bio:</strong> <?php echo htmlspecialchars($bio); ?></p>
        </div>
    </div>

    <script>
        const toggleButton = document.getElementById('themeToggle');
        const welcomeText = document.getElementById('welcomeText');
        const usernameText = document.getElementById('usernameText');
        const userHandle = document.getElementById('userHandle');
        const bioText = document.getElementById('bioText');

        toggleButton.onclick = function () {
            if (toggleButton.classList.contains('light')) {
                toggleButton.classList.remove('light');
                toggleButton.classList.add('dark');
                // Update text colors for dark mode
                welcomeText.style.color = "white";
                usernameText.style.color = "white";
                userHandle.style.color = "white";
                bioText.style.color = "white";
            } else {
                toggleButton.classList.remove('dark');
                toggleButton.classList.add('light');
                // Update text colors for light mode
                welcomeText.style.color = "black";
                usernameText.style.color = "black";
                userHandle.style.color = "black";
                bioText.style.color = "black";
            }
        };
    </script>
</body>

</html>
