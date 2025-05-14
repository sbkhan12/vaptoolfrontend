<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'vapt_tool';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed.");
}

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND verify_token = ?");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $update = $conn->prepare("UPDATE users SET is_verified = 1, verify_token = NULL WHERE email = ?");
        $update->bind_param("s", $email);
        $update->execute();
        echo "Email verified successfully. <a href='index.php'>Login</a>";
    } else {
        echo "Invalid or expired verification link.";
    }

    $stmt->close();
}

$conn->close();
?>
