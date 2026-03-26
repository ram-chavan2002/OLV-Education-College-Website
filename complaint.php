<?php 
/**
 * Complaint Management System - Full CRUD Operations
 * Location: /public_html/college/Complaint.php
 * Table: college_complaint (uses school_id field)
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Database connection
require_once 'db.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$college_id = isset($_SESSION['college_id']) ? (int)$_SESSION['college_id'] : 3;
$admin_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
$page_title = "Complaint Management";
$current_page = basename($_SERVER['PHP_SELF']);

// ==================== CREATE - ADD NEW COMPLAINT ====================
if (isset($_POST['add_complaint'])) {
    $complaint_type = mysqli_real_escape_string($db, $_POST['complaint_type']);
    $subject = mysqli_real_escape_string($db, $_POST['subject']);
    $description = mysqli_real_escape_string($db, $_POST['description']);
    $student_name = mysqli_real_escape_string($db, $_POST['student_name']);
    $student_email = mysqli_real_escape_string($db, $_POST['student_email']);
    $student_phone = mysqli_real_escape_string($db, $_POST['student_phone']);
    $student_id = mysqli_real_escape_string($db, $_POST['student_id']);
    $course = mysqli_real_escape_string($db, $_POST['course']);
    $year = mysqli_real_escape_string($db, $_POST['year']);
    $department = mysqli_real_escape_string($db, $_POST['department']);
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    $status = mysqli_real_escape_string($db, $_POST['status']);
    $priority = mysqli_real_escape_string($db, $_POST['priority']);
    $assigned_to = mysqli_real_escape_string($db, $_POST['assigned_to']);
    $admin_remarks = mysqli_real_escape_string($db, $_POST['admin_remarks']);

    // Handle attachment upload
    $attachment = '';
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $upload_dir = 'uploads/complaints/';

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];

        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            $file_name = 'complaint_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
                $attachment = $target_file;
            }
        } else {
            $error_message = "Invalid file format. Only JPG, PNG, PDF, DOC, DOCX allowed.";
        }
    }

    // Get IP address
    $ip_address = $_SERVER['REMOTE_ADDR'];

    if (!isset($error_message)) {
        $insert_query = "INSERT INTO college_complaint 
                         (school_id, complaint_type, subject, description, attachment, student_name, student_email, student_phone, student_id, course, year, department, is_anonymous, status, priority, assigned_to, admin_remarks, ip_address, created_at) 
                         VALUES 
                         ($college_id, '$complaint_type', '$subject', '$description', '$attachment', '$student_name', '$student_email', '$student_phone', '$student_id', '$course', '$year', '$department', $is_anonymous, '$status', '$priority', '$assigned_to', '$admin_remarks', '$ip_address', NOW())";

        if (mysqli_query($db, $insert_query)) {
            $success_message = "Complaint submitted successfully!";
        } else {
            $error_message = "Error: " . mysqli_error($db);
        }
    }
}

// ==================== UPDATE - EDIT COMPLAINT ====================
if (isset($_POST['update_complaint'])) {
    $complaint_id = (int)$_POST['complaint_id'];
    $complaint_type = mysqli_real_escape_string($db, $_POST['complaint_type']);
    $subject = mysqli_real_escape_string($db, $_POST['subject']);
    $description = mysqli_real_escape_string($db, $_POST['description']);
    $student_name = mysqli_real_escape_string($db, $_POST['student_name']);
    $student_email = mysqli_real_escape_string($db, $_POST['student_email']);
    $student_phone = mysqli_real_escape_string($db, $_POST['student_phone']);
    $student_id_field = mysqli_real_escape_string($db, $_POST['student_id']);
    $course = mysqli_real_escape_string($db, $_POST['course']);
    $year = mysqli_real_escape_string($db, $_POST['year']);
    $department = mysqli_real_escape_string($db, $_POST['department']);
    $status = mysqli_real_escape_string($db, $_POST['status']);
    $priority = mysqli_real_escape_string($db, $_POST['priority']);
    $assigned_to = mysqli_real_escape_string($db, $_POST['assigned_to']);
    $admin_remarks = mysqli_real_escape_string($db, $_POST['admin_remarks']);
    $resolution_details = mysqli_real_escape_string($db, $_POST['resolution_details']);
    $resolved_by = mysqli_real_escape_string($db, $_POST['resolved_by']);

    // Handle new attachment upload
    $attachment_update = "";
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $upload_dir = 'uploads/complaints/';

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
        $file_name = 'complaint_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
            // Delete old file
            $old_file_query = mysqli_query($db, "SELECT attachment FROM college_complaint WHERE id = $complaint_id");
            if ($old_row = mysqli_fetch_assoc($old_file_query)) {
                if (!empty($old_row['attachment']) && file_exists($old_row['attachment'])) {
                    unlink($old_row['attachment']);
                }
            }
            $attachment_update = ", attachment = '$target_file'";
        }
    }

    // Update resolved_at if status is resolved
    $resolved_at_update = "";
    if ($status == 'resolved') {
        $resolved_at_update = ", resolved_at = NOW()";
    }

    $update_query = "UPDATE college_complaint 
                     SET complaint_type = '$complaint_type',
                         subject = '$subject',
                         description = '$description',
                         student_name = '$student_name',
                         student_email = '$student_email',
                         student_phone = '$student_phone',
                         student_id = '$student_id_field',
                         course = '$course',
                         year = '$year',
                         department = '$department',
                         status = '$status',
                         priority = '$priority',
                         assigned_to = '$assigned_to',
                         admin_remarks = '$admin_remarks',
                         resolution_details = '$resolution_details',
                         resolved_by = '$resolved_by'
                         $attachment_update
                         $resolved_at_update
                     WHERE id = $complaint_id AND school_id = $college_id";

    if (mysqli_query($db, $update_query)) {
        $success_message = "Complaint updated successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($db);
    }
}

// ==================== DELETE - REMOVE COMPLAINT ====================
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    // Get attachment before deleting
    $get_file = mysqli_query($db, "SELECT attachment FROM college_complaint WHERE id = $delete_id AND school_id = $college_id");
    if ($row = mysqli_fetch_assoc($get_file)) {
        if (!empty($row['attachment']) && file_exists($row['attachment'])) {
            unlink($row['attachment']);
        }
    }

    $delete_query = "DELETE FROM college_complaint WHERE id = $delete_id AND school_id = $college_id";
    if (mysqli_query($db, $delete_query)) {
        $success_message = "Complaint deleted successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($db);
    }
    header("Location: Complaint.php");
    exit;
}

// ==================== READ - GET ALL COMPLAINTS ====================
$filter_type = isset($_GET['type']) ? $_GET['type'] : 'all';
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'all';
$filter_priority = isset($_GET['priority']) ? $_GET['priority'] : 'all';
$search = isset($_GET['search']) ? mysqli_real_escape_string($db, $_GET['search']) : '';

$where_clause = "WHERE school_id = $college_id";

if ($filter_type != 'all') {
    $where_clause .= " AND complaint_type = '$filter_type'";
}

if ($filter_status != 'all') {
    $where_clause .= " AND status = '$filter_status'";
}

if ($filter_priority != 'all') {
    $where_clause .= " AND priority = '$filter_priority'";
}

if (!empty($search)) {
    $where_clause .= " AND (subject LIKE '%$search%' OR student_name LIKE '%$search%' OR student_id LIKE '%$search%' OR description LIKE '%$search%')";
}

$complaints_query = "SELECT * FROM college_complaint $where_clause ORDER BY priority DESC, created_at DESC";
$complaints_result = @mysqli_query($db, $complaints_query);
$total_complaints = $complaints_result ? mysqli_num_rows($complaints_result) : 0;

// Get complaint for editing
$edit_complaint = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $edit_query = "SELECT * FROM college_complaint WHERE id = $edit_id AND school_id = $college_id";
    $edit_result = mysqli_query($db, $edit_query);
    $edit_complaint = mysqli_fetch_assoc($edit_result);
}

// Get statistics
$pending_count = @mysqli_num_rows(mysqli_query($db, "SELECT id FROM college_complaint WHERE school_id = $college_id AND status = 'pending'")) ?: 0;
$in_progress_count = @mysqli_num_rows(mysqli_query($db, "SELECT id FROM college_complaint WHERE school_id = $college_id AND status = 'in_progress'")) ?: 0;
$resolved_count = @mysqli_num_rows(mysqli_query($db, "SELECT id FROM college_complaint WHERE school_id = $college_id AND status = 'resolved'")) ?: 0;
$closed_count = @mysqli_num_rows(mysqli_query($db, "SELECT id FROM college_complaint WHERE school_id = $college_id AND status = 'closed'")) ?: 0;

if (file_exists('header.php')) {
    require_once 'header.php';
} else {
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Complaint Management</title></head><body>';
}
?>

<style>
/* Same CSS as before - keeping it short for token limit */
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f6fa; color: #2c3e50; }
.sidebar { position: fixed; left: 0; top: 0; width: 260px; height: 100vh; background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%); color: white; padding: 20px 0; overflow-y: auto; z-index: 1000; box-shadow: 2px 0 10px rgba(0,0,0,0.1); }
.sidebar-header { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
.sidebar-header h2 { font-size: 1.3em; font-weight: 600; }
.sidebar-menu { padding: 0 10px; }
.menu-item { display: block; padding: 12px 20px; color: #ecf0f1; text-decoration: none; border-radius: 8px; margin-bottom: 5px; transition: all 0.3s ease; font-size: 0.95em; }
.menu-item:hover { background: rgba(255,255,255,0.1); padding-left: 25px; }
.menu-item.active { background: #3498db; font-weight: 600; }
.main-content-area { margin-left: 260px; padding: 30px; min-height: 100vh; }
.page-header { background: white; padding: 25px 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
.page-header h2 { color: #2c3e50; font-size: 1.8em; }
.btn-add { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; border: none; cursor: pointer; }
.btn-add:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
.stats-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
.stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px; }
.stat-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5em; }
.stat-icon.pending { background: #fff3cd; color: #856404; }
.stat-icon.progress { background: #cce5ff; color: #004085; }
.stat-icon.resolved { background: #d4edda; color: #155724; }
.stat-icon.closed { background: #f8d7da; color: #721c24; }
.stat-info h3 { font-size: 2em; color: #2c3e50; margin-bottom: 5px; }
.stat-info p { color: #7f8c8d; font-size: 0.9em; }
.filter-bar { background: white; padding: 20px 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; gap: 15px; flex-wrap: wrap; align-items: center; }
.filter-box { flex: 1; min-width: 150px; }
.filter-box label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9em; color: #2c3e50; }
.filter-box select, .filter-box input { width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 0.95em; }
.alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.form-container { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; display: none; }
.form-container.show { display: block; }
.form-container h3 { margin-bottom: 25px; color: #2c3e50; font-size: 1.4em; }
.form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px; }
.form-group { margin-bottom: 20px; }
.form-group.full-width { grid-column: 1 / -1; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 0.95em; }
.form-control { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 1em; transition: border 0.3s ease; }
.form-control:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
textarea.form-control { resize: vertical; min-height: 100px; }
.checkbox-label { display: flex; align-items: center; gap: 10px; font-weight: normal; cursor: pointer; }
.checkbox-label input[type="checkbox"] { width: 20px; height: 20px; cursor: pointer; }
.btn-submit { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 12px 30px; border: none; border-radius: 8px; font-size: 1em; font-weight: 600; cursor: pointer; transition: all 0.3s ease; }
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
.btn-cancel { background: #95a5a6; color: white; padding: 12px 30px; border: none; border-radius: 8px; font-size: 1em; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; margin-left: 10px; }
.btn-cancel:hover { background: #7f8c8d; }
.complaints-container { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
.complaints-container h3 { margin-bottom: 25px; color: #2c3e50; font-size: 1.4em; }
.complaints-table { width: 100%; border-collapse: collapse; }
.complaints-table th { background: #f8f9fa; padding: 15px; text-align: left; font-weight: 600; color: #2c3e50; border-bottom: 2px solid #e9ecef; font-size: 0.9em; }
.complaints-table td { padding: 15px; border-bottom: 1px solid #e9ecef; font-size: 0.9em; }
.complaints-table tr:hover { background: #f8f9fa; }
.complaint-id { font-weight: 600; color: #667eea; }
.type-badge, .status-badge, .priority-badge { padding: 5px 12px; border-radius: 15px; font-size: 0.8em; font-weight: 600; display: inline-block; }
.type-academic { background: #cce5ff; color: #004085; }
.type-facility { background: #d4edda; color: #155724; }
.type-hostel { background: #fff3cd; color: #856404; }
.type-transport { background: #f8d7da; color: #721c24; }
.status-pending { background: #fff3cd; color: #856404; }
.status-in_progress { background: #cce5ff; color: #004085; }
.status-resolved { background: #d4edda; color: #155724; }
.status-closed { background: #f8d7da; color: #721c24; }
.priority-low { background: #e9ecef; color: #495057; }
.priority-medium { background: #fff3cd; color: #856404; }
.priority-high { background: #f8d7da; color: #721c24; }
.priority-urgent { background: #dc3545; color: white; }
.action-buttons { display: flex; gap: 8px; }
.btn-action { padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85em; font-weight: 500; transition: all 0.3s ease; }
.btn-edit { background: #3498db; color: white; }
.btn-delete { background: #e74c3c; color: white; }
.btn-action:hover { transform: translateY(-2px); box-shadow: 0 3px 10px rgba(0,0,0,0.2); }
.no-data { text-align: center; padding: 60px 20px; color: #7f8c8d; }
.no-data-icon { font-size: 4em; margin-bottom: 20px; }
@media (max-width: 768px) {
    .sidebar { width: 100%; height: auto; position: relative; }
    .main-content-area { margin-left: 0; padding: 15px; }
    .page-header { flex-direction: column; text-align: center; }
    .form-row { grid-template-columns: 1fr; }
    .complaints-table { display: block; overflow-x: auto; }
    .filter-bar { flex-direction: column; }
}
</style>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-header">
        <h2>🎓 College Admin</h2>
    </div>
   <nav class="sidebar-menu">
        <a href="dashboard.php" class="menu-item <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">🏠 Dashboard</a>
        <a href="Course.php" class="menu-item <?php echo $current_page == 'Course.php' ? 'active' : ''; ?>">📚 Courses</a>
        <a href="teachers.php" class="menu-item <?php echo $current_page == 'teachers.php' ? 'active' : ''; ?>">👨‍🏫 Teachers</a>
        <a href="complaint.php" class="menu-item <?php echo ($current_page == 'complaint.php') ? 'active' : ''; ?>">👨‍🏫Complaint</a>
        <a href="Gallery.php" class="menu-item <?php echo $current_page == 'gallery.php' ? 'active' : ''; ?>">🖼️ Gallery</a>
        <a href="Contact.php" class="menu-item <?php echo $current_page == 'Contact.php' ? 'active' : ''; ?>">📧 Contact</a>
        <a href="logout.php" class="menu-item <?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>">🚪Logout</a>
    </nav>
</div>

<!-- MAIN CONTENT -->
<div class="main-content-area">

    <div class="page-header">
        <h2>📝 Complaints Management (<?php echo $total_complaints; ?>)</h2>
        <?php if (!isset($_GET['edit_id'])): ?>
            <button class="btn-add" onclick="toggleForm('add-form')">➕ Add New Complaint</button>
        <?php endif; ?>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success">✓ <?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-error">✗ <?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon pending">⏳</div>
            <div class="stat-info">
                <h3><?php echo $pending_count; ?></h3>
                <p>Pending</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon progress">🔄</div>
            <div class="stat-info">
                <h3><?php echo $in_progress_count; ?></h3>
                <p>In Progress</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon resolved">✅</div>
            <div class="stat-info">
                <h3><?php echo $resolved_count; ?></h3>
                <p>Resolved</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon closed">🔒</div>
            <div class="stat-info">
                <h3><?php echo $closed_count; ?></h3>
                <p>Closed</p>
            </div>
        </div>
    </div>

    <?php if (!$edit_complaint && !isset($_GET['edit_id'])): ?>
    <div class="filter-bar">
        <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; width: 100%;">
            <div class="filter-box">
                <label>Search</label>
                <input type="text" name="search" placeholder="🔍 Search..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="filter-box">
                <label>Type</label>
                <select name="type" onchange="this.form.submit()">
                    <option value="all">All Types</option>
                    <option value="academic" <?php echo $filter_type == 'academic' ? 'selected' : ''; ?>>Academic</option>
                    <option value="facility" <?php echo $filter_type == 'facility' ? 'selected' : ''; ?>>Facility</option>
                    <option value="hostel" <?php echo $filter_type == 'hostel' ? 'selected' : ''; ?>>Hostel</option>
                    <option value="transport" <?php echo $filter_type == 'transport' ? 'selected' : ''; ?>>Transport</option>
                </select>
            </div>
            <div class="filter-box">
                <label>Status</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="all">All Status</option>
                    <option value="pending" <?php echo $filter_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="in_progress" <?php echo $filter_status == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="resolved" <?php echo $filter_status == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                    <option value="closed" <?php echo $filter_status == 'closed' ? 'selected' : ''; ?>>Closed</option>
                </select>
            </div>
            <div class="filter-box">
                <label>Priority</label>
                <select name="priority" onchange="this.form.submit()">
                    <option value="all">All Priorities</option>
                    <option value="low" <?php echo $filter_priority == 'low' ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?php echo $filter_priority == 'medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?php echo $filter_priority == 'high' ? 'selected' : ''; ?>>High</option>
                    <option value="urgent" <?php echo $filter_priority == 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                </select>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <div class="form-container <?php echo $edit_complaint ? 'show' : ''; ?>" id="add-form">
        <h3><?php echo $edit_complaint ? '✏️ Edit Complaint' : '➕ Add New Complaint'; ?></h3>
        <form method="POST" enctype="multipart/form-data">

            <?php if ($edit_complaint): ?>
                <input type="hidden" name="complaint_id" value="<?php echo $edit_complaint['id']; ?>">
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label>Complaint Type *</label>
                    <select name="complaint_type" class="form-control" required>
                        <option value="academic" <?php echo ($edit_complaint && $edit_complaint['complaint_type'] == 'academic') ? 'selected' : ''; ?>>Academic</option>
                        <option value="facility" <?php echo ($edit_complaint && $edit_complaint['complaint_type'] == 'facility') ? 'selected' : ''; ?>>Facility</option>
                        <option value="hostel" <?php echo ($edit_complaint && $edit_complaint['complaint_type'] == 'hostel') ? 'selected' : ''; ?>>Hostel</option>
                        <option value="transport" <?php echo ($edit_complaint && $edit_complaint['complaint_type'] == 'transport') ? 'selected' : ''; ?>>Transport</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Subject *</label>
                    <input type="text" name="subject" class="form-control" required 
                           value="<?php echo $edit_complaint ? htmlspecialchars($edit_complaint['subject']) : ''; ?>" 
                           placeholder="Enter complaint subject">
                </div>
            </div>

            <div class="form-group full-width">
                <label>Description *</label>
                <textarea name="description" class="form-control" required placeholder="Enter detailed description"><?php echo $edit_complaint ? htmlspecialchars($edit_complaint['description']) : ''; ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Student Name <?php echo !$edit_complaint ? '*' : ''; ?></label>
                    <input type="text" name="student_name" class="form-control" <?php echo !$edit_complaint ? 'required' : ''; ?>
                           value="<?php echo $edit_complaint ? htmlspecialchars($edit_complaint['student_name']) : ''; ?>" 
                           placeholder="Enter student name">
                </div>

                <div class="form-group">
                    <label>Student Email</label>
                    <input type="email" name="student_email" class="form-control" 
                           value="<?php echo $edit_complaint ? htmlspecialchars($edit_complaint['student_email']) : ''; ?>" 
                           placeholder="student@email.com">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Student Phone</label>
                    <input type="text" name="student_phone" class="form-control" 
                           value="<?php echo $edit_complaint ? htmlspecialchars($edit_complaint['student_phone']) : ''; ?>" 
                           placeholder="Enter phone number">
                </div>

                <div class="form-group">
                    <label>Student ID</label>
                    <input type="text" name="student_id" class="form-control" 
                           value="<?php echo $edit_complaint ? htmlspecialchars($edit_complaint['student_id']) : ''; ?>" 
                           placeholder="Enter student ID">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Course</label>
                    <input type="text" name="course" class="form-control" 
                           value="<?php echo $edit_complaint ? htmlspecialchars($edit_complaint['course']) : ''; ?>" 
                           placeholder="e.g. B.Tech, MBA">
                </div>

                <div class="form-group">
                    <label>Year</label>
                    <input type="text" name="year" class="form-control" 
                           value="<?php echo $edit_complaint ? htmlspecialchars($edit_complaint['year']) : ''; ?>" 
                           placeholder="e.g. 1st, 2nd">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Department</label>
                    <input type="text" name="department" class="form-control" 
                           value="<?php echo $edit_complaint ? htmlspecialchars($edit_complaint['department']) : ''; ?>" 
                           placeholder="e.g. Computer Science">
                </div>

                <div class="form-group">
                    <label>Attachment</label>
                    <input type="file" name="attachment" class="form-control" accept="image/*,.pdf,.doc,.docx">
                    <small style="color: #7f8c8d;">JPG, PNG, PDF, DOC, DOCX</small>
                    <?php if ($edit_complaint && !empty($edit_complaint['attachment'])): ?>
                        <div style="margin-top: 10px;">
                            <a href="<?php echo htmlspecialchars($edit_complaint['attachment']); ?>" target="_blank" style="color: #3498db;">📄 View Current File</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="pending" <?php echo ($edit_complaint && $edit_complaint['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="in_progress" <?php echo ($edit_complaint && $edit_complaint['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                        <option value="resolved" <?php echo ($edit_complaint && $edit_complaint['status'] == 'resolved') ? 'selected' : ''; ?>>Resolved</option>
                        <option value="closed" <?php echo ($edit_complaint && $edit_complaint['status'] == 'closed') ? 'selected' : ''; ?>>Closed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Priority</label>
                    <select name="priority" class="form-control">
                        <option value="low" <?php echo ($edit_complaint && $edit_complaint['priority'] == 'low') ? 'selected' : ''; ?>>Low</option>
                        <option value="medium" <?php echo ($edit_complaint && $edit_complaint['priority'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                        <option value="high" <?php echo ($edit_complaint && $edit_complaint['priority'] == 'high') ? 'selected' : ''; ?>>High</option>
                        <option value="urgent" <?php echo ($edit_complaint && $edit_complaint['priority'] == 'urgent') ? 'selected' : ''; ?>>Urgent</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Assigned To</label>
                    <input type="text" name="assigned_to" class="form-control" 
                           value="<?php echo $edit_complaint ? htmlspecialchars($edit_complaint['assigned_to']) : ''; ?>" 
                           placeholder="Assign to staff/admin">
                </div>

                <?php if (!$edit_complaint): ?>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_anonymous" value="1">
                        <span>📵 Submit Anonymously</span>
                    </label>
                </div>
                <?php endif; ?>
            </div>

            <div class="form-group full-width">
                <label>Admin Remarks</label>
                <textarea name="admin_remarks" class="form-control" placeholder="Admin notes/remarks"><?php echo $edit_complaint ? htmlspecialchars($edit_complaint['admin_remarks']) : ''; ?></textarea>
            </div>

            <?php if ($edit_complaint): ?>
            <div class="form-row">
                <div class="form-group">
                    <label>Resolution Details</label>
                    <textarea name="resolution_details" class="form-control" placeholder="How was the complaint resolved?"><?php echo htmlspecialchars($edit_complaint['resolution_details']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Resolved By</label>
                    <input type="text" name="resolved_by" class="form-control" 
                           value="<?php echo htmlspecialchars($edit_complaint['resolved_by']); ?>" 
                           placeholder="Name of person who resolved">
                </div>
            </div>
            <?php endif; ?>

            <div style="margin-top: 30px;">
                <button type="submit" name="<?php echo $edit_complaint ? 'update_complaint' : 'add_complaint'; ?>" class="btn-submit">
                    <?php echo $edit_complaint ? '💾 Update Complaint' : '➕ Submit Complaint'; ?>
                </button>
                <?php if ($edit_complaint): ?>
                    <a href="Complaint.php" class="btn-cancel">✕ Cancel</a>
                <?php else: ?>
                    <button type="button" class="btn-cancel" onclick="toggleForm('add-form')">✕ Cancel</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="complaints-container">
        <h3>📋 All Complaints</h3>

        <?php if ($total_complaints > 0): ?>
            <table class="complaints-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Student</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Assigned To</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($complaint = mysqli_fetch_assoc($complaints_result)): ?>
                        <tr>
                            <td class="complaint-id">#<?php echo $complaint['id']; ?></td>
                            <td>
                                <span class="type-badge type-<?php echo $complaint['complaint_type']; ?>">
                                    <?php echo ucfirst($complaint['complaint_type']); ?>
                                </span>
                            </td>
                            <td><strong><?php echo htmlspecialchars($complaint['subject']); ?></strong></td>
                            <td>
                                <?php if ($complaint['is_anonymous']): ?>
                                    <em>Anonymous</em>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($complaint['student_name']); ?>
                                    <?php if ($complaint['student_id']): ?>
                                        <br><small><?php echo htmlspecialchars($complaint['student_id']); ?></small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $complaint['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <span class="priority-badge priority-<?php echo $complaint['priority']; ?>">
                                    <?php echo ucfirst($complaint['priority']); ?>
                                </span>
                            </td>
                            <td><?php echo $complaint['assigned_to'] ? htmlspecialchars($complaint['assigned_to']) : '-'; ?></td>
                            <td><?php echo date('d M Y', strtotime($complaint['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?edit_id=<?php echo $complaint['id']; ?>" 
                                       class="btn-action btn-edit"
                                       title="Edit Complaint">
                                        ✏️
                                    </a>
                                    <a href="?delete_id=<?php echo $complaint['id']; ?>" 
                                       class="btn-action btn-delete"
                                       onclick="return confirm('Are you sure you want to delete this complaint?')"
                                       title="Delete Complaint">
                                        🗑️
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                <div class="no-data-icon">📝</div>
                <p style="font-size: 1.2em; font-weight: 600;">No complaints found</p>
                <p style="margin-top: 10px;">Click "Add New Complaint" to submit a complaint!</p>
            </div>
        <?php endif; ?>

    </div>

</div>

<script>
function toggleForm(formId) {
    const form = document.getElementById(formId);
    if (form) {
        if (form.classList.contains('show')) {
            form.classList.remove('show');
        } else {
            form.classList.add('show');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
}

setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(function() {
            alert.remove();
        }, 500);
    });
}, 5000);
</script>

<?php 
if (file_exists('footer.php')) {
    require_once 'footer.php';
} else {
    echo '</body></html>';
}
?>