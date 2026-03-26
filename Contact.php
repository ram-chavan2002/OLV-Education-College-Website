<?php 
/**
 * CONTACTS MANAGEMENT SYSTEM - College Admin Panel
 * Location: /public_html/college/Contact.php
 * Table: contacts_sch (uses school_id field)
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
$page_title = "Contacts Management";
$current_page = basename($_SERVER['PHP_SELF']);

// ==================== CREATE - ADD CONTACT ====================
if (isset($_POST['add_contact'])) {
    $enquiry_type = mysqli_real_escape_string($db, $_POST['enquiry_type']);
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $phone = mysqli_real_escape_string($db, $_POST['phone']);
    $subject = mysqli_real_escape_string($db, $_POST['subject']);
    $message = mysqli_real_escape_string($db, $_POST['message']);
    $course_interest = mysqli_real_escape_string($db, $_POST['course_interest']);
    $year_interest = mysqli_real_escape_string($db, $_POST['year_interest']);
    $city = mysqli_real_escape_string($db, $_POST['city']);
    $state = mysqli_real_escape_string($db, $_POST['state']);
    $priority = mysqli_real_escape_string($db, $_POST['priority']);
    $source = mysqli_real_escape_string($db, $_POST['source']);
    $is_subscribed = isset($_POST['is_subscribed']) ? 1 : 0;
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $insert_query = "INSERT INTO college_contact 
                     (school_id, enquiry_type, name, email, phone, subject, message, course_interest, year_interest, city, state, priority, source, is_subscribed, ip_address, user_agent, created_at) 
                     VALUES 
                     ($college_id, '$enquiry_type', '$name', '$email', '$phone', '$subject', '$message', '$course_interest', '$year_interest', '$city', '$state', '$priority', '$source', $is_subscribed, '$ip_address', '$user_agent', NOW())";

    if (mysqli_query($db, $insert_query)) {
        $success_message = "Contact added successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($db);
    }
}

// ==================== UPDATE - REPLY TO CONTACT ====================
if (isset($_POST['reply_contact'])) {
    $contact_id = (int)$_POST['contact_id'];
    $status = mysqli_real_escape_string($db, $_POST['status']);
    $priority = mysqli_real_escape_string($db, $_POST['priority']);
    $assigned_to = mysqli_real_escape_string($db, $_POST['assigned_to']);
    $admin_remarks = mysqli_real_escape_string($db, $_POST['admin_remarks']);
    $reply_message = mysqli_real_escape_string($db, $_POST['reply_message']);

    $update_query = "UPDATE contacts_sch 
                     SET status = '$status',
                         priority = '$priority',
                         assigned_to = '$assigned_to',
                         admin_remarks = '$admin_remarks',
                         reply_message = '$reply_message',
                         replied_by = '$admin_name',
                         replied_at = NOW()
                     WHERE id = $contact_id AND school_id = $college_id";

    if (mysqli_query($db, $update_query)) {
        $success_message = "Contact updated successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($db);
    }
}

// ==================== DELETE - REMOVE CONTACT ====================
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    $delete_query = "DELETE FROM contacts_sch WHERE id = $delete_id AND school_id = $college_id";
    if (mysqli_query($db, $delete_query)) {
        $success_message = "Contact deleted successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($db);
    }
    header("Location: Contact.php");
    exit;
}

// ==================== READ - GET ALL CONTACTS ====================
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'all';
$filter_type = isset($_GET['type']) ? $_GET['type'] : 'all';
$filter_priority = isset($_GET['priority']) ? $_GET['priority'] : 'all';
$search = isset($_GET['search']) ? mysqli_real_escape_string($db, $_GET['search']) : '';

$where_clause = "WHERE school_id = $college_id";

if ($filter_status != 'all') {
    $where_clause .= " AND status = '$filter_status'";
}

if ($filter_type != 'all') {
    $where_clause .= " AND enquiry_type = '$filter_type'";
}

if ($filter_priority != 'all') {
    $where_clause .= " AND priority = '$filter_priority'";
}

if (!empty($search)) {
    $where_clause .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%' OR subject LIKE '%$search%' OR message LIKE '%$search%')";
}

$contacts_query = "SELECT * FROM contacts_sch $where_clause ORDER BY 
                   FIELD(status, 'new', 'read', 'replied', 'resolved', 'closed'),
                   FIELD(priority, 'urgent', 'high', 'medium', 'low'),
                   id DESC LIMIT 200";
$contacts_result = @mysqli_query($db, $contacts_query);
$total_contacts = $contacts_result ? mysqli_num_rows($contacts_result) : 0;

// Get statistics
$stats_query = "SELECT 
                COUNT(*) as total,
                SUM(status='new') as new_count,
                SUM(status='replied') as replied_count,
                SUM(status='resolved') as resolved_count,
                SUM(enquiry_type='admission') as admission_count,
                SUM(priority='urgent') as urgent_count
                FROM contacts_sch WHERE school_id = $college_id";
$stats_result = mysqli_query($db, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// View single contact
$view_contact = null;
if (isset($_GET['view_id'])) {
    $view_id = (int)$_GET['view_id'];
    $view_query = "SELECT * FROM contacts_sch WHERE id = $view_id AND school_id = $college_id";
    $view_result = mysqli_query($db, $view_query);
    $view_contact = mysqli_fetch_assoc($view_result);

    // Mark as read if new
    if ($view_contact && $view_contact['status'] == 'new') {
        mysqli_query($db, "UPDATE contacts_sch SET status='read' WHERE id=$view_id");
        $view_contact['status'] = 'read';
    }
}

if (file_exists('header.php')) {
    require_once 'header.php';
} else {
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Contacts Management</title></head><body>';
}
?>

<style>
/* ==================== CONTACTS MANAGEMENT STYLES ==================== */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f5f6fa;
    color: #2c3e50;
}

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 260px;
    height: 100vh;
    background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
    color: white;
    padding: 20px 0;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    margin-bottom: 20px;
}

