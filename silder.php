<?php 
/**
 * Slider Management Content (For Dashboard Include)
 * Location: /public_html/college/slider.php
 */

// Only run if not already in a session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection (only if not already connected)
if (!isset($db)) {
    require_once 'db.php';
}

// Get college_id
$college_id = isset($_SESSION['college_id']) ? (int)$_SESSION['college_id'] : 1;

// ==================== HANDLE FORM SUBMISSIONS ====================

// Add New Slider
if (isset($_POST['add_slider'])) {
    $title = mysqli_real_escape_string($db, $_POST['title']);
    $description = mysqli_real_escape_string($db, $_POST['description']);
    $status = mysqli_real_escape_string($db, $_POST['status']);
    $display_order = (int)$_POST['display_order'];

    // Handle file upload
    $image_path = '';
    if (isset($_FILES['slider_image']) && $_FILES['slider_image']['error'] == 0) {
        $upload_dir = 'uploads/sliders/';

        // Create directory if not exists
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['slider_image']['name'], PATHINFO_EXTENSION);
        $file_name = 'slider_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['slider_image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }

    $insert_query = "INSERT INTO slider (college_id, title, description, image_path, status, display_order, created_at) 
                     VALUES ($college_id, '$title', '$description', '$image_path', '$status', $display_order, NOW())";

    if (mysqli_query($db, $insert_query)) {
        $slider_success_message = "Slider added successfully!";
    } else {
        $slider_error_message = "Error: " . mysqli_error($db);
    }
}

// Update Slider
if (isset($_POST['update_slider'])) {
    $slider_id = (int)$_POST['slider_id'];
    $title = mysqli_real_escape_string($db, $_POST['title']);
    $description = mysqli_real_escape_string($db, $_POST['description']);
    $status = mysqli_real_escape_string($db, $_POST['status']);
    $display_order = (int)$_POST['display_order'];

    $update_query = "UPDATE slider SET 
                     title = '$title', 
                     description = '$description', 
                     status = '$status', 
                     display_order = $display_order";

    // Handle new image upload
    if (isset($_FILES['slider_image']) && $_FILES['slider_image']['error'] == 0) {
        // Delete old image
        $old_image_query = mysqli_query($db, "SELECT image_path FROM slider WHERE id = $slider_id AND college_id = $college_id");
        if ($old_row = mysqli_fetch_assoc($old_image_query)) {
            if (!empty($old_row['image_path']) && file_exists($old_row['image_path'])) {
                unlink($old_row['image_path']);
            }
        }

        $upload_dir = 'uploads/sliders/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['slider_image']['name'], PATHINFO_EXTENSION);
        $file_name = 'slider_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['slider_image']['tmp_name'], $target_file)) {
            $update_query .= ", image_path = '$target_file'";
        }
    }

    $update_query .= " WHERE id = $slider_id AND college_id = $college_id";

    if (mysqli_query($db, $update_query)) {
        $slider_success_message = "Slider updated successfully!";
    } else {
        $slider_error_message = "Error: " . mysqli_error($db);
    }
}

// Delete Slider
if (isset($_GET['delete_slider_id'])) {
    $delete_id = (int)$_GET['delete_slider_id'];

    // Get image path before deleting
    $get_image = mysqli_query($db, "SELECT image_path FROM slider WHERE id = $delete_id AND college_id = $college_id");
    if ($row = mysqli_fetch_assoc($get_image)) {
        if (!empty($row['image_path']) && file_exists($row['image_path'])) {
            unlink($row['image_path']); // Delete image file
        }
    }

    $delete_query = "DELETE FROM slider WHERE id = $delete_id AND college_id = $college_id";
    if (mysqli_query($db, $delete_query)) {
        $slider_success_message = "Slider deleted successfully!";
    }
}

// Toggle Slider Status
if (isset($_GET['toggle_slider_status'])) {
    $slider_id = (int)$_GET['toggle_slider_status'];
    $update_query = "UPDATE slider 
                     SET status = IF(status = 'active', 'inactive', 'active') 
                     WHERE id = $slider_id AND college_id = $college_id";
    mysqli_query($db, $update_query);
}

