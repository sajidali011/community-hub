<?php
include "db_connection.php" ?>
<?php

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';
$users = [];
$communities = [];
$error = "";

// Handle search query
if (!empty($searchQuery)) {
    // Check if it's a username search
    $userSql = "SELECT username FROM register WHERE username LIKE ?";
    $stmt = $conn->prepare($userSql);
    $searchTerm = "%" . $searchQuery . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $userResult = $stmt->get_result();

    if ($userResult->num_rows > 0) {
        while ($row = $userResult->fetch_assoc()) {
            $users[] = $row['username'];
        }
    }

    // Check if it's a community search (only if users not found)
    if (empty($users)) {
        $communitySql = "SELECT * FROM communities WHERE name LIKE ? OR id LIKE ?";
        $stmt = $conn->prepare($communitySql);
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $communityResult = $stmt->get_result();

        if ($communityResult->num_rows > 0) {
            while ($row = $communityResult->fetch_assoc()) {
                $communities[] = $row;
            }
        }
    }

    // If no results found
    if (empty($users) && empty($communities)) {
        $error = "No results found for your search.";
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
    <title>Search Results</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h2 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #555;
        }

        .user-list,
        .community-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .user-item,
        .community-card {
            flex: 1 1 calc(30% - 20px);
            max-width: calc(30% - 20px);
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 15px;
        }

        .community-card:hover,
        .user-item:hover {
            transform: translateY(-5px);
        }

        .community-logo {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .no-results {
            text-align: center;
            color: #777;
            margin-top: 20px;
        }

        .user-item a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .user-item a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Search Results for: "<?= htmlspecialchars($searchQuery); ?>"</h1>

        <!-- Users Section -->
        <?php if (!empty($users)): ?>
            <div class="section">
                <h2>Users</h2>
                <div class="user-list">
                    <?php foreach ($users as $user): ?>
                        <div class="user-item">
                            <a href="Search_results_profile.php?username=<?= urlencode($user); ?>">
                                <?= htmlspecialchars($user); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Communities Section -->
        <?php if (!empty($communities)): ?>
            <div class="section">
                <h2>Communities</h2>
                <div class="community-list">
                    <?php foreach ($communities as $community): ?>
                        <div class="community-card">
                            <img src="<?= htmlspecialchars($community['logo']); ?>" alt="Community Logo" class="community-logo">
                            <div class="community-name"><?= htmlspecialchars($community['name']); ?></div>
                            <div class="community-description"><?= htmlspecialchars($community['description']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($users) && empty($communities)): ?>
            <p class="no-results"><?= $error; ?></p>
        <?php endif; ?>
    </div>
</body>

</html>