.sidebar-header h2 {
    font-size: 1.3em;
    font-weight: 600;
}

.sidebar-menu {
    padding: 0 10px;
}

.menu-item {
    display: block;
    padding: 12px 20px;
    color: #ecf0f1;
    text-decoration: none;
    border-radius: 8px;
    margin-bottom: 5px;
    transition: all 0.3s ease;
    font-size: 0.95em;
}

.menu-item:hover {
    background: rgba(255,255,255,0.1);
    padding-left: 25px;
}

.menu-item.active {
    background: #3498db;
    font-weight: 600;
}

.main-content-area {
    margin-left: 260px;
    padding: 30px;
    min-height: 100vh;
}

.page-header {
    background: white;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.page-header h2 {
    color: #2c3e50;
    font-size: 1.8em;
}

.btn-add {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Statistics Cards */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5em;
}

.stat-icon.new {
    background: #fff3cd;
    color: #856404;
}

.stat-icon.replied {
    background: #cce5ff;
    color: #004085;
}

.stat-icon.resolved {
    background: #d4edda;
    color: #155724;
}

.stat-icon.admission {
    background: #f8d7da;
    color: #721c24;
}

.stat-icon.urgent {
    background: #dc3545;
    color: white;
}

.stat-info h3 {
    font-size: 2em;
    color: #2c3e50;
    margin-bottom: 5px;
}

.stat-info p {
    color: #7f8c8d;
    font-size: 0.9em;
}

/* Filter Bar */
.filter-bar {
    background: white;
    padding: 20px 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    align-items: center;
}

.filter-box {
    flex: 1;
    min-width: 150px;
}

.filter-box label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    font-size: 0.9em;
    color: #2c3e50;
}

