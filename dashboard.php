<?php 
/**
 * College Dashboard (With Slider Management Included)
 * Location: /public_html/college/dashboard.php
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

$college_id = isset($_SESSION['college_id']) ? (int)$_SESSION['college_id'] : 1;
$admin_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
$page_title = "College Dashboard";
$current_page = basename($_SERVER['PHP_SELF']);

// ==================== GET STATISTICS ====================
$stats = [
    'total_students' => 0,
    'total_sliders' => 0,
    'total_gallery' => 0,
    'total_contacts' => 0,
    'total_courses' => 0
];

// Safe count function
function safeCount($db, $table, $college_id = null) {
    $table_name = mysqli_real_escape_string($db, $table);

    $check_query = "SHOW TABLES LIKE '$table_name'";
    $check_result = @mysqli_query($db, $check_query);

    if (!$check_result || mysqli_num_rows($check_result) == 0) {
        return 0;
    }

    if ($college_id !== null) {
        $college_id = (int)$college_id;
        $count_query = "SELECT COUNT(*) as count FROM `$table_name` WHERE college_id = $college_id";
    } else {
        $count_query = "SELECT COUNT(*) as count FROM `$table_name`";
    }

    $count_result = @mysqli_query($db, $count_query);

    if ($count_result) {
        $row = mysqli_fetch_assoc($count_result);
        return $row ? (int)$row['count'] : 0;
    }

    return 0;
}

// Get statistics
$stats['total_students'] = safeCount($db, 'students', $college_id);
$stats['total_sliders'] = safeCount($db, 'slider', $college_id);
$stats['total_gallery'] = safeCount($db, 'gallery', $college_id);
$stats['total_contacts'] = safeCount($db, 'contacts', $college_id);
$stats['total_courses'] = safeCount($db, 'courses', $college_id);

// Include header if exists
if (file_exists('header.php')) {
    require_once 'header.php';
} else {
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>College Dashboard</title></head><body>';
}
?>

<style>
/* ==================== DASHBOARD STYLES ==================== */

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

/* Sidebar */
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

/* Main Content */
.main-content-area {
    margin-left: 260px;
    padding: 30px;
    min-height: 100vh;
}

.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
}

/* Page Header */
.page-header {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.page-header h2 {
    color: #2c3e50;
    font-size: 2em;
    margin-bottom: 10px;
}

.page-header p {
    color: #7f8c8d;
    font-size: 1.05em;
}

/* Statistics Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8em;
}

.stat-icon.blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-icon.green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.stat-icon.orange { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-icon.purple { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-icon.teal { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

.stat-info h3 {
    font-size: 2em;
    color: #2c3e50;
    margin-bottom: 5px;
}

.stat-info p {
    color: #7f8c8d;
    font-size: 0.95em;
}

/* Quick Actions */
.actions-section {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.actions-section h3 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 1.4em;
}

.action-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.action-btn {
    padding: 15px 25px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    text-align: center;
    font-weight: 600;
    transition: all 0.3s ease;
    display: block;
}

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.action-btn.green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.action-btn.red { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.action-btn.purple { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.action-btn.orange { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.action-btn.teal { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

/* Slider Section Container */
.slider-included-section {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
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

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .action-buttons {
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
    <div class="dashboard-container">

        <!-- Page Header -->
        <div class="page-header">
            <h2>🎓 OLV School</h2>
            <p>Welcome back, <?php echo htmlspecialchars($admin_name); ?>! Manage your college content and data.</p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">

            <div class="stat-card">
                <div class="stat-icon blue">👨‍🎓</div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['total_students']); ?></h3>
                    <p>Total Students</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">🎞️</div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['total_sliders']); ?></h3>
                    <p>Total Sliders</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">🖼️</div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['total_gallery']); ?></h3>
                    <p>Gallery Images</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon purple">📧</div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['total_contacts']); ?></h3>
                    <p>Contact Messages</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon teal">📚</div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['total_courses']); ?></h3>
                    <p>Total Courses</p>
                </div>
            </div>

        </div>

        <!-- Quick Actions -->
        <div class="actions-section">
            <h3>⚡ Quick Actions</h3>
            <div class="action-buttons">
                <a href="#slider-management" class="action-btn" onclick="document.getElementById('add-slider-form').classList.toggle('show'); return false;">🎞️ Add New Slider</a>
                <a href="Gallery.php" class="action-btn green">🖼️ Manage Gallery</a>
                <a href="Course.php" class="action-btn orange">📚 Manage Courses</a>
                <a href="Contact.php" class="action-btn purple">📧 View Contacts</a>
                <a href="Donation.php" class="action-btn teal">💰 View Donations</a>
                <a href="About.php" class="action-btn red">ℹ️ About Page</a>
            </div>
        </div>

        <!-- SLIDER MANAGEMENT INCLUDED -->
        <div class="slider-included-section" id="slider-management">
            <?php
            // Include slider.php content
            $slider_file = __DIR__ . '/slider.php';

            if (file_exists($slider_file)) {
                include $slider_file;
            } else {
                echo '<h3>🎞️ Slider Management</h3>';
                echo '<p style="color: #7f8c8d;">Slider module not found.</p>';
            }
            ?>
        </div>

    </div>
</div>

<?php 
// Include footer if exists
if (file_exists('footer.php')) {
    require_once 'footer.php';
} else {
    echo '</body></html>';
}
?>
