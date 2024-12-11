<?php
// Include database connection
include 'db_connection.php'; // Ensure this file contains $conn with a successful connection

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Get user email from session
$user_email = $_SESSION['email'];

// Handle password change form submission
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if new password matches confirmation
    if ($new_password !== $confirm_password) {
        echo "<div class='alert alert-danger'>New password and confirm password do not match.</div>";
    } else {
        // Fetch the current password from the database
        $sql = "SELECT password FROM register WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $stmt->bind_result($stored_password);
        $stmt->fetch();
        $stmt->close();

        // Verify if the current password matches the stored password
        if ($current_password === $stored_password) {
            // Update the password in the database (plain text password)
            $update_sql = "UPDATE register SET password = ? WHERE email = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $new_password, $user_email);
            $update_stmt->execute();
            $update_stmt->close();

            echo "<div class='alert alert-success'>Password updated successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Current password is incorrect.</div>";
        }
    }
}

// Handle account deletion
if (isset($_POST['delete_account'])) {
    $delete_sql = "DELETE FROM register WHERE email = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("s", $user_email);
    $delete_stmt->execute();
    $delete_stmt->close();

    // Destroy the session and log out
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f7f6;
        color: #333;
        margin: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
    h1, h2 {
        color: #2c3e50;
    }
    .card {
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: #fff;
        width: 100%;
        max-width: 800px;
        margin-bottom: 20px; /* Space between cards */
    }
    .btn-danger, .btn-warning {
        margin-top: 20px;
    }
    .alert {
        margin-top: 10px;
    }
    .modal-content {
        background-color: #ecf0f1;
    }
</style>

</head>
<body>

    <!-- Change Password Form -->
    <div class="card">
        <h2>Change Password</h2>
        <form method="POST">
            <div class="mb-3">
                <input type="password" name="current_password" class="form-control" placeholder="Current Password" required>
            </div>
            <div class="mb-3">
                <input type="password" name="new_password" class="form-control" placeholder="New Password" required>
            </div>
            <div class="mb-3">
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm New Password" required>
            </div>
            <button type="submit" name="change_password" class="btn btn-primary">Update Password</button>
        </form>
    </div>

    <!-- Delete Account Button -->
    <div class="card mt-4">
        <h2>Delete Account</h2>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete Account</button>
    </div>

    <!-- Modal for Account Deletion -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>This action is permanent and cannot be undone. Do you really want to delete your account?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST">
                        <button type="submit" name="delete_account" class="btn btn-danger">Yes, Delete Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
