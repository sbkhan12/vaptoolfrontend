<?php
$host = 'localhost';
$user = 'root';
$password = ''; 
$dbname = 'vapt_tool'; 

$conn = new mysqli($host, $user, $password, $dbname);
$error = '';
$success = '';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $email = $role = $securityQuestion = $securityAnswer = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['signup-name']);
    $email = trim($_POST['signup-email']);
    $password = $_POST['signup-password'];
    $confirmPassword = $_POST['signup-confirm-password'];
    $role = $_POST['signup-role'];
    $securityQuestion = $_POST['signup-security-question'];
    $securityAnswer = trim($_POST['signup-security-answer']);

    if ($password !== $confirmPassword) {
        $error = "❌ Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "⚠️ Email already exists. Please use another.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, security_question, security_answer) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $name, $email, $hashedPassword, $role, $securityQuestion, $securityAnswer);

            if ($stmt->execute()) {
                $success = "✅ Account created successfully. Please login.";
                header("refresh:2;url=index.php"); // Redirect after 2 seconds
            } else {
                $error = "❌ Error: " . $stmt->error;
            }

            $stmt->close();
        }

        $check->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VAPT Tool | Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.12.1/font/bootstrap-icons.min.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <i class="fas fa-user-plus"></i>
                <h2>Create Account</h2>
                <p>Join our security platform</p>
            </div>

            <!-- Display error and success messages -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form id="signupForm" method="post" action="">
                <div class="form-group">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" class="form-control" id="signup-name" name="signup-name" placeholder="Full Name" required value="<?php echo htmlspecialchars($name); ?>">
                </div>
                <div class="form-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" class="form-control" id="signup-email" name="signup-email" placeholder="Email address" required value="<?php echo htmlspecialchars($email); ?>">
                </div>
                <div class="form-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" class="form-control" id="signup-password" name="signup-password" placeholder="Password" required>
                    <small id="password-strength" class="form-text text-muted"></small>
                </div>
                <div class="form-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" class="form-control" id="signup-confirm-password" name="signup-confirm-password" placeholder="Confirm Password" required>
                </div>
                <div class="form-group">
                    <i class="bi bi-person-lines-fill input-icon"></i>
                    <select class="form-control" id="signup-role" name="signup-role" required>
                        <option value="">Select Role</option>
                        <option value="user" <?php if($role=="user") echo "selected"; ?>>User</option>
                        <option value="admin" <?php if($role=="admin") echo "selected"; ?>>Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <i class="fas fa-question input-icon"></i>
                    <select class="form-control" id="signup-security-question" name="signup-security-question" required>
                        <option value="">Select a security question</option>
                        <option value="pet" <?php if($securityQuestion=="pet") echo "selected"; ?>>What is your pet’s name?</option>
                        <option value="school" <?php if($securityQuestion=="school") echo "selected"; ?>>What is the name of your first school?</option>
                        <option value="city" <?php if($securityQuestion=="city") echo "selected"; ?>>In what city were you born?</option>
                        <option value="mother" <?php if($securityQuestion=="mother") echo "selected"; ?>>What is your mother's maiden name?</option>
                    </select>
                </div>
                <div class="form-group">
                    <i class="fas fa-reply input-icon"></i>
                    <input type="text" class="form-control" id="signup-security-answer" name="signup-security-answer" placeholder="Your answer" required value="<?php echo htmlspecialchars($securityAnswer); ?>">
                </div>
                <button type="submit" class="btn btn-auth">Sign Up</button>
                <div class="auth-footer">
                    Already have an account? <a href="index.php">Login</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('signup-password').addEventListener('input', function() {
    const password = this.value;
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    const strengthText = document.getElementById('password-strength');
    if (strength <= 2) {
        strengthText.innerText = 'Weak Password';
        strengthText.style.color = 'red';
    } else if (strength === 3 || strength === 4) {
        strengthText.innerText = 'Moderate Password';
        strengthText.style.color = 'orange';
    } else {
        strengthText.innerText = 'Strong Password';
        strengthText.style.color = 'green';
    }
});
</script>

</body>
</html>
