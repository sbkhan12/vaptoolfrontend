<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

require 'db_connection.php'; // Make sure this file defines $pdo

$scan_id = $_GET['id'] ?? null;
if (!$scan_id) {
    echo "Invalid scan ID.";
    exit;
}

$query = $role === 'admin'
    ? "SELECT * FROM scan_history WHERE id = ?"
    : "SELECT * FROM scan_history WHERE id = ? AND user_id = ?";

$stmt = $pdo->prepare($query);
$params = $role === 'admin' ? [$scan_id] : [$scan_id, $user_id];
$stmt->execute($params);
$scan = $stmt->fetch();

if (!$scan) {
    echo "<h3>Access Denied or Scan Not Found</h3>";
    exit;
}

// Fetch vulnerabilities
$vulStmt = $pdo->prepare("SELECT * FROM scan_vulnerabilities WHERE scan_id = ?");
$vulStmt->execute([$scan_id]);
$vulnerabilities = $vulStmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scan Details</title>
    <link href="bootstrap.css" rel="stylesheet">
</head>
<body class="p-4">
    <h2><?= htmlspecialchars($scan['filename']) ?> – Scan Details</h2>
    <p><strong>Status:</strong> <?= $scan['status'] ?> | <strong>Date:</strong> <?= $scan['scan_date'] ?></p>
    <p><strong>Findings Summary:</strong> <?= $scan['findings'] ?></p>

    <hr>

    <?php foreach ($vulnerabilities as $vul): ?>
        <?php
            // Determine border class based on severity
            switch ($vul['severity']) {
                case 'Critical':
                    $borderClass = 'border-danger';
                    break;
                case 'High':
                    $borderClass = 'border-warning';
                    break;
                case 'Medium':
                    $borderClass = 'border-primary';
                    break;
                case 'Low':
                    $borderClass = 'border-success';
                    break;
                default:
                    $borderClass = 'border-secondary';
            }

            // Determine badge class based on severity
            switch ($vul['severity']) {
                case 'Critical':
                    $badgeClass = 'bg-danger';
                    break;
                case 'High':
                    $badgeClass = 'bg-warning text-dark';
                    break;
                case 'Medium':
                    $badgeClass = 'bg-primary';
                    break;
                case 'Low':
                    $badgeClass = 'bg-success';
                    break;
                default:
                    $badgeClass = 'bg-secondary';
            }
        ?>
        <div class="mb-4 border-start border-4 ps-3 <?= $borderClass ?>">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0"><?= htmlspecialchars($vul['title']) ?></h5>
                <span class="badge <?= $badgeClass ?>">
                    <?= $vul['severity'] ?>
                </span>
            </div>
            <p><strong>Location:</strong> <?= htmlspecialchars($vul['file_location']) ?>, line <?= $vul['line_number'] ?></p>
            <p><?= htmlspecialchars($vul['description']) ?></p>
            <pre class="bg-light rounded px-3 py-2 text-danger"><code><?= htmlspecialchars($vul['code_snippet']) ?></code></pre>
        </div>
    <?php endforeach; ?>
</body>
</html>
