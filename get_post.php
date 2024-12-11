<?php
// get_post.php
include('db_connection.php');

if (isset($_GET['id'])) {
    $postId = (int) $_GET['id'];
    $query = "SELECT * FROM posts WHERE id = $postId";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $post = mysqli_fetch_assoc($result);
        echo json_encode($post);
    } else {
        echo json_encode(['error' => 'Post not found']);
    }
}
?>
