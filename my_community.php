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
    echo "<script>alert('Please log in to view your communities.'); window.location.href='login.php';</script>";
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

// Fetch user ID from the 'register' table
$user_query = "SELECT id FROM register WHERE email = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("s", $current_email);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();
$current_user_id = $user_data['id'];
$stmt->close();

// Fetch the communities the user has joined
$sql = "SELECT c.* FROM communities c
        JOIN user_communities uc ON c.id = uc.community_id
        WHERE uc.user_id = ? ORDER BY c.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$communities = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $communities[] = $row;
    }
}
$stmt->close();

// Handle Remove Community Request
if (isset($_POST['remove_community_id'])) {
    $community_id = $_POST['remove_community_id'];

    // Remove the community from user_communities
    $remove_query = "DELETE FROM user_communities WHERE user_id = ? AND community_id = ?";
    $stmt = $conn->prepare($remove_query);
    $stmt->bind_param("ii", $current_user_id, $community_id);

    if ($stmt->execute()) {
        echo "<script>
                Swal.fire({
                    title: 'Community Removed!',
                    text: 'You have successfully removed the community.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Remove the community card from the DOM
                        let communityCard = document.getElementById('community-' + " . $community_id . ");
                        communityCard.remove(); // Remove the card
                    }
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to remove the community. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
              </script>";
    }
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Communities</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            color: #333;
        }

        .community-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .community-card {
            flex: 1 1 calc(33.333% - 20px);
            max-width: calc(33.333% - 20px);
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .community-card:hover {
            transform: translateY(-5px);
        }

        .community-logo {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .community-details {
            padding: 15px;
        }

        .community-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .community-description {
            font-size: 14px;
            color: #555;
            margin-bottom: 15px;
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .community-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            border-top: 1px solid #ddd;
        }

        .privacy {
            font-size: 12px;
            color: #999;
        }

        .view-btn,
        .remove-btn {
            padding: 8px 16px;
            font-size: 14px;
            color: #fff;
            background: #28a745;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .view-btn:hover,
        .remove-btn:hover {
            background: #218838;
        }

        .remove-btn {
            background-color: #dc3545;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h1>My Communities</h1>
        </div>

        <div class="community-list">
            <?php if (count($communities) > 0): ?>
                <?php foreach ($communities as $community): ?>
                    <div class="community-card" id="community-<?= $community['id']; ?>">
                        <img src="<?= htmlspecialchars($community['logo']); ?>" alt="Community Logo" class="community-logo">
                        <div class="community-details">
                            <div class="community-name"><?= htmlspecialchars($community['name']); ?></div>
                            <div class="community-description"><?= htmlspecialchars($community['description']); ?></div>
                        </div>
                        <div class="community-footer">
                            <span class="privacy"><?= ucfirst($community['privacy']); ?> Community</span>
                            <a href="explore_community.php?community_id=<?= $community['id']; ?>" class="view-btn">View</a>
                            
                            <!-- Remove Button Form -->
                            <button type="button" class="remove-btn" onclick="confirmDelete(<?= $community['id']; ?>)">Remove</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You have not joined any communities yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function confirmDelete(communityId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this action!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form to remove the community
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'my_community.php';

                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'remove_community_id';
                    input.value = communityId;
                    form.appendChild(input);

                    document.body.appendChild(form);
                    form.submit();

                    // Remove the community card from the DOM without waiting for page reload
                    let communityCard = document.getElementById('community-' + communityId);
                    if (communityCard) {
                        communityCard.remove();
                    }
                }
            });
        }
    </script>

</body>

</html>