// Get slider for editing
$edit_slider = null;
if (isset($_GET['edit_slider_id'])) {
    $edit_id = (int)$_GET['edit_slider_id'];
    $edit_query = mysqli_query($db, "SELECT * FROM slider WHERE id = $edit_id AND college_id = $college_id");
    $edit_slider = mysqli_fetch_assoc($edit_query);
}

// Fetch all sliders
$sliders_query = "SELECT * FROM slider WHERE college_id = $college_id ORDER BY display_order ASC, id DESC";
$sliders_result = @mysqli_query($db, $sliders_query);
$total_sliders = $sliders_result ? mysqli_num_rows($sliders_result) : 0;
?>

<style>
/* Slider Specific Styles */
.slider-management-section {
    margin-bottom: 30px;
}

.slider-management-section h3 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 1.4em;
}

.slider-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.slider-btn-add {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 0.9em;
    cursor: pointer;
    border: none;
}

.slider-btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.slider-alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
}

.slider-alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.slider-alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.slider-form-container {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 30px;
    display: none;
}

.slider-form-container.show {
    display: block;
}

.slider-form-group {
    margin-bottom: 20px;
}

.slider-form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
}

.slider-form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1em;
    transition: border 0.3s ease;
}

.slider-form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

textarea.slider-form-control {
    resize: vertical;
    min-height: 100px;
}

.slider-btn-submit {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    font-size: 1em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-right: 10px;
}

