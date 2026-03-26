<?php
// complaint1.php - Complete Complaint Form with Header & Footer Integration
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
if ($_POST) {
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
                
                $file_extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                $new_filename = 'complaint_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_path)) {
                    $attachment = $upload_path;
                }
            }
            
            // Insert complaint
            $sql = "INSERT INTO complaints_sch (
                school_id, complaint_type, subject, description, student_name, 
                student_email, student_phone, student_id, course, year, 
                department, priority, status, is_anonymous, ip_address, attachment
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?
            )";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $school_id, $complaint_type, $subject, $description, $student_name,
                $student_email ?: null, $student_phone ?: null, $student_id ?: null,
                $course ?: null, $year ?: null, $department ?: null, $priority,
                $is_anonymous, $_SERVER['REMOTE_ADDR'], $attachment
            ]);
            
            $message = "✅ Complaint submitted successfully! Your complaint ID: " . $pdo->lastInsertId();
            $_POST = [];
            
        } catch(PDOException $e) {
            $error = "❌ Database Error: " . $e->getMessage();
        }
    } else {
        $error = "⚠️ " . implode('<br>', $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Complaint Portal | Lodge Your Complaint</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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

        .form-container {
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

        .submit-btn:active {
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .form-container {
                padding: 30px 20px;
            }
            
            .complaint-header {
                padding: 30px 20px;
            }
            
            .complaint-header h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <?php include 'header1.php'; ?>

    <div class="main-container">
        <div class="complaint-container">
            <div class="complaint-header">
                <h1><i class="fas fa-exclamation-triangle"></i> Lodge Complaint</h1>
                <p>Your voice matters. We resolve complaints within 48 hours.</p>
            </div>
            
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
                                <option value="academic" <?php echo ($_POST['complaint_type'] ?? '') == 'academic' ? 'selected' : ''; ?>>📚 Academic Issues</option>
                                <option value="facility" <?php echo ($_POST['complaint_type'] ?? '') == 'facility' ? 'selected' : ''; ?>>🏢 Facility Problems</option>
                                <option value="hostel" <?php echo ($_POST['complaint_type'] ?? '') == 'hostel' ? 'selected' : ''; ?>>🏠 Hostel Related</option>
                                <option value="transport" <?php echo ($_POST['complaint_type'] ?? '') == 'transport' ? 'selected' : ''; ?>>🚌 Transport Issues</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-exclamation-circle"></i> Priority Level</label>
                            <select name="priority">
                                <option value="low" <?php echo ($_POST['priority'] ?? '') == 'low' ? 'selected' : ''; ?>>🟢 Low</option>
                                <option value="medium" <?php echo ($_POST['priority'] ?? 'medium') == 'medium' ? 'selected' : ''; ?>>🟡 Medium</option>
                                <option value="high" <?php echo ($_POST['priority'] ?? '') == 'high' ? 'selected' : ''; ?>>🟠 High</option>
                                <option value="urgent" <?php echo ($_POST['priority'] ?? '') == 'urgent' ? 'selected' : ''; ?>>🔴 Urgent</option>
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
                            <input type="text" name="student_name" value="<?php echo htmlspecialchars($_POST['student_name'] ?? ''); ?>" required placeholder="Enter your full name">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-id-card"></i> Student ID</label>
                            <input type="text" name="student_id" value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>" maxlength="100" placeholder="Your student registration number">
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" name="student_email" value="<?php echo htmlspecialchars($_POST['student_email'] ?? ''); ?>" placeholder="your.email@college.edu">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Phone Number</label>
                            <input type="tel" name="student_phone" value="<?php echo htmlspecialchars($_POST['student_phone'] ?? ''); ?>" maxlength="20" placeholder="10-digit mobile number">
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label><i class="fas fa-graduation-cap"></i> Course</label>
                            <input type="text" name="course" value="<?php echo htmlspecialchars($_POST['course'] ?? ''); ?>" maxlength="100" placeholder="B.Tech CSE, B.Com, etc.">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Year/Semester</label>
                            <input type="text" name="year" value="<?php echo htmlspecialchars($_POST['year'] ?? ''); ?>" maxlength="50" placeholder="2nd Year / 4th Sem">
                        </div>
                    </div>
                    
                    <div class="form-group full-width">
                        <label><i class="fas fa-building"></i> Department</label>
                        <input type="text" name="department" value="<?php echo htmlspecialchars($_POST['department'] ?? ''); ?>" maxlength="100" placeholder="Computer Science, Mechanical, etc.">
                    </div>
                    
                    <div class="form-group full-width">
                        <label><i class="fas fa-paperclip"></i> Attachment (Optional)</label>
                        <div class="file-input">
                            <input type="file" name="attachment" accept="image/*,.pdf,.doc,.docx" id="attachment">
                            <div class="file-info">📎 PNG, JPG, PDF, DOC/DOCX (Max 5MB) - Evidence photos/documents</div>
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
    </div>

    <?php include 'footer1.php'; ?>

    <script>
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
</body>
</html>
