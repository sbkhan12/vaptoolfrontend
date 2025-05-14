<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'vapt_tool';

$conn = new mysqli($host, $user, $password, $dbname);
$step = 1;
$error = '';
$success = '';
$email = '';
$securityQuestion = '';
$securityAnswerInput = '';

// Step 1: User submits email
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['reset-email'])) {
        $email = trim($_POST['reset-email']);
        $stmt = $conn->prepare("SELECT security_question FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($securityQuestion);
            $stmt->fetch();
            $step = 2;
        } else {
            $error = "⚠️ Email not found.";
        }

        $stmt->close();
    }

    // Step 2: User answers security question
    if (isset($_POST['security-answer'])) {
        $email = trim($_POST['email']);
        $securityAnswerInput = trim($_POST['security-answer']);

        $stmt = $conn->prepare("SELECT security_answer FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($correctAnswer);
        $stmt->fetch();

        if (strcasecmp($correctAnswer, $securityAnswerInput) === 0) {
            $step = 3;
        } else {
            $error = "❌ Incorrect answer. Please try again.";
            $step = 2;
        }

        $stmt->close();
    }

    // Step 3: User sets new password
    if (isset($_POST['new-password']) && isset($_POST['confirm-password'])) {
        $email = trim($_POST['email']);
        $newPassword = $_POST['new-password'];
        $confirmPassword = $_POST['confirm-password'];

        if ($newPassword !== $confirmPassword) {
            $error = "❌ Passwords do not match.";
            $step = 3;
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashedPassword, $email);
            if ($stmt->execute()) {
                $success = "✅ Password reset successful. You can now <a href='index.php'>login</a>.";
                $step = 4;
            } else {
                $error = "❌ Error updating password.";
                $step = 3;
            }
            $stmt->close();
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VAPT Tool | Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <i class="fas fa-unlock-alt"></i>
                <h2>Reset Password</h2>
                <p>Secure your account</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if ($step === 1): ?>
                <form method="post">
                    <div class="form-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="reset-email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    <button type="submit" class="btn btn-auth">Next</button>
                </form>
            <?php elseif ($step === 2): ?>
                <form method="post">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <div class="form-group">
                        <label><strong>Security Question:</strong> <?php echo htmlspecialchars($securityQuestion); ?></label>
                        <input type="text" name="security-answer" class="form-control" placeholder="Your answer" required value="<?php echo htmlspecialchars($securityAnswerInput); ?>">
                    </div>
                    <button type="submit" class="btn btn-auth">Verify</button>
                </form>
            <?php elseif ($step === 3): ?>
                <form method="post">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <div class="form-group">
                        <input type="password" name="new-password" class="form-control" placeholder="New Password" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password" required>
                    </div>
                    <button type="submit" class="btn btn-auth">Reset Password</button>
                </form>
            <?php endif; ?>

            <?php if ($step !== 4): ?>
                <div class="auth-footer mt-3">
                    Remembered your password? <a href="index.php">Login</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
