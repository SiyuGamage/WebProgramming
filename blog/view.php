<?php
require_once '../config/database.php';

$page_title = 'View Blog';
$css_path = '../assets/css/style.css';
$js_path = '../assets/js/main.js';
$home_path = '../index.php';
$login_path = '../auth/login.php';
$register_path = '../auth/register.php';
$logout_path = '../auth/logout.php';
$create_path = 'create.php';

// Get blog post ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$post_id = (int)$_GET['id'];

// Fetch blog post
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT bp.*, u.username FROM blogPost bp 
                       JOIN user u ON bp.user_id = u.id 
                       WHERE bp.id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../index.php");
    exit();
}

$post = $result->fetch_assoc();
$stmt->close();
$conn->close();

$page_title = $post['title'];

include '../includes/header.php';

// Simple Markdown to HTML conversion
function parseMarkdown($text) {
    // Headers
    $text = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $text);
    $text = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $text);
    $text = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $text);
    
    // Bold
    $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
    
    // Italic
    $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);
    
    // Links
    $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank">$1</a>', $text);
    
    // Line breaks and paragraphs
    $text = preg_replace('/\n\n/', '</p><p>', $text);
    $text = nl2br($text);
    
    // Lists
    $text = preg_replace('/^- (.+)$/m', '<li>$1</li>', $text);
    $text = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $text);
    
    // Wrap in paragraph if not starting with heading or list
    if (!preg_match('/^<h[1-6]>/', $text) && !preg_match('/^<ul>/', $text)) {
        $text = '<p>' . $text . '</p>';
    }
    
    return $text;
}
?>

<div class="blog-single">
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    
    <div class="blog-meta" style="margin-bottom: 2rem;">
        <span>👤 <?php echo htmlspecialchars($post['username']); ?></span>
        <span>📅 <?php echo date('F d, Y', strtotime($post['created_at'])); ?></span>
        <?php if ($post['updated_at'] != $post['created_at']): ?>
            <span>✏️ Updated: <?php echo date('F d, Y', strtotime($post['updated_at'])); ?></span>
        <?php endif; ?>
    </div>
    
    <hr style="margin: 1.5rem 0; border: none; border-top: 2px solid #f0f0f0;">
    
    <div class="blog-content">
        <?php echo parseMarkdown($post['content']); ?>
    </div>
    
    <div class="blog-actions">
        <a href="../index.php" class="btn btn-secondary">← Back to Home</a>
        
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
            <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn btn-primary">✏️ Edit</a>
            <form method="POST" action="delete.php?id=<?php echo $post['id']; ?>" style="display: inline;" 
                  onsubmit="return confirm('Are you sure you want to delete this blog post?\n\nThis action cannot be undone.');">
                <button type="submit" class="btn btn-danger">🗑️ Delete</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>