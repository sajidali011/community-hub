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

// Query to fetch data from the 'register' table using email
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
    $bio = $row['bio'] ? $row['bio'] : 'No Bio'; // Default if no bio
} else {
    echo "<script>alert('No data found for this user!'); window.location.href='login.php';</script>";
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
            padding: 20px;
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
            margin-bottom: 20px;
        }

        .profile-image img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #2575fc;
            margin-bottom: 20px;
        }

        .profile-info {
            margin-bottom: 20px;
        }

        .profile-info p {
            font-size: 18px;
            color: #333;
            margin: 5px 0;
        }

        .profile-info p strong {
            color: #4e73df;
        }

        .bio-info {
            
            margin-bottom: 20px;
            background-color:#e7e8dc;
            padding:7px;
            border-radius:15px;
        }

        .bio-info p {
            font-size: 14px;
            color: black;
            margin: 5px 0;
        }

        .edit-profile-btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #2575fc;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 25px;
            transition: background-color 0.3s ease;
            margin-right:15px;
        }

        .edit-profile-btn:hover {
            background-color: #6a11cb;
        }

        .cancel-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 12px 30px;
            background-color: #e0e0e0;
            color:#1E5BA1 ;
            font-size: 16px;
            border-radius: 25px;
            text-decoration: none;
        }

        .cancel-btn:hover {
            background-color: #c4c4c4;
        }

        .cover-pic{
            display: block;
            width: 100%;
            max-height:180px;
            object-fit:cover;
            background-position:center;
            border-radius:15px;
        }
        .image-pic{
           
            width: 140px;
            border-radius: 50%;
            margin-top: -70px;
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
    background: #333; /* Dark background */
    color: white; /* Text color in dark mode */
}

/* Additional styles for specific elements */
.profile-container.dark h1,.profile-container.dark strong,
.profile-container.dark p {
    color: white; /* Ensures all text within dark theme is white */
}

.profile-container h1,
.profile-container p {
    color: black; /* Ensures all text within light theme is black */
}
.profile-container.dark .bio-info {
    background-color: #555; /* Dark background for bio-info in dark mode */
}   
.profile-container.dark .profile-info strong{
    color:yellow;
}     
    </style>
</head>

<body >

    <div class="profile-container "class="toggle-button light" id="themeToggle">
        


        <h1>Welcome, <?php echo htmlspecialchars($firstname); ?>!</h1>

       
        <img src="<?php echo htmlspecialchars($imgupload); ?>" alt="Profile Image" class="cover-pic" >
        <div class="profile-image">
           
            <img src="<?php echo htmlspecialchars($imgupload); ?>" alt="Profile Image" class="image-pic" >
        </div>
        <div class="profile-info">
        <p><strong> <?php echo htmlspecialchars($firstname); ?></strong></p>
            <p><b>Username:</b> <strong> @<?php echo htmlspecialchars($username); ?></strong></p>
           
        </div>

        <div class="bio-info">
            <p><strong>Bio:</strong> <?php echo htmlspecialchars($bio); ?></p>
        </div>

        <a href="edit_profile.php" class="edit-profile-btn">Edit Profile</a>
        <a href="index.php" class="cancel-btn ">Back to Home</a>
    </div>
    
  

   
    <script>
    const toggleButton = document.getElementById('themeToggle');
    

    toggleButton.onclick = function() {
      
        if (toggleButton.classList.contains('light')) {
            toggleButton.classList.remove('light');
            toggleButton.classList.add('dark');
           
        } else {
            toggleButton.classList.remove('dark');
            toggleButton.classList.add('light');
            
        }
    };
</script>
</body>

</html>