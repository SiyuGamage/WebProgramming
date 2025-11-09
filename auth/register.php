<?php
require_once '../config/database.php';

$page_title = 'Register';
$css_path = '../assets/css/style.css';
$js_path = '../assets/js/main.js';
$home_path = '../index.php';
$login_path = 'login.php';
$register_path = 'register.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $conn = getDBConnection();
        
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM user WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Username or email already exists.';
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $success = 'Registration successful! Redirecting to login...';
                header("refresh:2;url=login.php");
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
        
        $stmt->close();
        $conn->close();
    }
}

include '../includes/header.php';
?>

<div class="auth-container">
    <h2>Create Account</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="" id="register-form">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required 
                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                   placeholder="Choose a username">
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required 
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                   placeholder="your@email.com">
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required 
                   placeholder="Create a strong password (min 6 characters)">
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required 
                   placeholder="Confirm your password">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
        
        <div class="auth-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </form>
</div>

<script>
    addPasswordStrengthIndicator('password');
</script>

<?php include '../includes/footer.php'; ?>