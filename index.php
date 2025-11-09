<?php
require_once 'config/database.php';

$page_title = 'Home';
$css_path = 'assets/css/style.css';
$js_path = 'assets/js/main.js';
$home_path = 'index.php';
$login_path = 'auth/login.php';
$register_path = 'auth/register.php';
$logout_path = 'auth/logout.php';
$create_path = 'blog/create.php';

include 'includes/header.php';

// Show delete messages
$delete_success = isset($_SESSION['delete_success']) ? $_SESSION['delete_success'] : '';
$delete_error = isset($_SESSION['delete_error']) ? $_SESSION['delete_error'] : '';
unset($_SESSION['delete_success']);
unset($_SESSION['delete_error']);

// Fetch all blog posts
$conn = getDBConnection();
$sql = "SELECT bp.*, u.username FROM blogPost bp 
        JOIN user u ON bp.user_id = u.id 
        ORDER BY bp.created_at DESC";
$result = $conn->query($sql);
?>

<div class="header-section">
    <h1>Welcome to BlogSpace</h1>
    <p>Share your thoughts, stories, and ideas with the world</p>
</div>

<?php if ($result->num_rows > 0): ?>
    <div class="blog-grid">
        <?php while($row = $result->fetch_assoc()): ?>
            <?php
            // Create excerpt (first 150 characters)
            $excerpt = substr(strip_tags($row['content']), 0, 150) . '...';
            $date = date('M d, Y', strtotime($row['created_at']));
            
            // Random emoji for each post
            $emojis = ['📝', '💡', '🚀', '✨', '📚', '🎯', '💻', '🌟'];
            $random_emoji = $emojis[array_rand($emojis)];
            ?>
            <div class="blog-card">
                <div class="blog-card-image"><?php echo $random_emoji; ?></div>
                <div class="blog-card-content">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <div class="blog-meta">
                        <span>👤 <?php echo htmlspecialchars($row['username']); ?></span>
                        <span>📅 <?php echo $date; ?></span>
                    </div>
                    <p class="blog-excerpt"><?php echo htmlspecialchars($excerpt); ?></p>
                    <div class="blog-card-actions">
                        <a href="blog/view.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Read More</a>
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']): ?>
                            <a href="blog/edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <h3>No blog posts yet</h3>
        <p>Be the first to share your story!</p>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="blog/create.php" class="btn btn-primary">Create Your First Blog</a>
        <?php else: ?>
            <a href="auth/register.php" class="btn btn-primary">Register to Create Blogs</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php
$conn->close();
include 'includes/footer.php';
?>