.slider-btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.slider-btn-cancel {
    background: #95a5a6;
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    font-size: 1em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.slider-btn-cancel:hover {
    background: #7f8c8d;
}

.sliders-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.sliders-table th {
    background: #f8f9fa;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #2c3e50;
    border-bottom: 2px solid #e9ecef;
}

.sliders-table td {
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
}

.sliders-table tr:hover {
    background: #f8f9fa;
}

.slider-thumb {
    width: 100px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.status-badge {
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: 600;
    display: inline-block;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.slider-action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.slider-btn-action {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.85em;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-block;
}

.slider-btn-edit {
    background: #f39c12;
    color: white;
}

.slider-btn-toggle {
    background: #3498db;
    color: white;
}

.slider-btn-delete {
    background: #e74c3c;
    color: white;
}

.slider-btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
}

.slider-no-data {
    text-align: center;
    padding: 40px;
    color: #7f8c8d;
}
</style>

<!-- SLIDER MANAGEMENT CONTENT -->
<div class="slider-management-section">

    <!-- Success/Error Messages -->
    <?php if (isset($slider_success_message)): ?>
        <div class="slider-alert slider-alert-success">✓ <?php echo $slider_success_message; ?></div>
    <?php endif; ?>

    <?php if (isset($slider_error_message)): ?>
        <div class="slider-alert slider-alert-error">✗ <?php echo $slider_error_message; ?></div>
    <?php endif; ?>

    <div class="slider-section-header">
        <h3>🎞️ All Sliders (<?php echo $total_sliders; ?>)</h3>
        <button class="slider-btn-add" onclick="toggleSliderForm('add-slider-form', 'edit-slider-form')">➕ Add New Slider</button>
    </div>

    <!-- Add New Slider Form -->
    <div class="slider-form-container" id="add-slider-form">
        <h4 style="margin-bottom: 15px; color: #2c3e50;">➕ Add New Slider</h4>
        <form method="POST" enctype="multipart/form-data">

            <div class="slider-form-group">
                <label>Slider Title *</label>
                <input type="text" name="title" class="slider-form-control" required placeholder="Enter slider title">
            </div>

            <div class="slider-form-group">
                <label>Description</label>
                <textarea name="description" class="slider-form-control" placeholder="Enter slider description"></textarea>
            </div>

            <div class="slider-form-group">
                <label>Slider Image *</label>
                <input type="file" name="slider_image" class="slider-form-control" accept="image/*" required>
                <small style="color: #7f8c8d;">Recommended size: 1920x600 pixels</small>
            </div>

            <div class="slider-form-group">
                <label>Display Order</label>
                <input type="number" name="display_order" class="slider-form-control" value="0" min="0">
                <small style="color: #7f8c8d;">Lower number = Higher priority</small>
            </div>

            <div class="slider-form-group">
                <label>Status</label>
                <select name="status" class="slider-form-control">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <button type="submit" name="add_slider" class="slider-btn-submit">💾 Add Slider</button>
            <button type="button" class="slider-btn-cancel" onclick="toggleSliderForm('add-slider-form')">Cancel</button>
        </form>
    </div>

    <!-- Edit Slider Form -->
    <?php if ($edit_slider): ?>
    <div class="slider-form-container show" id="edit-slider-form">
        <h4 style="margin-bottom: 15px; color: #2c3e50;">✏️ Edit Slider</h4>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="slider_id" value="<?php echo $edit_slider['id']; ?>">

            <div class="slider-form-group">
                <label>Slider Title *</label>
                <input type="text" name="title" class="slider-form-control" required value="<?php echo htmlspecialchars($edit_slider['title']); ?>">
            </div>

            <div class="slider-form-group">
                <label>Description</label>
                <textarea name="description" class="slider-form-control"><?php echo htmlspecialchars($edit_slider['description']); ?></textarea>
            </div>

            <div class="slider-form-group">
                <label>Slider Image</label>
                <?php if (!empty($edit_slider['image_path']) && file_exists($edit_slider['image_path'])): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="<?php echo htmlspecialchars($edit_slider['image_path']); ?>" style="max-width: 300px; border-radius: 8px;" alt="Current">
                    </div>
                <?php endif; ?>
                <input type="file" name="slider_image" class="slider-form-control" accept="image/*">
                <small style="color: #7f8c8d;">Leave empty to keep current image</small>
            </div>

            <div class="slider-form-group">
                <label>Display Order</label>
                <input type="number" name="display_order" class="slider-form-control" value="<?php echo $edit_slider['display_order']; ?>" min="0">
            </div>

            <div class="slider-form-group">
                <label>Status</label>
                <select name="status" class="slider-form-control">
                    <option value="active" <?php echo $edit_slider['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $edit_slider['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>

            <button type="submit" name="update_slider" class="slider-btn-submit">💾 Update Slider</button>
            <a href="dashboard.php" class="slider-btn-cancel">Cancel</a>
        </form>
    </div>
    <?php endif; ?>

    <!-- Existing Sliders List -->
    <?php if ($total_sliders > 0): ?>
        <table class="sliders-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($slider = mysqli_fetch_assoc($sliders_result)): ?>
                    <tr>
                        <td>
                            <?php if (!empty($slider['image_path']) && file_exists($slider['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($slider['image_path']); ?>" 
                                     alt="Slider" class="slider-thumb">
                            <?php else: ?>
                                <div style="width:100px;height:60px;background:#ddd;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#999;font-size:0.8em;">No Image</div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($slider['title']); ?></strong></td>
                        <td><?php echo htmlspecialchars(substr($slider['description'], 0, 60)) . (strlen($slider['description']) > 60 ? '...' : ''); ?></td>
                        <td>#<?php echo $slider['display_order']; ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $slider['status']; ?>">
                                <?php echo ucfirst($slider['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="slider-action-buttons">
                                <a href="?edit_slider_id=<?php echo $slider['id']; ?>#edit-slider-form" 
                                   class="slider-btn-action slider-btn-edit"
                                   title="Edit Slider">
                                    ✏️ Edit
                                </a>
                                <a href="?toggle_slider_status=<?php echo $slider['id']; ?>" 
                                   class="slider-btn-action slider-btn-toggle"
                                   title="Toggle Status">
                                    🔄 Toggle
                                </a>
                                <a href="?delete_slider_id=<?php echo $slider['id']; ?>" 
                                   class="slider-btn-action slider-btn-delete"
                                   onclick="return confirm('Are you sure you want to delete this slider?')"
                                   title="Delete Slider">
                                    🗑️ Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="slider-no-data">
            <p>📭 No sliders found</p>
            <p style="margin-top: 10px;">Click "Add New Slider" button above to create your first slider!</p>
        </div>
    <?php endif; ?>

</div>

<script>
// Toggle slider form visibility
function toggleSliderForm(showFormId, hideFormId = null) {
    const showForm = document.getElementById(showFormId);
    if (showForm) {
        showForm.classList.toggle('show');
    }

    if (hideFormId) {
        const hideForm = document.getElementById(hideFormId);
        if (hideForm) {
            hideForm.classList.remove('show');
        }
    }
}

// Auto-hide slider alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.slider-alert');
    alerts.forEach(function(alert) {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(function() {
            alert.remove();
        }, 500);
    });
}, 5000);
</script>
