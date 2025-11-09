<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - BlogSpace' : 'BlogSpace - Share Your Stories'; ?></title>
    <link rel="stylesheet" href="<?php echo isset($css_path) ? $css_path : '../assets/css/style.css'; ?>">
</head>
<body>
    <div class="container">
        <nav>
            <a href="<?php echo isset($home_path) ? $home_path : '../index.php'; ?>" class="logo">BlogSpace</a>
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo isset($home_path) ? $home_path : '../index.php'; ?>">Home</a>
                    <a href="<?php echo isset($create_path) ? $create_path : '../blog/create.php'; ?>">Create Blog</a>
                    <span style="color: #667eea;">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="<?php echo isset($logout_path) ? $logout_path : '../auth/logout.php'; ?>" class="btn btn-primary">Logout</a>
                <?php else: ?>
                    <a href="<?php echo isset($home_path) ? $home_path : '../index.php'; ?>">Home</a>
                    <a href="<?php echo isset($login_path) ? $login_path : '../auth/login.php'; ?>" class="btn btn-secondary">Login</a>
                    <a href="<?php echo isset($register_path) ? $register_path : '../auth/register.php'; ?>" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </div>
        </nav>