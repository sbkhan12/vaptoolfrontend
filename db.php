<?php
$dsn = "mysql:host=localhost;dbname=vapt_tool;charset=utf8mb4";
$username = "root";
$password = ""; 

try {
    $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
