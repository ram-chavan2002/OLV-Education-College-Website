<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Database connection - SAFE VERSION
try {
    if (file_exists('db.php')) {
        require_once 'db.php';
    } elseif (file_exists('../db.php')) {
        require_once '../db.php';
    } else {
        throw new Exception("db.php not found");
    }
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage() . "<br>Create db.php with: <pre>?php \$db = mysqli_connect('localhost','username','password','database'); ?></pre>");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$college_id = 1; // Default
if (isset($_SESSION['college_id'])) $college_id = (int)$_SESSION['college_id'];
$success_message = '';
$error_message = '';

// Handle file upload directory
$upload_dir = 'uploads/teachers/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// CREATE with image
if (isset($_POST['add_teacher'])) {
    $name = mysqli_real_escape_string($GLOBALS['db'], $_POST['name']);
    $surname = mysqli_real_escape_string($GLOBALS['db'], $_POST['surname']);
    $dateofjoining = mysqli_real_escape_string($GLOBALS['db'], $_POST['dateofjoining']);
    $basic = mysqli_real_escape_string($GLOBALS['db'], $_POST['basic']);
    $highest_qualification = mysqli_real_escape_string($GLOBALS['db'], $_POST['highest_qualification']);
    $image_path = '';

    // Handle image upload
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] == 0) {
        $file_name = time() . '_' . basename($_FILES['image_path']['name']);
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['image_path']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }

    $query = "INSERT INTO college_teachers (college_id,name,surname,dateofjoining,basic,highest_qualification,image_path,created_at) 
              VALUES ($college_id,'$name','$surname','$dateofjoining','$basic','$highest_qualification','$image_path',NOW())";
    
    if (mysqli_query($GLOBALS['db'], $query)) {
        $success_message = "✅ Teacher added with image!";
        $edit_teacher = null;
    } else {
        $error_message = "❌ " . mysqli_error($GLOBALS['db']);
    }
}

// UPDATE with image
if (isset($_POST['update_teacher'])) {
    $teacher_id = (int)$_POST['teacher_id'];
    $name = mysqli_real_escape_string($GLOBALS['db'], $_POST['name']);
    $surname = mysqli_real_escape_string($GLOBALS['db'], $_POST['surname']);
    $dateofjoining = mysqli_real_escape_string($GLOBALS['db'], $_POST['dateofjoining']);
    $basic = mysqli_real_escape_string($GLOBALS['db'], $_POST['basic']);
    $highest_qualification = mysqli_real_escape_string($GLOBALS['db'], $_POST['highest_qualification']);
    $image_path = isset($_POST['existing_image']) ? $_POST['existing_image'] : '';

    // Handle new image upload
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] == 0) {
        $file_name = time() . '_' . basename($_FILES['image_path']['name']);
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['image_path']['tmp_name'], $target_file)) {
            // Delete old image if exists
            if ($image_path && file_exists($image_path)) {
                unlink($image_path);
            }
            $image_path = $target_file;
        }
    }

    $query = "UPDATE college_teachers SET name='$name',surname='$surname',dateofjoining='$dateofjoining',basic='$basic',highest_qualification='$highest_qualification',image_path='$image_path' 
              WHERE id=$teacher_id AND college_id=$college_id";
    
    if (mysqli_query($GLOBALS['db'], $query)) {
        $success_message = "✅ Teacher updated with image!";
        $edit_teacher = null;
    } else {
        $error_message = "❌ " . mysqli_error($GLOBALS['db']);
    }
}

// DELETE
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $teacher = mysqli_fetch_assoc(mysqli_query($GLOBALS['db'], "SELECT image_path FROM college_teachers WHERE id=$delete_id AND college_id=$college_id"));
    if ($teacher && $teacher['image_path'] && file_exists($teacher['image_path'])) {
        unlink($teacher['image_path']);
    }
    mysqli_query($GLOBALS['db'], "DELETE FROM college_teachers WHERE id=$delete_id AND college_id=$college_id");
    header("Location: teachers.php");
    exit;
}

// READ
$search = isset($_GET['search']) ? mysqli_real_escape_string($GLOBALS['db'], $_GET['search']) : '';
$where = "WHERE college_id = $college_id";
if ($search) $where .= " AND (name LIKE '%$search%' OR surname LIKE '%$search%' OR highest_qualification LIKE '%$search%')";

$result = mysqli_query($GLOBALS['db'], "SELECT * FROM college_teachers $where ORDER BY id DESC");
$total = mysqli_num_rows($result);

$edit_teacher = null;
$is_edit_mode = false;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $edit_result = mysqli_query($GLOBALS['db'], "SELECT * FROM college_teachers WHERE id=$edit_id AND college_id=$college_id");
    $edit_teacher = mysqli_fetch_assoc($edit_result);
    $is_edit_mode = true;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Teachers</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