.filter-box select,
.filter-box input {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.95em;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Contacts Table */
.contacts-container {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.contacts-container h3 {
    margin-bottom: 25px;
    color: #2c3e50;
    font-size: 1.4em;
}

.contacts-table {
    width: 100%;
    border-collapse: collapse;
}

.contacts-table th {
    background: #f8f9fa;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #2c3e50;
    border-bottom: 2px solid #e9ecef;
    font-size: 0.9em;
}

.contacts-table td {
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
    font-size: 0.9em;
}

.contacts-table tr:hover {
    background: #f8f9fa;
}

.contact-id {
    font-weight: 600;
    color: #667eea;
}

.type-badge {
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.8em;
    font-weight: 600;
    display: inline-block;
}

.type-admission {
    background: #cce5ff;
    color: #004085;
}

.type-enquiry {
    background: #d4edda;
    color: #155724;
}

.type-feedback {
    background: #fff3cd;
    color: #856404;
}

.type-support {
    background: #f8d7da;
    color: #721c24;
}

.status-badge {
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.8em;
    font-weight: 600;
    display: inline-block;
}

.status-new {
    background: #fff3cd;
    color: #856404;
}

.status-read {
    background: #cce5ff;
    color: #004085;
}

.status-replied {
    background: #d4edda;
    color: #155724;
}

.status-resolved {
    background: #d1ecf1;
    color: #0c5460;
}

.status-closed {
    background: #f8d7da;
    color: #721c24;
}

.priority-badge {
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.8em;
    font-weight: 600;
    display: inline-block;
}

.priority-low {
    background: #e9ecef;
    color: #495057;
}

.priority-medium {
    background: #fff3cd;
    color: #856404;
}

.priority-high {
    background: #f8d7da;
    color: #721c24;
}

.priority-urgent {
    background: #dc3545;
    color: white;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-action {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.85em;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-view {
    background: #17a2b8;
    color: white;
}

.btn-delete {
    background: #e74c3c;
    color: white;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
}

/* Contact Detail View */
.contact-detail {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.contact-detail h3 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 1.5em;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.detail-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.detail-item label {
    display: block;
    font-weight: 600;
    color: #7f8c8d;
    font-size: 0.85em;
    margin-bottom: 5px;
}

.detail-item p {
    color: #2c3e50;
    font-size: 1em;
}

.detail-full {
    grid-column: 1 / -1;
}

.message-box {
    padding: 20px;
    background: #f8f9fa;
    border-left: 4px solid #3498db;
    border-radius: 8px;
    margin: 20px 0;
}

.reply-form {
    margin-top: 30px;
    padding-top: 30px;
    border-top: 2px solid #e9ecef;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.95em;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1em;
    transition: border 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

.btn-submit {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    font-size: 1em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.btn-back {
    background: #95a5a6;
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    font-size: 1em;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    margin-left: 10px;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background: #7f8c8d;
}

.no-data {
    text-align: center;
    padding: 60px 20px;
    color: #7f8c8d;
}

.no-data-icon {
    font-size: 4em;
    margin-bottom: 20px;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }

    .main-content-area {
        margin-left: 0;
        padding: 15px;
    }

    .page-header {
        flex-direction: column;
        text-align: center;
    }

    .contacts-table {
        display: block;
        overflow-x: auto;
    }

    .filter-bar {
        flex-direction: column;
    }

    .detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-header">
        <h2>🎓 College Admin</h2>
    </div>
    <nav class="sidebar-menu">
        <a href="dashboard.php" class="menu-item">🏠 Dashboard</a>
        <a href="Course.php" class="menu-item">📚 Courses</a>
        <a href="Teachers.php" class="menu-item">👨‍🏫 Teachers</a>
        <a href="Complaint.php" class="menu-item">📝 Complaints</a>
        <a href="Gallery.php" class="menu-item">🖼️ Gallery</a>
        <a href="Contact.php" class="menu-item active">📧 Contacts</a>
        <a href="logout.php" class="menu-item">🚪 Logout</a>
    </nav>
</div>

<!-- MAIN CONTENT -->
<div class="main-content-area">

    <div class="page-header">
        <h2>📧 Contacts Management (<?php echo $total_contacts; ?>)</h2>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success">✓ <?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-error">✗ <?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if (!$view_contact): ?>

    <!-- Statistics Cards -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon new">📨</div>
            <div class="stat-info">
                <h3><?php echo $stats['new_count'] ?: 0; ?></h3>
                <p>New</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon replied">📧</div>
            <div class="stat-info">
                <h3><?php echo $stats['replied_count'] ?: 0; ?></h3>
                <p>Replied</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon resolved">✅</div>
            <div class="stat-info">
                <h3><?php echo $stats['resolved_count'] ?: 0; ?></h3>
                <p>Resolved</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon admission">🎓</div>
            <div class="stat-info">
                <h3><?php echo $stats['admission_count'] ?: 0; ?></h3>
                <p>Admissions</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon urgent">🔥</div>
            <div class="stat-info">
                <h3><?php echo $stats['urgent_count'] ?: 0; ?></h3>
                <p>Urgent</p>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
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
                    <option value="admission" <?php echo $filter_type == 'admission' ? 'selected' : ''; ?>>Admission</option>
                    <option value="enquiry" <?php echo $filter_type == 'enquiry' ? 'selected' : ''; ?>>Enquiry</option>
                    <option value="feedback" <?php echo $filter_type == 'feedback' ? 'selected' : ''; ?>>Feedback</option>
                    <option value="support" <?php echo $filter_type == 'support' ? 'selected' : ''; ?>>Support</option>
                </select>
            </div>
            <div class="filter-box">
                <label>Status</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="all">All Status</option>
                    <option value="new" <?php echo $filter_status == 'new' ? 'selected' : ''; ?>>New</option>
                    <option value="read" <?php echo $filter_status == 'read' ? 'selected' : ''; ?>>Read</option>
                    <option value="replied" <?php echo $filter_status == 'replied' ? 'selected' : ''; ?>>Replied</option>
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

    <!-- Contacts Table -->
    <div class="contacts-container">
        <h3>📋 All Contacts</h3>

        <?php if ($total_contacts > 0): ?>
            <table class="contacts-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Email / Phone</th>
                        <th>Subject</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($contact = mysqli_fetch_assoc($contacts_result)): ?>
                        <tr>
                            <td class="contact-id">#<?php echo $contact['id']; ?></td>
                            <td>
                                <span class="type-badge type-<?php echo $contact['enquiry_type']; ?>">
                                    <?php echo ucfirst($contact['enquiry_type']); ?>
                                </span>
                            </td>
                            <td><strong><?php echo htmlspecialchars($contact['name']); ?></strong></td>
                            <td>
                                <?php echo htmlspecialchars($contact['email']); ?><br>
                                <small><?php echo htmlspecialchars($contact['phone']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($contact['subject']); ?></td>
                            <td>
                                <span class="priority-badge priority-<?php echo $contact['priority']; ?>">
                                    <?php echo ucfirst($contact['priority']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $contact['status']; ?>">
                                    <?php echo ucfirst($contact['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($contact['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?view_id=<?php echo $contact['id']; ?>" 
                                       class="btn-action btn-view"
                                       title="View Contact">
                                        👁️
                                    </a>
                                    <a href="?delete_id=<?php echo $contact['id']; ?>" 
                                       class="btn-action btn-delete"
                                       onclick="return confirm('Are you sure you want to delete this contact?')"
                                       title="Delete Contact">
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
                <div class="no-data-icon">📧</div>
                <p style="font-size: 1.2em; font-weight: 600;">No contacts found</p>
                <p style="margin-top: 10px;">Contacts will appear here when submitted!</p>
            </div>
        <?php endif; ?>

    </div>

    <?php else: ?>

    <!-- Contact Detail View -->
    <div class="contact-detail">
        <h3>📧 Contact Details - #<?php echo $view_contact['id']; ?></h3>

        <div class="detail-grid">
            <div class="detail-item">
                <label>Name</label>
                <p><?php echo htmlspecialchars($view_contact['name']); ?></p>
            </div>
            <div class="detail-item">
                <label>Email</label>
                <p><?php echo htmlspecialchars($view_contact['email']); ?></p>
            </div>
            <div class="detail-item">
                <label>Phone</label>
                <p><?php echo htmlspecialchars($view_contact['phone']); ?></p>
            </div>
            <div class="detail-item">
                <label>Type</label>
                <p><span class="type-badge type-<?php echo $view_contact['enquiry_type']; ?>"><?php echo ucfirst($view_contact['enquiry_type']); ?></span></p>
            </div>
            <div class="detail-item">
                <label>Priority</label>
                <p><span class="priority-badge priority-<?php echo $view_contact['priority']; ?>"><?php echo ucfirst($view_contact['priority']); ?></span></p>
            </div>
            <div class="detail-item">
                <label>Status</label>
                <p><span class="status-badge status-<?php echo $view_contact['status']; ?>"><?php echo ucfirst($view_contact['status']); ?></span></p>
            </div>
            <div class="detail-item">
                <label>Course Interest</label>
                <p><?php echo htmlspecialchars($view_contact['course_interest'] ?: '-'); ?></p>
            </div>
            <div class="detail-item">
                <label>Year Interest</label>
                <p><?php echo htmlspecialchars($view_contact['year_interest'] ?: '-'); ?></p>
            </div>
            <div class="detail-item">
                <label>City / State</label>
                <p><?php echo htmlspecialchars($view_contact['city'] ?: '-'); ?> / <?php echo htmlspecialchars($view_contact['state'] ?: '-'); ?></p>
            </div>
            <div class="detail-item">
                <label>Source</label>
                <p><?php echo htmlspecialchars($view_contact['source'] ?: '-'); ?></p>
            </div>
            <div class="detail-item">
                <label>Subscribed</label>
                <p><?php echo $view_contact['is_subscribed'] ? 'Yes ✓' : 'No'; ?></p>
            </div>
            <div class="detail-item">
                <label>Date</label>
                <p><?php echo date('d M Y, h:i A', strtotime($view_contact['created_at'])); ?></p>
            </div>

            <div class="detail-item detail-full">
                <label>Subject</label>
                <p><strong><?php echo htmlspecialchars($view_contact['subject']); ?></strong></p>
            </div>

            <div class="detail-item detail-full">
                <label>Message</label>
                <div class="message-box">
                    <?php echo nl2br(htmlspecialchars($view_contact['message'])); ?>
                </div>
            </div>

            <?php if (!empty($view_contact['reply_message'])): ?>
            <div class="detail-item detail-full">
                <label>Reply Message</label>
                <div class="message-box" style="border-left-color: #28a745;">
                    <?php echo nl2br(htmlspecialchars($view_contact['reply_message'])); ?>
                    <p style="margin-top: 15px; color: #7f8c8d; font-size: 0.9em;">
                        <strong>Replied by:</strong> <?php echo htmlspecialchars($view_contact['replied_by']); ?> on 
                        <?php echo date('d M Y, h:i A', strtotime($view_contact['replied_at'])); ?>
                    </p>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($view_contact['admin_remarks'])): ?>
            <div class="detail-item detail-full">
                <label>Admin Remarks</label>
                <p><?php echo nl2br(htmlspecialchars($view_contact['admin_remarks'])); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Reply Form -->
        <div class="reply-form">
            <h4 style="margin-bottom: 20px;">📝 Reply / Update Status</h4>
            <form method="POST">
                <input type="hidden" name="contact_id" value="<?php echo $view_contact['id']; ?>">

                <div class="detail-grid">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="new" <?php echo $view_contact['status'] == 'new' ? 'selected' : ''; ?>>New</option>
                            <option value="read" <?php echo $view_contact['status'] == 'read' ? 'selected' : ''; ?>>Read</option>
                            <option value="replied" <?php echo $view_contact['status'] == 'replied' ? 'selected' : ''; ?>>Replied</option>
                            <option value="resolved" <?php echo $view_contact['status'] == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                            <option value="closed" <?php echo $view_contact['status'] == 'closed' ? 'selected' : ''; ?>>Closed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Priority</label>
                        <select name="priority" class="form-control">
                            <option value="low" <?php echo $view_contact['priority'] == 'low' ? 'selected' : ''; ?>>Low</option>
                            <option value="medium" <?php echo $view_contact['priority'] == 'medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="high" <?php echo $view_contact['priority'] == 'high' ? 'selected' : ''; ?>>High</option>
                            <option value="urgent" <?php echo $view_contact['priority'] == 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Assigned To</label>
                    <input type="text" name="assigned_to" class="form-control" 
                           value="<?php echo htmlspecialchars($view_contact['assigned_to'] ?: ''); ?>" 
                           placeholder="Assign to staff/admin">
                </div>

                <div class="form-group">
                    <label>Reply Message</label>
                    <textarea name="reply_message" class="form-control" placeholder="Type your reply message here..."><?php echo htmlspecialchars($view_contact['reply_message'] ?: ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Admin Remarks (Internal Notes)</label>
                    <textarea name="admin_remarks" class="form-control" placeholder="Internal notes/remarks..."><?php echo htmlspecialchars($view_contact['admin_remarks'] ?: ''); ?></textarea>
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" name="reply_contact" class="btn-submit">💾 Save & Reply</button>
                    <a href="Contact.php" class="btn-back">← Back to List</a>
                </div>
            </form>
        </div>

    </div>

    <?php endif; ?>

</div>

<script>
// Auto-hide alerts after 5 seconds
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