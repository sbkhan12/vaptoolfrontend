document.getElementById('show-signup').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('login-form').style.display = 'none';
        document.getElementById('signup-form').style.display = 'block';
    });
    
    document.getElementById('show-login').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('signup-form').style.display = 'none';
        document.getElementById('login-form').style.display = 'block';
    });

    // Form submissions
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('login-email').value;
        
        // Store user info in dashboard
        document.getElementById('user-email').textContent = email;
        document.getElementById('user-avatar').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(email.split('@')[0])}&background=6c63ff&color=fff`;
        
        // Show dashboard and hide auth page
        document.getElementById('auth-page').style.display = 'none';
        document.getElementById('dashboard-page').style.display = 'block';
        
        console.log('Login successful for:', email);
    });
    
    document.getElementById('signupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('signup-name').value;
        const email = document.getElementById('signup-email').value;
        
        // Store user info in dashboard
        document.getElementById('user-email').textContent = email;
        document.getElementById('user-avatar').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=6c63ff&color=fff`;
        
        // Show dashboard and hide auth page
        document.getElementById('auth-page').style.display = 'none';
        document.getElementById('dashboard-page').style.display = 'block';
        
        console.log('Account created for:', name, email);
    });

    //forgot password
    const loginFormDiv = document.getElementById("login-form");
        const forgotForm = document.getElementById("forgot-password-form");

        document.getElementById("forgot-password").addEventListener("click", function () {
            loginFormDiv.classList.add("d-none");
            forgotForm.style.display = "block";
        });

        document.getElementById("back-to-login").addEventListener("click", function (e) {
            e.preventDefault();
            forgotForm.style.display = "none";
            loginFormDiv.classList.remove("d-none");
        });

    // Sidebar Navigation Logic
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            // Don't prevent default for logout button
            if (!this.id.includes('logout')) {
                e.preventDefault();
            }
    
            // Get target section ID
            const targetId = this.getAttribute('href')?.substring(1) || '';
            
            // Only proceed if this isn't the logout button
            if (targetId) {
                // Remove 'active' and 'active-link' from all nav-links
                document.querySelectorAll('.nav-link').forEach(nav => {
                    nav.classList.remove('active', 'active-link');
                });
        
                // Add both classes to the clicked link
                this.classList.add('active', 'active-link');
        
                // Show the corresponding section
                document.querySelectorAll('.page-section').forEach(section => {
                    section.classList.add('d-none');
                });
        
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.classList.remove('d-none');
                }
            }
        });
    });

    //sidebar toggle btn
    function btnToggle() {
        const sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('active');
    }

    function closeSidebar() {
        document.querySelector('.sidebar').classList.remove('active');
    }


    // Close sidebar on nav item click (only on mobile)
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 768) {
                document.querySelector('.sidebar').classList.remove('active');
            }
        });
    });

    // File upload functionality
    const fileInput = document.getElementById('file-input');
    const browseBtn = document.getElementById('browse-btn');
    const fileUploadArea = document.getElementById('file-upload-area');
    const uploadText = fileUploadArea.querySelector('p:first-of-type');
    const codeInput = document.getElementById('code-input');
    
    // Handle browse button click
    browseBtn.addEventListener('click', () => fileInput.click());
    
    // Handle file selection
    fileInput.addEventListener('change', function(e) {
        if (this.files.length > 0) {
            const file = this.files[0];
            const fileName = file.name;
            
            // Check if file is C/C++
            if (!fileName.match(/\.(c|cpp|h|hpp)$/i)) {
                alert('Please upload a C/C++ file (.c, .cpp, .h, .hpp)');
                return;
            }
            
            // Update UI
            uploadText.textContent = fileName;
            uploadText.style.fontWeight = '600';
            uploadText.style.color = '#2d3748';
            fileUploadArea.style.borderColor = 'var(--primary-color)';
            fileUploadArea.style.backgroundColor = 'rgba(108, 99, 255, 0.05)';
            
            // Read file content
            const reader = new FileReader();
            reader.onload = function(e) {
                codeInput.value = e.target.result;
            };
            reader.readAsText(file);
        }
    });
    
    // Handle drag and drop
    fileUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileUploadArea.style.borderColor = 'var(--primary-color)';
        fileUploadArea.style.backgroundColor = 'rgba(108, 99, 255, 0.1)';
    });
    
    fileUploadArea.addEventListener('dragleave', () => {
        fileUploadArea.style.borderColor = '#e2e8f0';
        fileUploadArea.style.backgroundColor = '#f8fafc';
    });
    
    fileUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        fileUploadArea.style.borderColor = '#e2e8f0';
        fileUploadArea.style.backgroundColor = '#f8fafc';
        
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            const event = new Event('change');
            fileInput.dispatchEvent(event);
        }
    });
    
    // Start fuzzing button
    document.getElementById('start-fuzzing-btn').addEventListener('click', function() {
        const code = codeInput.value;
        const file = fileInput.files[0];
        
        if (!file && !code.trim()) {
            alert('Please upload a C/C++ file or paste code to analyze');
            return;
        }
        
        // Show loading state
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';
        this.disabled = true;
        
        // Show progress bar
        const progressContainer = document.getElementById('progress-container');
        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-text');
        const progressDetails = document.getElementById('progress-details');
        
        progressContainer.style.display = 'block';
        progressDetails.style.display = 'none';
        
        // Simulate analysis with progress
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.floor(Math.random() * 10) + 1;
            if (progress > 100) progress = 100;
            
            progressBar.style.width = `${progress}%`;
            
            if (progress < 30) {
                progressText.textContent = 'Initializing analysis...';
            } else if (progress < 60) {
                progressText.textContent = 'Scanning code for vulnerabilities...';
            } else if (progress < 90) {
                progressText.textContent = 'Validating potential issues...';
            } else {
                progressText.textContent = 'Finalizing report...';
            }
            
            // Update details
            document.getElementById('lines-scanned').textContent = Math.floor(progress * 50);
            document.getElementById('vulnerabilities-found').textContent = Math.floor(progress / 10);
            document.getElementById('time-elapsed').textContent = `${(progress / 20).toFixed(1)}s`;
            
            if (progress === 100) {
                clearInterval(interval);
                progressText.textContent = 'Analysis complete!';
                progressDetails.style.display = 'block';

                // Hide current page section
                document.getElementById("codeAnalysis").classList.add("d-none");
          
                // Show Vulnerabilities section
                document.getElementById("volnerabilities").classList.remove("d-none");
          
                // Update Sidebar
                const vulnNavItem = document.getElementById("vulnNav");
                vulnNavItem.classList.remove("d-none");
          
                // Remove active-link from all sidebar links
                document.querySelectorAll(".sidebar .nav-link").forEach(link => {
                    link.classList.remove("active", "active-link");
                });
          
                // Add active-link to vulnerabilities nav
                vulnNavItem.querySelector(".nav-link").classList.add("active", "active-link");
          
                // Optional scroll effect
                document.getElementById("volnerabilities").scrollIntoView({ behavior: "smooth" });
          
                // Reset button
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-play"></i> Start Fuzzing Analysis';
                    this.disabled = false;
                }, 1000);
            }
        }, 200);
    });

    // Copy code functionality
    document.querySelectorAll('.btn-outline-primary.btn-sm').forEach(button => {
        button.addEventListener('click', function() {
            const codeBlock = this.parentElement.querySelector('pre code');
            if (codeBlock) {
                navigator.clipboard.writeText(codeBlock.innerText)
                    .then(() => {
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="bi bi-check"></i> Copied!';
                        setTimeout(() => {
                            this.innerHTML = originalText;
                        }, 2000);
                    })
                    .catch(err => {
                        console.error('Failed to copy text: ', err);
                    });
            }
        });
    });

    function generatePatch() {
        const progressCard = document.getElementById("patchProgressCard");
        const progressBar = document.getElementById("patchProgressBar");
        const statusText = document.getElementById("patchStatusText");
        
        const messages = [
            "Analyzing vulnerabilities...",
            "Generating patch candidates...",
            "Validating patches..."
        ];
        
        progressCard.classList.remove("d-none");
        
        // Scroll to progress card
        progressCard.scrollIntoView({ behavior: "smooth", block: "center" });
        
        let progress = 0;
        let messageIndex = 0;
        
        const interval = setInterval(() => {
            progress += 5;
            progressBar.style.width = progress + "%";
            progressBar.setAttribute("aria-valuenow", progress);
    
            if (progress >= (messageIndex + 1) * 33 && messageIndex < messages.length) {
                statusText.textContent = messages[messageIndex];
                messageIndex++;
            }
    
            if (progress >= 100) {
                clearInterval(interval);
                
                document.getElementById("volnerabilities").classList.add("d-none");
                document.getElementById("pageDetail").classList.remove("d-none");
    
                const patchNavItem = document.getElementById("patchDetail");
                patchNavItem.classList.remove("d-none");
    
                document.querySelectorAll(".sidebar .nav-link").forEach(link => {
                    link.classList.remove("active", "active-link");
                });
                patchNavItem.querySelector(".nav-link").classList.add("active", "active-link");
    
                document.getElementById("pageDetail").scrollIntoView({ behavior: "smooth" });
            }
        }, 300);
    }


    //download Vulnerabilities page
    async function downloadVul() {
        const vulnerabilities = document.querySelectorAll('.card-body > div');

        // jsPDF init
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        let y = 20;

        doc.setFont("courier", "normal");
        doc.setFontSize(12);
        doc.text("🔐 Vulnerability Report", 10, 10);

        vulnerabilities.forEach((vul, index) => {
            const titleEl = vul.querySelector('h5');
            const severityEl = vul.querySelector('.badge');
            const locationEl = vul.querySelector('p:nth-of-type(1)');
            const descEl = vul.querySelector('p:nth-of-type(2)');
            const codeEl = vul.querySelector('code');

            // Skip if any essential element is missing
            if (!(titleEl && severityEl && locationEl && descEl && codeEl)) return;

            const title = titleEl.textContent.trim();
            const severity = severityEl.textContent.trim();
            const location = locationEl.textContent.trim();
            const description = descEl.textContent.trim();
            const code = codeEl.innerText.trim();

            const entry = `#${index + 1}: ${title}\nSeverity: ${severity}\n${location}\n${description}\nCode:\n${code}\n\n${'-'.repeat(40)}\n\n`;

            const lines = doc.splitTextToSize(entry, 180);
            doc.text(lines, 10, y);
            y += lines.length * 7;

            // Start new page if near bottom
            if (y > 270) {
                doc.addPage();
                y = 20;
            }
        });

        doc.save("vulnerability-report.pdf");
    }
    
    // Report download functionality
    function downloadReport(type, format) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        let content = '';
        let title = '';
        
        if (type === 'vulnerability') {
            title = 'Vulnerability Report';
            content = 'This report contains all detected vulnerabilities in your code.\n\n';
            
            // Add vulnerabilities summary
            content += 'Vulnerabilities Summary:\n';
            content += 'Critical: 5\n';
            content += 'High: 8\n';
            content += 'Medium: 12\n';
            content += 'Low: 3\n\n';
            
            // Add vulnerability details
            content += 'Vulnerability Details:\n';
            content += '1. Buffer Overflow (Critical)\n';
            content += '   Location: main.cpp, line 45\n';
            content += '2. Memory Leak (High)\n';
            content += '   Location: utils.cpp, line 112\n';
        } else {
            title = 'Patch Report';
            content = 'This report contains all generated patches for your code.\n\n';
            
            // Add patch details
            content += 'Patch Details:\n';
            content += '1. Buffer Overflow Fix\n';
            content += '   - Replaced gets() with fgets()\n';
            content += '   - Added input length validation\n';
            content += '2. Memory Leak Fix\n';
            content += '   - Added free() for allocated memory\n';
        }
        
        doc.setFont("helvetica");
        doc.setFontSize(12);
        doc.text(title, 14, 10);
        doc.text(content, 14, 20);
        
        if (format === 'pdf') {
            doc.save(`${title.toLowerCase().replace(' ', '_')}.pdf`);
        } else if (format === 'csv') {
            // For CSV we'll create a simple download
            const csvContent = "data:text/csv;charset=utf-8," + content.replace(/\n/g, ',');
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `${title.toLowerCase().replace(' ', '_')}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } else {
            // For other formats, just show a message
            alert(`${title} would be downloaded in ${format.toUpperCase()} format in a real application`);
        }
    }
    
    // Set up report download buttons
    document.querySelectorAll('.btn-primary').forEach(btn => {
        if (btn.textContent.includes('Download Vulnerability Report')) {
            btn.addEventListener('click', () => {
                const format = btn.closest('.card-body').querySelector('.format-selector').value;
                downloadReport('vulnerability', format.toLowerCase());
            });
        }
    });
    
    document.querySelectorAll('.btn-success').forEach(btn => {
        if (btn.textContent.includes('Download Vulnerability Report')) {
            btn.addEventListener('click', () => {
                const format = btn.closest('.card-body').querySelector('.format-selector').value;
                downloadReport('patch', format.toLowerCase());
            });
        }
    });

    //download patchDetails
    function downloadPatch() {
        // Get jsPDF instance
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
    
        // Get the original and patched code
        const originalCode = document.getElementById('originalCode').innerText;
        const patchedCode = document.getElementById('patchedCode').innerText;
    
        // Get patch details
        const patchDetails = document.querySelectorAll('.patch-details ul li');
        let patchDetailsText = '';
        patchDetails.forEach((item, index) => {
            patchDetailsText += `${index + 1}. ${item.innerText}\n`;
        });
    
        // Add content to PDF
        doc.setFont("courier");
        doc.setFontSize(10);
    
        // Add title
        doc.text('Generated Patch Report', 14, 10);
        doc.text('------------------------', 14, 12);
    
        // Original Code
        doc.text('Original Vulnerable Code:', 14, 20);
        doc.text(originalCode, 14, 30);
    
        // Patched Code
        doc.text('Patched Code:', 14, 50);
        doc.text(patchedCode, 14, 60);
    
        // Patch Details
        doc.text('Patch Details:', 14, 90);
        doc.text(patchDetailsText, 14, 100);
    
        // Download the PDF
        doc.save('patch_report.pdf');
    }

    //patch Detail to Vulnerabilities page move
    function testNew() {
        const vulnNav = document.getElementById("vulnNav");
    
        // Show the #volnerabilities section
        document.getElementById("volnerabilities").classList.remove("d-none");
        document.getElementById("pageDetail").classList.add("d-none");
    
        // Update sidebar active link
        document.querySelectorAll(".sidebar .nav-link").forEach(link => {
            link.classList.remove("active", "active-link");
        });
        vulnNav.querySelector(".nav-link").classList.add("active", "active-link");
    
        // Scroll to the section
        document.getElementById("volnerabilities").scrollIntoView({ behavior: "smooth" });
    }


    //Download full report in report section
    async function fullReport() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        let y = 20;

        doc.setFont("courier", "normal");
        doc.setFontSize(12);
        doc.text("🔐 Full Vulnerability & Patch Report", 10, 10);

        // ---------------- VULNERABILITY DETAILS ----------------
        const vulnerabilities = document.querySelectorAll('.card-body > div');

        vulnerabilities.forEach((vul, index) => {
            const titleEl = vul.querySelector('h5');
            const severityEl = vul.querySelector('.badge');
            const locationEl = vul.querySelector('p:nth-of-type(1)');
            const descEl = vul.querySelector('p:nth-of-type(2)');
            const codeEl = vul.querySelector('code');

            if (!(titleEl && severityEl && locationEl && descEl && codeEl)) return;

            const title = titleEl.textContent.trim();
            const severity = severityEl.textContent.trim();
            const location = locationEl.textContent.trim();
            const description = descEl.textContent.trim();
            const code = codeEl.innerText.trim();

            const section = `#${index + 1}: ${title}\nSeverity: ${severity}\n${location}\n${description}\nCode:\n${code}\n\n${'-'.repeat(40)}\n\n`;

            const lines = doc.splitTextToSize(section, 180);
            doc.text(lines, 10, y);
            y += lines.length * 7;

            if (y > 270) {
                doc.addPage();
                y = 20;
            }
        });

        // ---------------- PATCH DETAILS ----------------
        const originalCode = document.getElementById('originalCode')?.innerText.trim();
        const patchedCode = document.getElementById('patchedCode')?.innerText.trim();
        const patchPoints = Array.from(document.querySelectorAll('.patch-details ul li')).map(li => `• ${li.innerText.trim()}`);

        if (originalCode && patchedCode) {
            const patchSection = `Patch Section:\n\nOriginal Code:\n${originalCode}\n\nPatched Code:\n${patchedCode}\n\nPatch Notes:\n${patchPoints.join('\n')}\n`;

            const patchLines = doc.splitTextToSize(patchSection, 180);
            if (y > 270 - patchLines.length * 7) {
                doc.addPage();
                y = 20;
            }

            doc.text(patchLines, 10, y);
        }

        // Save the file
        doc.save("full-vulnerability-report.pdf");
    }

    // Logout functionality
    const logoutBtns = [document.getElementById('logout-btn'), document.getElementById('sidebar-logout')];
    
    logoutBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (this.id === 'sidebar-logout') {
                e.preventDefault();
            }
            
            // Hide dashboard and show auth page
            document.getElementById('dashboard-page').style.display = 'none';
            document.getElementById('auth-page').style.display = 'flex';
            
            // Reset forms
            document.getElementById('login-form').style.display = 'block';
            document.getElementById('signup-form').style.display = 'none';
            document.getElementById('loginForm').reset();
            document.getElementById('signupForm').reset();
            
            // Reset file upload and code
            fileInput.value = '';
            codeInput.value = '';
            uploadText.textContent = 'Drag & drop your C/C++ file here';
            uploadText.style.fontWeight = '';
            uploadText.style.color = '';
            fileUploadArea.style.borderColor = '';
            fileUploadArea.style.backgroundColor = '';
            
            // Hide progress bar
            document.getElementById('progress-container').style.display = 'none';
            
            console.log('User logged out');
        });
    });

    // Initialize the dashboard with code analysis visible
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('codeAnalysis').classList.remove('d-none');
        document.querySelector('.sidebar .nav-link[href="#codeAnalysis"]').classList.add('active', 'active-link');
    });