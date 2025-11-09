<?php
require_once '../config/database.php';
requireLogin();

$page_title = 'Edit Blog';
$css_path = '../assets/css/style.css';
$js_path = '../assets/js/main.js';
$home_path = '../index.php';
$logout_path = '../auth/logout.php';
$create_path = 'create.php';

// Get blog post ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$post_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Check if user owns this post
if (!ownsPost($user_id, $post_id)) {
    header("Location: ../index.php");
    exit();
}

$error = '';
$success = '';

// Fetch blog post
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM blogPost WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../index.php");
    exit();
}

$post = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    
    if (empty($title) || empty($content)) {
        $error = 'Both title and content are required.';
    } elseif (strlen($title) > 255) {
        $error = 'Title is too long (max 255 characters).';
    } else {
        $stmt = $conn->prepare("UPDATE blogPost SET title = ?, content = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("ssi", $title, $content, $post_id);
        
        if ($stmt->execute()) {
            $success = 'Blog post updated successfully! Redirecting...';
            $post['title'] = $title;
            $post['content'] = $content;
            header("refresh:2;url=view.php?id=$post_id");
        } else {
            $error = 'Failed to update blog post. Please try again.';
        }
    }
}

$stmt->close();
$conn->close();

include '../includes/header.php';
?>

<div class="editor-container">
    <h2>Edit Blog Post</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="" id="blog-form">
        <div class="form-group">
            <label for="title">Blog Title *</label>
            <input type="text" id="title" name="title" required 
                   value="<?php echo htmlspecialchars($post['title']); ?>"
                   placeholder="Enter an engaging title for your blog...">
        </div>
        
        <div class="form-group">
            <label for="content">Content (Markdown Supported) *</label>
            <textarea id="content" name="content" required placeholder="Write your blog content here...

You can use Markdown syntax:
# Heading 1
## Heading 2
### Heading 3

**bold text**
*italic text*

- List item 1
- List item 2

[Link text](https://example.com)"><?php echo htmlspecialchars($post['content']); ?></textarea>
        </div>
        
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <button type="submit" class="btn btn-primary">Update Blog</button>
            <a href="view.php?id=<?php echo $post_id; ?>" class="btn btn-secondary">Cancel</a>
            <form method="POST" action="delete.php?id=<?php echo $post_id; ?>" style="display: inline;" 
                  onsubmit="return confirm('Are you sure you want to delete this blog post?\n\nThis action cannot be undone.');">
                <button type="submit" class="btn btn-danger">Delete Blog</button>
            </form>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>