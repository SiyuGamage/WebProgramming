<?php
require_once '../config/database.php';
requireLogin();

$page_title = 'Create Blog';
$css_path = '../assets/css/style.css';
$js_path = '../assets/js/main.js';
$home_path = '../index.php';
$logout_path = '../auth/logout.php';
$create_path = 'create.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];
    
    if (empty($title) || empty($content)) {
        $error = 'Both title and content are required.';
    } elseif (strlen($title) > 255) {
        $error = 'Title is too long (max 255 characters).';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO blogPost (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $content);
        
        if ($stmt->execute()) {
            $new_post_id = $stmt->insert_id;
            $success = 'Blog post created successfully! Redirecting...';
            header("refresh:2;url=view.php?id=$new_post_id");
        } else {
            $error = 'Failed to create blog post. Please try again.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

include '../includes/header.php';
?>

<div class="editor-container">
    <h2>Create New Blog Post</h2>
    
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
                   value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
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

[Link text](https://example.com)"><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
        </div>
        
        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">Publish Blog</button>
            <a href="../index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>