* {margin:0;padding:0;box-sizing:border-box;font-family:Segoe UI,Tahoma,sans-serif;}
body {background:#f5f6fa;color:#333;}
.sidebar {position:fixed;left:0;top:0;width:250px;height:100vh;background:#2c3e50;color:#fff;padding:20px;overflow:auto;}
.sidebar h2 {padding:20px 0;border-bottom:1px solid #34495e;margin-bottom:20px;}
.menu-item {display:block;padding:12px 20px;color:#ecf0f1;text-decoration:none;border-radius:6px;margin:5px 0;transition:0.3s;}
.menu-item:hover,.menu-item.active {background:#3498db;}
.main {margin-left:250px;padding:20px;min-height:100vh;}
.header {background:#fff;padding:20px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;}
.header h1 {color:#2c3e50;font-size:1.8em;}
.btn {padding:10px 20px;border:none;border-radius:6px;cursor:pointer;font-weight:600;text-decoration:none;display:inline-block;transition:0.3s;}
.btn-primary {background:#667eea;color:#fff;}
.btn-primary:hover {background:#5a67d8;transform:translateY(-1px);}
.search {background:#fff;padding:20px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);margin-bottom:20px;}
.search form {display:flex;gap:10px;flex-wrap:wrap;}
.search input {flex:1;padding:10px;border:1px solid #ddd;border-radius:6px;min-width:200px;}
.form-box {background:#fff;padding:25px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);margin-bottom:20px;display:none;}
.form-box.show {display:block;}
.form-box h3 {margin-bottom:20px;color:#2c3e50;}
.form-grid {display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-bottom:20px;}
.form-group {margin-bottom:15px;}
.form-group label {display:block;margin-bottom:5px;font-weight:600;color:#555;}
.form-group input,.form-group select,.form-group textarea {width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-size:14px;}
.form-group input[type="file"] {padding:8px;}
.image-preview {max-width:120px;max-height:120px;border-radius:8px;margin-top:8px;display:block;}
.btn-success {background:#28a745;color:#fff;}
.btn-success:hover {background:#218838;}
.btn-secondary {background:#6c757d;color:#fff;}
.table-box {background:#fff;padding:25px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
.table-box h3 {margin-bottom:20px;color:#2c3e50;}
table {width:100%;border-collapse:collapse;}
th,td {padding:12px;text-align:left;border-bottom:1px solid #eee;}
th {background:#f8f9fa;font-weight:600;color:#495057;}
tr:hover {background:#f8f9fa;}
.btn-sm {padding:5px 10px;font-size:12px;border-radius:4px;margin:0 2px;}
.btn-edit {background:#007bff;color:#fff;}
.btn-delete {background:#dc3545;color:#fff;}
.alert {padding:15px;margin-bottom:20px;border-radius:6px;font-weight:500;}
.alert-success {background:#d4edda;color:#155724;border:1px solid #c3e6cb;}
.alert-danger {background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}
.no-data {text-align:center;padding:50px;color:#6c757d;}
.no-data i {font-size:4em;margin-bottom:20px;display:block;}
.teacher-avatar {width:45px;height:45px;border-radius:50%;background:#667eea;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:16px;margin-right:12px;}
.teacher-photo {width:50px;height:50px;border-radius:8px;object-fit:cover;margin-right:12px;box-shadow:0 2px 8px rgba(0,0,0,0.1);}
@media (max-width:768px) {.sidebar {width:100%;height:auto;position:relative;}.main {margin-left:0;}.header {flex-direction:column;text-align:center;gap:10px;}.search form {flex-direction:column;}.form-grid {grid-template-columns:1fr;}}
</style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fas fa-graduation-cap"></i> 🎓 College Admin</h2>
    <a href="/college/orc_sch/dashbored1.php" class="menu-item"><i class="fas fa-tachometer-alt"></i> 🏠 Dashboard</a>
    <a href="/college/orc_sch/course.php" class="menu-item"><i class="fas fa-book-open"></i> 📚 Course</a>
    <a href="/college/orc_sch/teachers.php" class="menu-item active"><i class="fas fa-chalkboard-teacher"></i> 👨‍🏫 Teachers</a>
    <a href="/college/orc_sch/complaint.php" class="menu-item"><i class="fas fa-exclamation-triangle"></i> 📝 Complaint</a>
    <a href="/college/orc_sch/gallery.php" class="menu-item"><i class="fas fa-images"></i> 🖼️ Gallery</a>
    <a href="/college/orc_sch/contact.php" class="menu-item"><i class="fas fa-envelope"></i> 📧 Contact</a>
    <a href="/college/orc_sch/logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i> 🚪 Logout</a>
</div>

<div class="main">
    <div class="header">
        <h1><i class="fas fa-users"></i> 👨‍🏫 Teachers (<?php echo $total; ?>)</h1>
        <?php if (!$is_edit_mode): ?>
            <button class="btn btn-primary" onclick="toggleForm('teacher-form')">
                <i class="fas fa-plus"></i> ➕ Add Teacher
            </button>
        <?php endif; ?>
    </div>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if (!$is_edit_mode): ?>
    <div class="search">
        <form method="GET">
            <input type="text" name="search" placeholder="🔍 Search Name/Surname/Qualification" value="<?php echo htmlspecialchars($search ?? ''); ?>">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
            <?php if (isset($search) && $search): ?>
                <a href="teachers.php" class="btn btn-secondary"><i class="fas fa-times"></i> Clear</a>
            <?php endif; ?>
        </form>
    </div>
    <?php endif; ?>

    <!-- Form with Image Upload -->
    <div class="form-box <?php echo $is_edit_mode ? 'show' : ''; ?>" id="teacher-form">
        <h3><i class="fas fa-<?php echo $is_edit_mode ? 'edit' : 'user-plus'; ?>"></i> <?php echo $is_edit_mode ? 'Edit Teacher' : 'Add New Teacher'; ?></h3>
        <form method="POST" enctype="multipart/form-data">
            <?php if ($is_edit_mode): ?>
                <input type="hidden" name="teacher_id" value="<?php echo $edit_teacher['id']; ?>">
                <input type="hidden" name="update_teacher" value="1">
                <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($edit_teacher['image_path'] ?? ''); ?>">
            <?php else: ?>
                <input type="hidden" name="add_teacher" value="1">
            <?php endif; ?>

            <div class="form-grid">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Name *</label>
                    <input type="text" name="name" required value="<?php echo $edit_teacher ? htmlspecialchars($edit_teacher['name']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Surname *</label>
                    <input type="text" name="surname" required value="<?php echo $edit_teacher ? htmlspecialchars($edit_teacher['surname']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calendar-alt"></i> DateOfJoining *</label>
                    <input type="date" name="dateofjoining" required value="<?php echo $edit_teacher ? $edit_teacher['dateofjoining'] : ''; ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-rupee-sign"></i> BASIC *</label>
                    <input type="text" name="basic" required value="<?php echo $edit_teacher ? htmlspecialchars($edit_teacher['basic']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-graduation-cap"></i> HIGHEST QUALIFICATION *</label>
                    <input type="text" name="highest_qualification" required value="<?php echo $edit_teacher ? htmlspecialchars($edit_teacher['highest_qualification']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-image"></i> Teacher Photo</label>
                    <input type="file" name="image_path" accept="image/*">
                    <?php if ($is_edit_mode && $edit_teacher['image_path']): ?>
                        <img src="<?php echo htmlspecialchars($edit_teacher['image_path']); ?>" alt="Current Photo" class="image-preview">
                        <small class="text-muted">Upload new photo to replace</small>
                    <?php endif; ?>
                </div>
            </div>

            <div style="text-align:right;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-<?php echo $is_edit_mode ? 'save' : 'plus'; ?>"></i> 
                    <?php echo $is_edit_mode ? 'Update Teacher' : 'Add Teacher'; ?>
                </button>
                <?php if ($is_edit_mode): ?>
                    <a href="teachers.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Table with Image Column -->
    <div class="table-box">
        <h3><i class="fas fa-list"></i> All Teachers</h3>
        <?php if ($total > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-image"></i> Photo</th>
                        <th><i class="fas fa-user"></i> Name</th>
                        <th><i class="fas fa-user"></i> Surname</th>
                        <th><i class="fas fa-calendar"></i> DateOfJoining</th>
                        <th><i class="fas fa-money-bill-wave"></i> BASIC</th>
                        <th><i class="fas fa-award"></i> HIGHEST QUALIFICATION</th>
                        <th><i class="fas fa-cogs"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($result, 0);
                    while ($row = mysqli_fetch_assoc($result)):
                    ?>
                    <tr>
                        <td>
                            <?php if ($row['image_path'] && file_exists($row['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="teacher-photo">
                            <?php else: ?>
                                <div class="teacher-avatar"><?php echo strtoupper(substr($row['name'],0,1)); ?></div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['surname']); ?></td>
                        <td><?php echo date('d-m-Y', strtotime($row['dateofjoining'])); ?></td>
                        <td><i class="fas fa-rupee-sign"></i> <?php echo htmlspecialchars($row['basic']); ?></td>
                        <td><?php echo htmlspecialchars($row['highest_qualification']); ?></td>
                        <td>
                            <a href="?edit_id=<?php echo $row['id']; ?>" class="btn-sm btn-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?delete_id=<?php echo $row['id']; ?>" class="btn-sm btn-delete" onclick="return confirm('Delete teacher and photo?')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-chalkboard-teacher"></i>
                <p>No teachers found</p>
                <p>Add your first teacher above!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleForm(id) {
    var form = document.getElementById(id);
    form.classList.toggle('show');
}

// Image preview on form
document.querySelector('input[type="file"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            let preview = document.querySelector('.image-preview');
            if (!preview) {
                preview = document.createElement('img');
                preview.className = 'image-preview';
                preview.alt = 'Preview';
                e.target.parentNode.appendChild(preview);
            }
            preview.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

// Auto hide alerts
setTimeout(function() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(el) {
        el.style.transition = 'opacity 0.5s';
        el.style.opacity = '0';
        setTimeout(function() { el.remove(); }, 500);
    });
}, 4000);
</script>

</body>
</html>
