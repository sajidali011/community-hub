<?php
include "db_connection.php" ?>
<?php
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

// Get the search query if any
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$filter_category = isset($_GET['category']) ? $_GET['category'] : null;

// Build the query based on search and category filters
if ($filter_category && $search_query) {
    $sql = "SELECT * FROM communities WHERE category = ? AND (name LIKE ? OR description LIKE ?) ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $search_query = "%$search_query%";
    $stmt->bind_param("sss", $filter_category, $search_query, $search_query);
} elseif ($filter_category) {
    $sql = "SELECT * FROM communities WHERE category = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $filter_category);
} elseif ($search_query) {
    $sql = "SELECT * FROM communities WHERE name LIKE ? OR description LIKE ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $search_query = "%$search_query%";
    $stmt->bind_param("ss", $search_query, $search_query);
} else {
    $sql = "SELECT * FROM communities ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
}
$stmt->execute();
$result = $stmt->get_result();

$communities = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $communities[] = $row;
    }
}
$stmt->close();

// Fetch all categories for the dropdown
$categories = [];
$sql = "SELECT DISTINCT category FROM communities";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $categories[] = $row['category'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community List</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
   <link href="community_list.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Communities</h1>
        <form method="GET" action="">
            <div class="search-box">
                <!-- Only the category dropdown is kept here -->
                <select name="category" class="form-select category-select" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category); ?>" <?= $filter_category === $category ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <div class="community-list">
        <?php if (count($communities) > 0): ?>
            <?php foreach ($communities as $community): ?>
                <div class="community-card">
                    <img src="<?= htmlspecialchars($community['logo']); ?>" alt="Community Logo" class="community-logo">
                    <div class="community-details">
                        <div class="community-name"><?= htmlspecialchars($community['name']); ?></div>
                        <div class="community-description"><?= htmlspecialchars($community['description']); ?></div>
                    </div>
                    <div class="community-footer">
                        <span class="privacy"><?= ucfirst($community['privacy']); ?> Community</span>
                        <form method="POST" action="join_community.php" style="display: inline;">
                            <input type="hidden" name="community_id" value="<?= $community['id']; ?>">
                            <button type="submit" class="join-btn" style="margin-left: 10px;">Join</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No communities found.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>
    // Apply select2 to category dropdown
    $(document).ready(function() {
        $('.category-select').select2({
            placeholder: "Select a category",
            allowClear: true
        });
    });
</script>

</body>
</html>
