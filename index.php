<?php
session_start();

// DB connection setup
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'vapt_tool';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login-email'])) {

    // CSRF token check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $email = trim($_POST['login-email']);
    $password = $_POST['login-password'];
    $role = $_POST['login-role'];
    $securityQuestion = $_POST['login-security-question'];
    $securityAnswer = trim($_POST['login-security-answer']);

    // Input validation (basic sanitization)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id, name, password, role, security_question, security_answer FROM users WHERE email = ? AND role = ?");
        $stmt->bind_param("ss", $email, $role);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Check security question and answer
            if ($user['security_question'] === $securityQuestion && strcasecmp($user['security_answer'], $securityAnswer) === 0) {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = $user['role'];

                    // Redirect by role
                    header("Location: " . ($user['role'] === 'admin' ? "admin-dashboard.php" : "user-dashboard.php"));
                    exit();
                } else {
                    $error = "Incorrect password.";
                }
            } else {
                $error = "Incorrect security question or answer.";
            }
        } else {
            $error = "No account found with the provided email and role.";
        }

        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VAPT Tool | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header text-center">
                <i class="fas fa-shield-alt fa-2x"></i>
                <h2>VAPT Tool Login</h2>
                <p>Secure vulnerability assessment platform</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                <div class="form-group mb-3">
                    <input type="email" class="form-control" name="login-email" placeholder="Email address" required>
                </div>
                <div class="form-group mb-3">
                    <input type="password" class="form-control" name="login-password" placeholder="Password" required>
                </div>
                <div class="form-group mb-3">
                    <select class="form-control" name="login-role" required>
                        <option value="">Select Role</option>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <select class="form-control" name="login-security-question" required>
                        <option value="">Select your security question</option>
                        <option value="pet">What is your pet’s name?</option>
                        <option value="school">What is the name of your first school?</option>
                        <option value="city">In what city were you born?</option>
                        <option value="mother">What is your mother's maiden name?</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <input type="text" class="form-control" name="login-security-answer" placeholder="Answer to your security question" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>

                <div class="text-center mt-3">
                    <a href="forgot-password.php">Forgot Password?</a>
                </div>
                <div class="text-center mt-2">
                    Don’t have an account? <a href="signup.php">Sign up</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
