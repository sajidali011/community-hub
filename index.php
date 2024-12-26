<?php
include("db_connection.php");
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to view this page.'); window.location.href='login.php';</script>";
    exit;
}

// Get the logged-in user's email from the session
$user_email = $_SESSION['email'];

// Query to fetch the user's profile info
$sql = "SELECT firstname, imgupload FROM register WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $firstname = $row['firstname'];
    $profile_image = $row['imgupload'] ? $row['imgupload'] : 'img/undraw_profile.svg';
} else {
    $firstname = "Guest";
    $profile_image = 'img/undraw_profile.svg';
}
$stmt->close();

// Fetch the latest posts across all communities
$sql_recent_posts = "SELECT p.id AS post_id, p.content, p.image, p.created_at, r.firstname AS author, c.id AS community_id, c.name AS community_name
                     FROM posts p
                     JOIN register r ON p.user_id = r.id
                     JOIN communities c ON p.community_id = c.id
                     WHERE p.status = 'published'
                     ORDER BY p.created_at DESC
                     LIMIT 3"; // Fetch the 3 most recent posts
$result_recent_posts = $conn->query($sql_recent_posts);

$recent_posts = [];
if ($result_recent_posts->num_rows > 0) {
    while ($row = $result_recent_posts->fetch_assoc()) {
        $recent_posts[] = $row;
    }
}

// Fetch the total number of posts
$sql_total_posts = "SELECT COUNT(*) AS total_posts FROM posts WHERE status = 'published'";
$result_total_posts = $conn->query($sql_total_posts);
$total_posts = 0;

if ($result_total_posts && $row = $result_total_posts->fetch_assoc()) {
    $total_posts = $row['total_posts'];
}

// Close the database connection
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Community_hub</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
         footer {
            position: relative;
            background-color: #f1f1f1;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #ccc;
        }

        /* AI Icon Button */
        #chatIcon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            background-color: #0078d4; /* Button background color */
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        #chatIcon i {
            margin-right: 10px; /* Space between icon and text */
            font-size: 18px;
        }

        /* Chat iframe */
        #chatFrame {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 400px;
            height: 500px;
            border: none;
            display: none; /* Initially hidden */
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            z-index: 1000;
        }
       
        .posts-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .post-card {
            background-color: #fff;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.3s ease;
            height: 400px; /* Fixed height for the cards */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .post-card:hover {
            transform: translateY(-5px);
        }

        .post-card img,
        .post-card video {
            max-width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
        }

        .post-card h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .post-card p {
            color: #555;
            flex-grow: 1;
        }

        .post-card .timestamp {
            font-size: 12px;
            color: #999;
        }

        .post-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .post-card-header img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }
    </style>
  

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Community <sup>Hub</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
<hr class="sidebar-divider">

<!-- Nav Item - Global Profile -->
<li class="nav-item">
    <a class="nav-link" href="user_profile.php">
        <i class="fas fa-fw fa-user"></i>
        <span>Global Profile</span>
    </a>
</li>

<!-- Nav Item - Communities -->
<li class="nav-item">
    <a class="nav-link" href="my_community.php">
        <i class="fas fa-fw fa-users"></i>
        <span>My Communities</span>
    </a>
</li>

<!-- Nav Item - Browse Communities -->
<li class="nav-item">
    <a class="nav-link" href="community_list.php">
        <i class="fas fa-fw fa-search"></i>
        <span>Community List</span>
    </a>
</li>

<!-- Nav Item - Create Community -->
<li class="nav-item">
    <a class="nav-link" href="create_community.php">
        <i class="fas fa-fw fa-plus-circle"></i>
        <span>Create Community</span>
    </a>
</li>

<!-- Nav Item - Create Post -->
<li class="nav-item">
    <a class="nav-link" href="community_list.php">
        <i class="fas fa-fw fa-pencil-alt"></i>
        <span>Create Post</span>
    </a>
</li>

<!-- Nav Item - Notifications -->
<li class="nav-item">
    <a class="nav-link" href="#notifications">
        <i class="fas fa-fw fa-bell"></i>
        <span>Notifications</span>
    </a>
</li>

<!-- Nav Item - Search -->
<li class="nav-item">
    <a class="nav-link" href="#search">
        <i class="fas fa-fw fa-search"></i>
        <span>Search</span>
    </a>
</li>

<!-- Nav Item - Settings -->
<li class="nav-item">
    <a class="nav-link" href="settings.php">
        <i class="fas fa-fw fa-cogs"></i>
        <span>Settings</span>
    </a>
