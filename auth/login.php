<?php
require_once '../config/database.php';

$page_title = 'Login';
$css_path = '../assets/css/style.css';
$js_path = '../assets/js/main.js';
$home_path = '../index.php';
$login_path = 'login.php';
$register_path = 'register.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: ../index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id, username, password FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $email;
                
                header("Location: ../index.php");
                exit();
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

include '../includes/header.php';
?>

<div class="auth-container">
    <h2>Welcome Back!</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="" id="login-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required 
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                   placeholder="your@email.com">
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required 
                   placeholder="Enter your password">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        
        <div class="auth-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </form>
    
    <div style="margin-top: 2rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; font-size: 0.9rem;">
        <strong>Demo Account:</strong><br>
        Email: admin@blogspace.com<br>
        Password: admin123
    </div>
</div>

<?php include '../includes/footer.php'; ?>