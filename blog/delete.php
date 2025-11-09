<?php
require_once '../config/database.php';
requireLogin();

// Get blog post ID from either GET or POST
$post_id = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $post_id = (int)$_GET['id'];
} elseif (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $post_id = (int)$_POST['id'];
}

if (!$post_id) {
    $_SESSION['delete_error'] = 'Invalid blog post ID.';
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if user owns this post
if (!ownsPost($user_id, $post_id)) {
    $_SESSION['delete_error'] = 'You do not have permission to delete this blog post.';
    header("Location: ../index.php");
    exit();
}

// Delete the blog post
$conn = getDBConnection();
$stmt = $conn->prepare("DELETE FROM blogPost WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $post_id, $user_id);

if ($stmt->execute()) {
    $_SESSION['delete_success'] = 'Blog post deleted successfully!';
} else {
    $_SESSION['delete_error'] = 'Failed to delete blog post.';
}

$stmt->close();
$conn->close();

header("Location: ../index.php");
exit();
?>