</li>
<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
    Addons
</div>

<!-- Nav Item - Manage Communities -->
<li class="nav-item">
    <a class="nav-link collapsed" href="manage_community.php" data-toggle="collapse" data-target="#collapseManage"
        aria-expanded="true" aria-controls="collapseManage">
        <i class="fas fa-fw fa-cogs"></i>
        <span>Manage Communities</span>
    </a>
    <div id="collapseManage" class="collapse" aria-labelledby="headingManage" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="community_profile.php">Edit Community Profile</a>
            <a class="collapse-item" href="#manageMembers">Manage Members</a>
            <a class="collapse-item" href="#communitySettings">Community Settings</a>
        </div>
    </div>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Nav Item - Account Settings -->
<li class="nav-item">
    <a class="nav-link" href="#accountSettings">
        <i class="fas fa-fw fa-user-cog"></i>
        <span>Account Settings</span>
    </a>
</li>

<!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
    class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search"
    action="search.php" method="get">
    <div class="input-group">
        <input type="text" name="query" class="form-control bg-light border-0 small" placeholder="Search for..."
            aria-label="Search" aria-describedby="basic-addon2">
        <div class="input-group-append">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-search fa-sm"></i>
            </button>
        </div>
    </div>
</form>


                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">1+</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 12, 2019</div>
                                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                    </div>
                                </a>
                               

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
                                <span class="badge badge-danger badge-counter">1</span>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
                                    Message Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_1.svg"
                                            alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div class="font-weight-bold">
                                        <div class="text-truncate">Hi there! I am wondering if you can help me with a
                                            problem I've been having.</div>
                                        <div class="small text-gray-500">Emily Fowler · 58m</div>
                                    </div>
                               

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                        

<!-- The Dropdown with dynamic data -->
<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($firstname); ?></span>
    <img class="img-profile rounded-circle" src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Image">
   </a>

                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="user_profile.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->
<!-- Begin Page Content --> 
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>
    <!-- Overview Cards Row -->
    <div class="row">
        <!-- Active Communities Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <h5 class="font-weight-bold text-primary">Active Communities</h5>
                            <p class="h5 mb-0 text-gray-800">15</p>
                        </div>
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Content Created Card -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="mr-3">
                    <h5 class="font-weight-bold text-success">Total Posts Created</h5>
                    <p class="h5 mb-0 text-gray-800"><?= htmlspecialchars($total_posts); ?></p>
                </div>
                <i class="fas fa-pencil-alt fa-2x text-success"></i>
            </div>
        </div>
    </div>
</div>


        <!-- User's Contributions Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <h5 class="font-weight-bold text-info">My Contributions</h5>
                            <p class="h5 mb-0 text-gray-800">12 Blogs, 5 Quizzes</p>
                        </div>
                        <i class="fas fa-edit fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- recent post -->
<div class="content">
    <h3>Recent Posts</h3>
    <div class="posts-container">
        <?php if (!empty($recent_posts)): ?>
            <?php foreach ($recent_posts as $post): ?>
                <div class="post-card" onclick="window.location.href='show_post.php?community_id=<?= $post['community_id'] ?>&post_id=<?= $post['post_id'] ?>'">
                    <div class="post-card-header">
                        <img src="<?= htmlspecialchars($profile_image) ?>" alt="User Profile Image">
                        <h4><?= htmlspecialchars($post['author']) ?> <br> Post Name is <?= htmlspecialchars($post['community_name']) ?></h4>
                    </div>
                    <p><?= htmlspecialchars(mb_substr(strip_tags($post['content']), 0, 100)) ?>...</p>
                    <div class="timestamp"><?= htmlspecialchars($post['created_at']) ?></div>
                    <?php if (!empty($post['image'])): ?>
                        <?php if (preg_match('/\.(mp4|webm|ogg)$/i', $post['image'])): ?>
                            <video controls>
                                <source src="<?= htmlspecialchars($post['image']) ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        <?php else: ?>
                            <img src="<?= htmlspecialchars($post['image']) ?>" alt="Post Image">
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No recent posts available.</p>
        <?php endif; ?>
    </div>
</div>

        <!-- User Profile & Community Management -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Profile & Community Management</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="user_profile.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-edit mr-2"></i>Edit Global Profile
                        </a>
                        <a href="community_list.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-users-cog mr-2"></i>Manage Communities
                        </a>
                        <a href="create_community.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-users mr-2"></i>Create New Community
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?php include "botpress_chatbot.php"
?>
        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>
</html>