<?php
// complaintt.php - PERFECT MATCH for college/front/complaintt.php with new UI + header/footer
session_start();

// Database Configuration
$host = 'localhost';
$dbname = 'sai7755_college';
$username = 'sai7755_college';
$password = 'Admin_66666';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // school_id is REQUIRED - Default to 3 as per table structure
    $school_id = 3; 
    $complaint_type = $_POST['complaint_type'] ?? '';
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $student_name = trim($_POST['student_name'] ?? '');
    $student_email = trim($_POST['student_email'] ?? '');
    $student_phone = trim($_POST['student_phone'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $priority = $_POST['priority'] ?? 'medium';
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    
    // Validation
    $errors = [];
    if (empty($complaint_type)) $errors[] = "Complaint type is required";
    if (empty($subject)) $errors[] = "Subject is required";
    if (empty($description)) $errors[] = "Description is required";
    if (empty($student_name)) $errors[] = "Student name is required";
    
    if (empty($errors)) {
        try {
            // Handle file upload
            $attachment = null;
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/complaints/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
                $file_extension = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
                $file_size = $_FILES['attachment']['size'];
                
                if (in_array($file_extension, $allowed_extensions) && $file_size <= 5242880) {
                    $new_filename = 'complaint_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_path)) {
                        $attachment = $new_filename; // Store only filename
                    }
                } else {
                    $errors[] = "Invalid file type or size exceeds 5MB";
                }
            }
            
            if (empty($errors)) {
                // PERFECT MATCH INSERT for your table structure
                $sql = "INSERT INTO college_complaint (
                    school_id, complaint_type, subject, description, attachment,
                    student_name, student_email, student_phone, student_id,
                    course, year, department, is_anonymous, status, priority, ip_address
                ) VALUES (
                    :school_id, :complaint_type, :subject, :description, :attachment,
                    :student_name, :student_email, :student_phone, :student_id,
                    :course, :year, :department, :is_anonymous, 'pending', :priority, :ip_address
                )";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':school_id' => $school_id,
                    ':complaint_type' => $complaint_type,
                    ':subject' => $subject,
                    ':description' => $description,
                    ':attachment' => $attachment,
                    ':student_name' => $student_name,
                    ':student_email' => $student_email ?: null,
                    ':student_phone' => $student_phone ?: null,
                    ':student_id' => $student_id ?: null,
                    ':course' => $course ?: null,
                    ':year' => $year ?: null,
                    ':department' => $department ?: null,
                    ':is_anonymous' => $is_anonymous,
                    ':priority' => $priority,
                    ':ip_address' => $_SERVER['REMOTE_ADDR']
                ]);
                
                $complaint_id = $pdo->lastInsertId();
                $message = "✅ Complaint submitted successfully! Your complaint ID is: <strong>#" . $complaint_id . "</strong>";
                $_POST = []; // Clear form
            }
            
        } catch(PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

// Fetch recent complaints for dashboard
$recent_complaints = [];
try {
    $sql = "SELECT id, school_id, complaint_type, subject, priority, status, created_at,
                   CASE WHEN is_anonymous = 1 THEN 'Anonymous' ELSE student_name END as complainant
            FROM college_complaint 
            WHERE school_id = 3
            ORDER BY created_at DESC 
            LIMIT 10";
    $stmt = $pdo->query($sql);
    $recent_complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Silently fail
}
?>

<?php include 'header.php'; ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<div class="main-container">
    <div class="complaint-container">
        <div class="complaint-header">
            <h1><i class="fas fa-exclamation-triangle"></i> Lodge Complaint</h1>
            <p>Your voice matters. We resolve complaints within 48 hours.</p>
        </div>
        
        <div class="tabs">
            <button class="tab active" onclick="showTab('form')">📋 Submit Complaint</button>
            <button class="tab" onclick="showTab('dashboard')">📊 Dashboard</button>
        </div>
        
        <!-- Submit Form Tab -->
        <div id="form" class="tab-content active">
            <div class="form-container">
                <?php if ($message): ?>
                    <div class="message success">
                        <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="message error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" id="complaintForm" novalidate>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-tag"></i> Complaint Type <span class="required">*</span>
                            </label>
                            <select name="complaint_type" required>
                                <option value="">Select Complaint Type...</option>
                                <option value="academic" <?php echo (isset($_POST['complaint_type']) && $_POST['complaint_type'] == 'academic') ? 'selected' : ''; ?>>📚 Academic Issues</option>
                                <option value="facility" <?php echo (isset($_POST['complaint_type']) && $_POST['complaint_type'] == 'facility') ? 'selected' : ''; ?>>🏢 Facility Problems</option>
                                <option value="hostel" <?php echo (isset($_POST['complaint_type']) && $_POST['complaint_type'] == 'hostel') ? 'selected' : ''; ?>>🏠 Hostel Related</option>
                                <option value="transport" <?php echo (isset($_POST['complaint_type']) && $_POST['complaint_type'] == 'transport') ? 'selected' : ''; ?>>🚌 Transport Issues</option>
                                <option value="library" <?php echo (isset($_POST['complaint_type']) && $_POST['complaint_type'] == 'library') ? 'selected' : ''; ?>>📖 Library</option>
                                <option value="canteen" <?php echo (isset($_POST['complaint_type']) && $_POST['complaint_type'] == 'canteen') ? 'selected' : ''; ?>>🍽️ Canteen</option>
                                <option value="other" <?php echo (isset($_POST['complaint_type']) && $_POST['complaint_type'] == 'other') ? 'selected' : ''; ?>>🔧 Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-exclamation-circle"></i> Priority Level</label>
                            <select name="priority">
                                <option value="low" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'low') ? 'selected' : ''; ?>>🟢 Low</option>
                                <option value="medium" <?php echo (!isset($_POST['priority']) || $_POST['priority'] == 'medium') ? 'selected' : ''; ?>>🟡 Medium</option>
                                <option value="high" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'high') ? 'selected' : ''; ?>>🟠 High</option>
                                <option value="urgent" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'urgent') ? 'selected' : ''; ?>>🔴 Urgent</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>
                            <i class="fas fa-heading"></i> Subject <span class="required">*</span>
                        </label>
                        <input type="text" name="subject" value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>" required maxlength="255" placeholder="Brief title of your complaint">
                    </div>
                    
                    <div class="form-group full-width">
                        <label>
                            <i class="fas fa-align-left"></i> Detailed Description <span class="required">*</span>
                        </label>
                        <textarea name="description" required placeholder="Please provide complete details of your complaint. Include dates, locations, and people involved."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-user"></i> Full Name <span class="required">*</span>
                            </label>
                            <input type="text" name="student_name" value="<?php echo htmlspecialchars($_POST['student_name'] ?? ''); ?>" required maxlength="100" placeholder="Enter your full name">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-id-card"></i> Student ID</label>
                            <input type="text" name="student_id" value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>" maxlength="50" placeholder="e.g., MUM12345">
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" name="student_email" value="<?php echo htmlspecialchars($_POST['student_email'] ?? ''); ?>" maxlength="100" placeholder="your.email@example.com">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Phone Number</label>
                            <input type="tel" name="student_phone" value="<?php echo htmlspecialchars($_POST['student_phone'] ?? ''); ?>" maxlength="20" placeholder="+91 9876543210">
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label><i class="fas fa-graduation-cap"></i> Course</label>
                            <input type="text" name="course" value="<?php echo htmlspecialchars($_POST['course'] ?? ''); ?>" maxlength="100" placeholder="e.g., B.Tech, M.Sc">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Year/Semester</label>
                            <input type="text" name="year" value="<?php echo htmlspecialchars($_POST['year'] ?? ''); ?>" maxlength="20" placeholder="e.g., 2nd Year, Sem 4">
                        </div>
                    </div>
                    
                    <div class="form-group full-width">
                        <label><i class="fas fa-building"></i> Department</label>
                        <input type="text" name="department" value="<?php echo htmlspecialchars($_POST['department'] ?? ''); ?>" maxlength="100" placeholder="e.g., Computer Science, Mechanical">
                    </div>
                    
                    <div class="form-group full-width">
                        <label><i class="fas fa-paperclip"></i> Attachment (Optional)</label>
                        <div class="file-input">
                            <input type="file" name="attachment" accept="image/*,.pdf,.doc,.docx" id="attachment">
                            <div class="file-info">📎 PNG, JPG, JPEG, PDF, DOC/DOCX (Max 5MB) - Evidence photos/documents</div>
                        </div>
                    </div>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" name="is_anonymous" id="anonymous" <?php echo isset($_POST['is_anonymous']) ? 'checked' : ''; ?>>
                        <label for="anonymous">
                            <strong>Anonymous Submission</strong><br>
                            <small>Your name will be kept strictly confidential</small>
                        </label>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Submit Complaint Now
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Dashboard Tab -->
        <div id="dashboard" class="tab-content">
            <div class="dashboard-container">
                <h3><i class="fas fa-chart-bar"></i> Recent Complaints Dashboard</h3>
                <?php if (empty($recent_complaints)): ?>
                    <div class="no-complaints">
                        <i class="fas fa-inbox"></i>
                        <p>No complaints submitted yet.</p>
                    </div>
                <?php else: ?>
                    <div class="complaints-list">
                        <?php foreach ($recent_complaints as $complaint): ?>
                            <div class="complaint-item">
                                <div class="complaint-header">
                                    <div class="complaint-type">
                                        <i class="fas fa-tag"></i>
                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $complaint['complaint_type']))); ?>
                                    </div>
                                    <div class="badges">
                                        <span class="priority-badge priority-<?php echo $complaint['priority']; ?>">
                                            <?php echo ucfirst($complaint['priority']); ?>
                                        </span>
                                        <span class="status-badge"><?php echo ucfirst($complaint['status']); ?></span>
                                    </div>
                                </div>
                                <div class="complaint-meta">
                                    <strong>By:</strong> <?php echo htmlspecialchars($complaint['complainant']); ?> 
                                    | <strong>Date:</strong> <?php echo date('M j, Y g:i A', strtotime($complaint['created_at'])); ?>
                                </div>
                                <div class="complaint-subject">
                                    <?php echo htmlspecialchars(substr($complaint['subject'], 0, 80)) . (strlen($complaint['subject']) > 80 ? '...' : ''); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<style>
    :root {
        --primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --secondary: linear-gradient(135deg, #ff6b6b, #feca57);
        --success: #28a745;
        --danger: #dc3545;
        --warning: #ffc107;
        --dark: #2c3e50;
        --light: #f8f9fa;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body { 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: var(--primary);
        line-height: 1.6;
        color: #333;
        padding: 20px 0;
    }

    .main-container {
        min-height: 100vh;
        padding: 20px 0;
    }

    .complaint-container {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        overflow: hidden;
        animation: slideUp 0.6s ease-out;
        margin-bottom: 20px;
    }

    @keyframes slideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .complaint-header {
        background: var(--secondary);
        color: white;
        padding: 40px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .complaint-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: rgba(255,255,255,0.1);
        transform: rotate(30deg);
    }

    .complaint-header h1 {
        font-size: 2.5em;
        margin-bottom: 10px;
        position: relative;
        z-index: 2;
    }

    .complaint-header p {
        font-size: 1.2em;
        opacity: 0.95;
        position: relative;
        z-index: 2;
    }

    .tabs { 
        display: flex; background: #f8f9fa; border-bottom: 2px solid #e9ecef; 
    }
    .tab { 
        flex: 1; padding: 15px 20px; text-align: center; border: none; 
        background: none; font-size: 16px; font-weight: 600; cursor: pointer; 
        transition: all 0.3s ease; color: #6c757d;
    }
    .tab.active { background: white; color: #667eea; border-bottom: 3px solid #667eea; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    .form-container, .dashboard-container {
        padding: 50px;
    }

    .message {
        padding: 20px;
        margin-bottom: 30px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 16px;
        border-left: 5px solid;
    }

    .success {
        background: #d4edda;
        color: #155724;
        border-left-color: var(--success);
    }

    .error {
        background: #f8d7da;
        color: #721c24;
        border-left-color: var(--danger);
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    label {
        display: block;
        margin-bottom: 10px;
        font-weight: 600;
        color: var(--dark);
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .required {
        color: var(--danger);
    }

    input, select, textarea {
        width: 100%;
        padding: 16px 20px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s ease;
        font-family: inherit;
    }

    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        transform: translateY(-1px);
    }

    textarea {
        resize: vertical;
        min-height: 140px;
        font-family: inherit;
    }

    .file-input {
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .file-input input[type=file] {
        padding: 16px;
        background: #f8f9fa;
    }

    .file-info {
        font-size: 13px;
        color: #6c757d;
        margin-top: 5px;
    }

    .checkbox-group {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 2px solid #e9ecef;
    }

    .checkbox-group input[type=checkbox] {
        width: auto;
        margin: 0;
        transform: scale(1.2);
    }

    .submit-btn {
        background: var(--primary);
        color: white;
        padding: 20px 50px;
        border: none;
        border-radius: 15px;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        position: relative;
        overflow: hidden;
    }

    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.4);
    }

    /* Dashboard Styles */
    .dashboard-container h3 {
        color: var(--dark);
        margin-bottom: 30px;
        font-size: 1.8em;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .no-complaints {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .no-complaints i {
        font-size: 4em;
        display: block;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .complaints-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .complaint-item { 
        background: #f8f9fa; 
        padding: 25px; 
        border-radius: 15px; 
        border-left: 5px solid #667eea;
        transition: all 0.3s ease;
    }

    .complaint-item:hover {
        transform: translateX(5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .complaint-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 15px; 
    }

    .complaint-type { 
        font-weight: 700; 
        color: #667eea; 
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
    }

    .badges { 
        display: flex; 
        gap: 10px; 
    }

    .priority-badge { 
        padding: 6px 14px; 
        border-radius: 20px; 
        font-size: 12px; 
        font-weight: 700; 
        text-transform: uppercase;
    }

    .priority-low { background: #d4edda; color: #155724; }
    .priority-medium { background: #fff3cd; color: #856404; }
    .priority-high { background: #f8d7da; color: #721c24; }
    .priority-urgent { background: #f5c2c7; color: #842029; }

    .status-badge { 
        padding: 6px 12px; 
        border-radius: 15px; 
        font-size: 12px; 
        font-weight: 700; 
        background: #e9ecef; 
        color: #495057; 
    }

    .complaint-meta { 
        font-size: 14px; 
        color: #6c757d; 
        margin-bottom: 12px; 
    }

    .complaint-subject { 
        font-weight: 600; 
        color: #333; 
        font-size: 16px;
        line-height: 1.5;
    }

    @media (max-width: 768px) { 
        .form-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .form-container, .dashboard-container {
            padding: 30px 20px;
        }
        
        .complaint-header {
            padding: 30px 20px;
        }
        
        .complaint-header h1 {
            font-size: 2em;
        }
        
        .tabs { 
            flex-direction: column; 
        }
    }
</style>

<script>
    function showTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
        document.getElementById(tabName).classList.add('active');
        event.target.classList.add('active');
    }
    
    // Enhanced form validation with real-time feedback
    document.getElementById('complaintForm').addEventListener('submit', function(e) {
        const requiredFields = ['complaint_type', 'subject', 'description', 'student_name'];
        let isValid = true;
        
        requiredFields.forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (!input.value.trim()) {
                input.style.borderColor = '#dc3545';
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill all required fields marked with *');
            return false;
        }
    });

    // Real-time validation
    ['complaint_type', 'subject', 'description', 'student_name'].forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        input.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#28a745';
            }
        });
    });

    // File size validation
    document.getElementById('attachment').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            e.target.value = '';
        }
    });
</script>
