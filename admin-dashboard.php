<?php
session_start();
require 'db.php'; // include your PDO connection file

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

try {
    if ($user_role === 'admin') {
        // Admin sees all history
        $stmt = $pdo->prepare("SELECT * FROM scan_history ORDER BY created_at DESC");
        $stmt->execute();
    } else {
        // User sees only their history
        $stmt = $pdo->prepare("SELECT * FROM scan_history WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
    }

    $scan_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error fetching scan history: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VAPT Tool | Security Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.12.1/font/bootstrap-icons.min.css">
</head>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-shield-alt"></i> VAPT Tool</h3>
    </div>  
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link sidebar-link" data-target="codeAnalysis"><i class="fas fa-code"></i> Code Analysis</a>
        </li>
        <li class="nav-item">
            <a class="nav-link sidebar-link" data-target="vulnerabilities"><i class="fas fa-bug"></i> Vulnerabilities</a>
        </li>
        <li class="nav-item">
            <a class="nav-link sidebar-link" data-target="pageDetail"><i class="fas fa-tools"></i> Patch Details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link sidebar-link" data-target="history"><i class="fas fa-history"></i> History</a>
        </li>
        <li class="nav-item">
            <a class="nav-link sidebar-link" data-target="report"><i class="fas fa-file-alt"></i> Reports</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="manage_users.php"><i class="fas fa-user-cog"></i> Manage Users</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>
</div>



<!-- Main Content -->
<div class="main-content">
    <div class="header">
        <h2>Welcome Admin, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>
        <div class="user-profile">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name']) ?>&background=6c63ff&color=fff" alt="User">
            <span class="me-2"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="logout.php" class="btn btn-sm btn-outline-danger"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>

    <!-- Dashboard Section -->
    <div id="codeAnalysis" class="dashboard-card">
        <h4 class="mt-4"><i class="fas fa-upload"></i> Upload C/C++ Code</h4>
        <div class="file-upload">
            <i class="fas fa-cloud-upload-alt fa-2x"></i>
            <p>Drag & drop your C/C++ file here</p>
            <p class="text-muted">or</p>
            <button class="btn btn-primary" onclick="document.getElementById('file-input').click();">
                <i class="fas fa-folder-open"></i> Browse Files
            </button>
            <input type="file" id="file-input" style="display: none;" accept=".c,.cpp,.h,.hpp">
        </div>

        <div class="code-editor mt-4">
            <label for="code-input" class="form-label">Paste your C/C++ code here:</label>
            <textarea id="code-input" class="form-control" placeholder="Paste your C/C++ code here..."></textarea>
        </div>

        <div class="mt-3">
            <button class="btn btn-success" id="start-fuzzing-btn">
                <i class="fas fa-play"></i> Start Fuzzing Analysis
            </button>
        </div>

        <div class="progress-container mt-4">
            <div class="progress mb-2">
                <div class="progress-bar" id="progress-bar" role="progressbar" style="width: 0%"></div>
            </div>
            <div id="progress-text">Starting analysis...</div>
            <div class="progress-details row mt-2">
                <div class="col-md-4">
                    <i class="fas fa-code"></i> <strong>Lines scanned:</strong> <span id="lines-scanned">0</span>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-bug"></i> <strong>Vulnerabilities found:</strong> <span id="vulnerabilities-found">0</span>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-clock"></i> <strong>Time elapsed:</strong> <span id="time-elapsed">0s</span>
                </div>
            </div>
 
         <!-- Vulnerabilities Section -->
<div class="row page-section p-3 d-none" id="vulnerabilities">
    <!-- Header -->
    <div class="d-lg-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5">
        <h2 class="fw-bold mb-3">Detected Vulnerabilities</h2>
        <div class="d-lg-flex gap-2">
            <button class="btn btn-success mb-2">
                <i class="bi bi-patch-check-fill me-1"></i> Generate Patch
            </button>
            <button class="btn btn-primary mb-2">
                <i class="bi bi-download me-1"></i> Download Report
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <h5 class="fw-bold">Vulnerabilities Summary</h5>
    <div class="row">
        <div class="col-lg-3 col-6 mb-3">
            <div class="card border-danger shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-danger mb-2">Critical</h6>
                    <h4 class="fw-bold text-danger">5</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6 mb-3">
            <div class="card border-warning shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-warning mb-2">High</h6>
                    <h4 class="fw-bold text-warning">8</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6 mb-3">
            <div class="card border-primary shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-primary mb-2">Medium</h6>
                    <h4 class="fw-bold text-primary">12</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6 mb-3">
            <div class="card border-success shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-success mb-2">Low</h6>
                    <h4 class="fw-bold text-success">3</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Vulnerability Details -->
    <div class="col-12 mt-4">
        <div class="card shadow rounded-4">
            <div class="card-header bg-primary text-white fw-semibold">
                Vulnerability Details
            </div>
            <div class="card-body">
                <!-- Vulnerability: Buffer Overflow -->
                <div class="mb-4 border-start border-4 border-danger ps-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Buffer Overflow</h5>
                        <span class="badge bg-danger">Critical</span>
                    </div>
                    <p><strong>Location:</strong> main.cpp, line 45</p>
                    <p><strong>Description:</strong> Potential buffer overflow in the 'processInput' function when handling user input.</p>
                    <pre class="bg-light rounded px-3 py-2 text-danger"><code id="code1">char buffer[50];
gets(buffer); // Vulnerable line</code></pre>
                    <button class="btn btn-outline-primary btn-sm mt-2" onclick="copyCode('code1')">
                        <i class="bi bi-clipboard"></i> Copy Code
                    </button>
                </div>

                <!-- Vulnerability: Memory Leak -->
                <div class="mb-4 border-start border-4 border-warning ps-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Memory Leak</h5>
                        <span class="badge bg-warning text-dark">High</span>
                    </div>
                    <p><strong>Location:</strong> utils.cpp, line 112</p>
                    <p><strong>Description:</strong> Allocated memory not freed in the 'loadConfig' function.</p>
                    <pre class="bg-light rounded px-3 py-2 text-danger"><code id="code2">char* config = (char*)malloc(1024);
// Missing free(config)</code></pre>
                    <button class="btn btn-outline-primary btn-sm mt-2" onclick="copyCode('code2')">
                        <i class="bi bi-clipboard"></i> Copy Code
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Patch Progress Section (Initially Hidden) -->
    <div class="card shadow-lg rounded-4 mt-3 d-none" id="patchProgressCard">
        <div class="card-header bg-primary text-white fw-semibold">
            Patch Generation in Progress
        </div>
        <div class="card-body text-center">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="progress mb-3" style="height: 20px;">
                <div id="patchProgressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                     role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
            <p class="text-muted fw-semibold" id="patchStatusText"></p>
        </div>
    </div>
</div>
<!-- Patch Details Section -->
<div class="row page-section d-none p-3" id="pageDetail">
    <div class="d-lg-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-2 text-gray-800">Patch Details</h1>
        <div class="d-lg-flex gap-2">
            <button class="btn btn-primary mb-2" onclick="downloadPatch()">
                <i class="bi bi-download me-1"></i> Download Report
            </button>
            <button class="btn btn-primary mb-2" onclick="testOldCases()">
                Test with Old Cases
            </button>
            <button class="btn btn-primary mb-2" onclick="testNewCases()">
                Test with New Cases
            </button>
        </div>
    </div>

    <!-- Generated Patch Card -->
    <div class="card m-0 p-0">
        <div class="card-header">
            <h5 class="m-0 font-weight-bold">Generated Patch</h5>
        </div>
        <div class="card-body">
            <!-- Original Code -->
            <h6 class="font-weight-bold mb-3">Original Vulnerable Code</h6>
            <div class="code-block bg-light text-danger p-3 mb-3 rounded">
                <pre><code class="language-c" id="originalCode">
char buffer[50];
gets(buffer); // Vulnerable line
                </code></pre>
            </div>

            <!-- Patched Code -->
            <h6 class="font-weight-bold mb-3">Patched Code</h6>
            <div class="code-block bg-light text-success p-3 mb-3 rounded">
                <pre><code class="language-c" id="patchedCode">
char buffer[50];
if (fgets(buffer, sizeof(buffer), stdin) == NULL) {
    // Handle error
}
buffer[strcspn(buffer, "\n")] = '\0'; // Remove newline
                </code></pre>
            </div>

            <button class="btn btn-outline-primary mb-3 btn-sm" onclick="copyPatchedCode()">
                <i class="bi bi-check-circle"></i> Copy Patched Code
            </button>

            <!-- Patch Detail Summary -->
            <h5 class="font-weight-bold">Patch Details</h5>
            <ul class="patch-details list-unstyled mt-2">
                <li>✔ Replaced unsafe <code>gets()</code> with secure <code>fgets()</code></li>
                <li>✔ Added input length validation</li>
                <li>✔ Implemented input error handling</li>
                <li>✔ Removed potential newline character</li>
            </ul>
        </div>
    </div>
</div>

<div class="page-section p-3" id="history">
    <div class="content-wrapper">
        <h1 class="h3 mb-4 text-gray-800">Scan History</h1>
        <div class="card">
            <div class="card-header">
                <h5 class="m-0 font-weight-bold">Scan Records</h5>
            </div>
            <div class="card-body p-0">
                <div class="m-3">
                    <?php if (!empty($scan_results)) : ?>
                        <?php foreach ($scan_results as $scan) : 
                            // Safely get values or set defaults
                            $filename = htmlspecialchars($scan['filename'] ?? 'Unknown File');
                            $status = htmlspecialchars($scan['status'] ?? 'Unknown');
                            $date = htmlspecialchars($scan['created_at'] ?? 'Unknown Date');
                            $critical = (int)($scan['critical'] ?? 0);
                            $high = (int)($scan['high'] ?? 0);
                            $medium = (int)($scan['medium'] ?? 0);
                            $username = htmlspecialchars($scan['username'] ?? 'Unknown User');
                        ?>
                            <div class="card scan-card mb-4">
                                <div class="card-body">
                                    <div class="d-md-flex justify-content-between align-items-center">
                                        <h5 class="card-title"><?= $filename ?> Analysis</h5>
                                        <span class="status-badge <?= $status === 'Complete' ? 'bg-success' : 'bg-warning' ?> p-1 rounded text-white">
                                            <?= $status ?>
                                        </span>
                                    </div>
                                    <p class="text-muted mb-2"><strong>User:</strong> <?= $username ?></p>
                                    <p class="text-muted mb-2"><strong>Date:</strong> <?= $date ?></p>
                                    <p><strong>Findings:</strong> <?= $critical ?> Critical, <?= $high ?> High, <?= $medium ?> Medium</p>
                                    <a href="scan-details.php?id=<?= (int)$scan['id'] ?>" class="btn btn-sm btn-primary">View Details</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p class="text-muted">No scan records found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

               <!-- Report -->
<div class="row page-section d-none p-3" id="report">
    <div class="d-md-flex justify-content-between mb-3">
        <h3 class="mb-3">Generate Reports</h3>
        <button class="btn btn-primary" onclick="fullReport()">
            <i class="bi bi-file-earmark-text me-1"></i> Generate Full Report
        </button>
    </div>

    <!-- Vulnerability Report Card -->
    <div class="col-lg-6 mb-2">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Vulnerability Report
            </div>
            <div class="card-body">
                <p>Generate a detailed report of all vulnerabilities found in your code.</p>
                <div class="mb-4">
                    <label for="vulFormat" class="form-label">Select Format</label>
                    <select id="vulFormat" class="form-select format-selector">
                        <option selected>PDF</option>
                        <option>CSV</option>
                        <option>HTML</option>
                        <option>JSON</option>
                    </select>
                </div>
                <div class="d-grid">
                    <button class="btn btn-primary" onclick="downloadVul()">
                        <i class="bi bi-download me-1"></i> Download Vulnerability Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Patch Report Card -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                Patch Report
            </div>
            <div class="card-body">
                <p>Generate a report of all patches applied to your code.</p>
                <div class="mb-4">
                    <label for="patchFormat" class="form-label">Select Format</label>
                    <select id="patchFormat" class="form-select format-selector">
                        <option selected>PDF</option>
                        <option>CSV</option>
                        <option>HTML</option>
                        <option>JSON</option>
                    </select>
                </div>
                <div class="d-grid">
                    <button class="btn btn-success" onclick="downloadPatch()">
                        <i class="bi bi-download me-1"></i> Download Patch Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports Table -->
    <div class="col-12 mt-3">
        <div class="card">
            <div class="card-header bg-info text-white">Recent Reports</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Report Type</th>
                                <th>Format</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2023-11-15</td>
                                <td>Vulnerability Report</td>
                                <td>PDF</td>
                                <td>
                                    <button class="btn btn-outline-primary btn-sm" onclick="downloadVul()">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>2023-11-14</td>
                                <td>Patch Report</td>
                                <td>CSV</td>
                                <td>
                                    <button class="btn btn-outline-success btn-sm" onclick="downloadPatch()">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>2023-11-12</td>
                                <td>Full Report</td>
                                <td>PDF</td>
                                <td>
                                    <button class="btn btn-outline-info btn-sm" onclick="fullReport()">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const sections = document.querySelectorAll('.page-section');

            sections.forEach(section => {
                section.classList.add('d-none');
            });

            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.classList.remove('d-none');
                targetSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
</script>

</body>
</html>
