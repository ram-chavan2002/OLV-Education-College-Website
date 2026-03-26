<?php 
/**
 * Gallery Management - Images & Videos with Tags (Multiple Upload)
 * Location: /college/Gallery.php
 * Table: college_gallery
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
$page_title = "Gallery Management";

// ==================== CREATE - ADD MULTIPLE MEDIA FILES ====================
if (isset($_POST['add_multiple_media'])) {
    $default_category = mysqli_real_escape_string($db, $_POST['default_category']);
    $default_tags = mysqli_real_escape_string($db, $_POST['default_tags']);
    $status = mysqli_real_escape_string($db, $_POST['status']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    $upload_count = 0;
    $error_count = 0;
    
    if (isset($_FILES['media_files']) && !empty($_FILES['media_files']['name'][0])) {
        $total_files = count($_FILES['media_files']['name']);
        
        for ($i = 0; $i < $total_files; $i++) {
            if ($_FILES['media_files']['error'][$i] == 0) {
                
                $file_name = $_FILES['media_files']['name'][$i];
                $file_tmp = $_FILES['media_files']['tmp_name'][$i];
                $file_size = $_FILES['media_files']['size'][$i];
                $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $video_extensions = ['mp4', 'webm', 'ogg', 'avi', 'mov'];
                
                if (in_array($file_extension, $image_extensions)) {
                    $media_type = 'image';
                    $upload_dir = 'uploads/gallery/';
                } elseif (in_array($file_extension, $video_extensions)) {
                    $media_type = 'video';
                    $upload_dir = 'uploads/gallery/videos/';
                } else {
                    $error_count++;
                    continue;
                }
                
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $new_filename = $media_type . '_' . time() . '_' . rand(1000, 9999) . '_' . $i . '.' . $file_extension;
                $target_file = $upload_dir . $new_filename;
                
                if (move_uploaded_file($file_tmp, $target_file)) {
                    $title = ucwords(str_replace(['_', '-'], ' ', pathinfo($file_name, PATHINFO_FILENAME)));
                    $title = mysqli_real_escape_string($db, $title);
                    
                    $insert_query = "INSERT INTO college_gallery 
                                     (college_id, media_type, title, file_path, tags, category, status, featured, uploaded_by, created_at) 
                                     VALUES 
                                     ($college_id, '$media_type', '$title', '$target_file', '$default_tags', '$default_category', '$status', $featured, '$admin_name', NOW())";
                    
                    if (mysqli_query($db, $insert_query)) {
                        $upload_count++;
                    } else {
                        $error_count++;
                    }
                } else {
                    $error_count++;
                }
            }
        }
        
        if ($upload_count > 0) {
            $success_message = "$upload_count file(s) uploaded successfully!";
        }
        if ($error_count > 0) {
            $error_message = "$error_count file(s) failed to upload.";
        }
    } else {
        $error_message = "No files selected.";
    }
}

// ==================== CREATE - ADD SINGLE MEDIA ====================
if (isset($_POST['add_media'])) {
    $media_type = mysqli_real_escape_string($db, $_POST['media_type']);
    $title = mysqli_real_escape_string($db, $_POST['title']);
    $description = mysqli_real_escape_string($db, $_POST['description']);
    $tags = mysqli_real_escape_string($db, $_POST['tags']);
    $category = mysqli_real_escape_string($db, $_POST['category']);
    $status = mysqli_real_escape_string($db, $_POST['status']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $video_url = mysqli_real_escape_string($db, $_POST['video_url']);
    
    $file_path = '';
    $thumbnail = '';
    
    // Handle Image Upload
    if ($media_type == 'image' && isset($_FILES['media_file']) && $_FILES['media_file']['error'] == 0) {
        $upload_dir = 'uploads/gallery/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['media_file']['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            $file_name = 'img_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['media_file']['tmp_name'], $target_file)) {
                $file_path = $target_file;
            }
        } else {
            $error_message = "Invalid image format. Only JPG, PNG, GIF, WEBP allowed.";
        }
    }
    
    // Handle Video Upload (only if no YouTube URL)
    if ($media_type == 'video' && empty($video_url) && isset($_FILES['media_file']) && $_FILES['media_file']['error'] == 0) {
        $upload_dir = 'uploads/gallery/videos/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['media_file']['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['mp4', 'webm', 'ogg', 'avi', 'mov'];
        
        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            $file_name = 'video_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['media_file']['tmp_name'], $target_file)) {
                $file_path = $target_file;
            }
        } else {
            $error_message = "Invalid video format. Only MP4, WEBM, OGG, AVI, MOV allowed.";
        }
    }
    
    // Handle Video Thumbnail
    if ($media_type == 'video' && isset($_FILES['video_thumbnail']) && $_FILES['video_thumbnail']['error'] == 0) {
        $upload_dir = 'uploads/gallery/thumbnails/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['video_thumbnail']['name'], PATHINFO_EXTENSION);
        $file_name = 'thumb_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['video_thumbnail']['tmp_name'], $target_file)) {
            $thumbnail = $target_file;
        }
    }

    // Validation: Image must have file, Video must have file OR url
    if ($media_type == 'image' && empty($file_path) && !isset($error_message)) {
        $error_message = "Please select an image file to upload.";
    }

    if ($media_type == 'video' && empty($file_path) && empty($video_url) && !isset($error_message)) {
        $error_message = "Please upload a video file OR enter a YouTube/Vimeo URL.";
    }
    
    if (!isset($error_message)) {
        $insert_query = "INSERT INTO college_gallery 
                         (college_id, media_type, title, description, file_path, thumbnail, video_url, tags, category, status, featured, uploaded_by, created_at) 
                         VALUES 
                         ($college_id, '$media_type', '$title', '$description', '$file_path', '$thumbnail', '$video_url', '$tags', '$category', '$status', $featured, '$admin_name', NOW())";
        
        if (mysqli_query($db, $insert_query)) {
            $success_message = "Media added successfully!";
        } else {
            $error_message = "Error: " . mysqli_error($db);
        }
    }
}

// ==================== UPDATE - EDIT MEDIA ====================
if (isset($_POST['update_media'])) {
    $media_id = (int)$_POST['media_id'];
    $title = mysqli_real_escape_string($db, $_POST['title']);
    $description = mysqli_real_escape_string($db, $_POST['description']);
    $tags = mysqli_real_escape_string($db, $_POST['tags']);
    $category = mysqli_real_escape_string($db, $_POST['category']);
    $status = mysqli_real_escape_string($db, $_POST['status']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $video_url = mysqli_real_escape_string($db, $_POST['video_url']);
    
    $file_update = "";
    if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] == 0) {
        $media_info = mysqli_fetch_assoc(mysqli_query($db, "SELECT media_type, file_path FROM college_gallery WHERE id = $media_id"));
        
        if ($media_info['media_type'] == 'image') {
            $upload_dir = 'uploads/gallery/';
        } else {
            $upload_dir = 'uploads/gallery/videos/';
        }
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['media_file']['name'], PATHINFO_EXTENSION);
        $file_name = 'media_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['media_file']['tmp_name'], $target_file)) {
            if (!empty($media_info['file_path']) && file_exists($media_info['file_path'])) {
                unlink($media_info['file_path']);
            }
            $file_update = ", file_path = '$target_file'";
        }
    }
    
    $thumb_update = "";
    if (isset($_FILES['video_thumbnail']) && $_FILES['video_thumbnail']['error'] == 0) {
        $upload_dir = 'uploads/gallery/thumbnails/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['video_thumbnail']['name'], PATHINFO_EXTENSION);
        $file_name = 'thumb_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['video_thumbnail']['tmp_name'], $target_file)) {
            $old_thumb = mysqli_fetch_assoc(mysqli_query($db, "SELECT thumbnail FROM college_gallery WHERE id = $media_id"));
            if (!empty($old_thumb['thumbnail']) && file_exists($old_thumb['thumbnail'])) {
                unlink($old_thumb['thumbnail']);
            }
            $thumb_update = ", thumbnail = '$target_file'";
        }
    }
    
    $update_query = "UPDATE college_gallery 
                     SET title = '$title',
                         description = '$description',
                         tags = '$tags',
                         category = '$category',
                         video_url = '$video_url',
                         status = '$status',
                         featured = $featured
                         $file_update
                         $thumb_update
                     WHERE id = $media_id AND college_id = $college_id";
    
    if (mysqli_query($db, $update_query)) {
        $success_message = "Media updated successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($db);
    }
}

// ==================== DELETE - REMOVE MEDIA ====================
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    
    $get_files = mysqli_query($db, "SELECT file_path, thumbnail FROM college_gallery WHERE id = $delete_id AND college_id = $college_id");
    if ($row = mysqli_fetch_assoc($get_files)) {
        if (!empty($row['file_path']) && file_exists($row['file_path'])) {
            unlink($row['file_path']);
        }
        if (!empty($row['thumbnail']) && file_exists($row['thumbnail'])) {
            unlink($row['thumbnail']);
        }
    }
    
    $delete_query = "DELETE FROM college_gallery WHERE id = $delete_id AND college_id = $college_id";
    if (mysqli_query($db, $delete_query)) {
        $success_message = "Media deleted successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($db);
    }
}

// ==================== TOGGLE FEATURED ====================
if (isset($_GET['toggle_featured'])) {
    $media_id = (int)$_GET['toggle_featured'];
    $toggle_query = "UPDATE college_gallery SET featured = IF(featured = 1, 0, 1) WHERE id = $media_id AND college_id = $college_id";
    mysqli_query($db, $toggle_query);
    header("Location: Gallery.php");
    exit;
}

// ==================== READ - GET ALL MEDIA ====================
$filter_type = isset($_GET['type']) ? $_GET['type'] : 'all';
$filter_category = isset($_GET['category']) ? mysqli_real_escape_string($db, $_GET['category']) : 'all';
$search = isset($_GET['search']) ? mysqli_real_escape_string($db, $_GET['search']) : '';

$where_clause = "WHERE college_id = $college_id";

if ($filter_type != 'all') {
    $where_clause .= " AND media_type = '$filter_type'";
}

if ($filter_category != 'all') {
    $where_clause .= " AND category = '$filter_category'";
}

if (!empty($search)) {
    $where_clause .= " AND (title LIKE '%$search%' OR tags LIKE '%$search%' OR description LIKE '%$search%')";
}

$media_query = "SELECT * FROM college_gallery $where_clause ORDER BY featured DESC, id DESC";
$media_result = mysqli_query($db, $media_query);
$total_media = $media_result ? mysqli_num_rows($media_result) : 0;

$edit_media = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $edit_query = "SELECT * FROM college_gallery WHERE id = $edit_id AND college_id = $college_id";
    $edit_result = mysqli_query($db, $edit_query);
    $edit_media = mysqli_fetch_assoc($edit_result);
}

$categories_query = "SELECT DISTINCT category FROM college_gallery WHERE college_id = $college_id AND category IS NOT NULL AND category != '' ORDER BY category";
$categories_result = mysqli_query($db, $categories_query);

$current_page = basename($_SERVER['PHP_SELF']);

if (file_exists('header.php')) {
    require_once 'header.php';
} else {
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Gallery Management</title></head><body>';
}
?>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f5f6fa;
    color: #2c3e50;
}

.sidebar {
    position: fixed;
    left: 0; top: 0;
    width: 260px; height: 100vh;
    background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
    color: white;
    padding: 20px 0;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.sidebar-header { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
.sidebar-header h2 { font-size: 1.3em; font-weight: 600; }
.sidebar-menu { padding: 0 10px; }

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
.menu-item:hover { background: rgba(255,255,255,0.1); padding-left: 25px; }
.menu-item.active { background: #3498db; font-weight: 600; }

.main-content-area { margin-left: 260px; padding: 30px; min-height: 100vh; }

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
.page-header h2 { color: #2c3e50; font-size: 1.8em; }
.header-actions { display: flex; gap: 10px; }

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
    font-size: 0.9em;
}
.btn-add:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }

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

.filter-tabs { display: flex; gap: 10px; flex-wrap: wrap; }

.filter-tab {
    padding: 8px 20px;
    border-radius: 20px;
    background: #f8f9fa;
    color: #495057;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 0.9em;
}
.filter-tab:hover { background: #e9ecef; }
.filter-tab.active { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }

.search-box { flex: 1; min-width: 200px; }
.search-box input { width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 0.95em; }
.filter-box select { padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 0.95em; }

.alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

.form-container {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}
.form-container h3 { margin-bottom: 25px; color: #2c3e50; font-size: 1.4em; }

.form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px; }
.form-group { margin-bottom: 20px; }
.form-group.full-width { grid-column: 1 / -1; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 0.95em; }

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1em;
    transition: border 0.3s ease;
}
.form-control:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,0.1); }
textarea.form-control { resize: vertical; min-height: 80px; }

/* YouTube URL highlight box */
.youtube-hint {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 8px;
    padding: 10px 15px;
    font-size: 0.85em;
    color: #856404;
    margin-top: 8px;
}

.checkbox-label { display: flex; align-items: center; gap: 10px; font-weight: normal; cursor: pointer; }
.checkbox-label input[type="checkbox"] { width: 20px; height: 20px; cursor: pointer; }

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
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }

.btn-cancel {
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
    margin-left: 10px;
}
.btn-cancel:hover { background: #7f8c8d; }

.gallery-container {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.gallery-container h3 { margin-bottom: 25px; color: #2c3e50; font-size: 1.4em; }

.media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; }

.media-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}
.media-card:hover { transform: translateY(-5px); box-shadow: 0 5px 20px rgba(0,0,0,0.1); }

.media-thumbnail {
    width: 100%;
    height: 220px;
    object-fit: cover;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
}

/* YouTube thumbnail style */
.yt-thumb-wrapper {
    width: 100%;
    height: 220px;
    position: relative;
    overflow: hidden;
    cursor: pointer;
}
.yt-thumb-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.yt-play-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 60px;
    height: 60px;
    background: rgba(255,0,0,0.85);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5em;
    color: white;
    transition: all 0.3s;
}
.yt-thumb-wrapper:hover .yt-play-btn { background: red; transform: translate(-50%, -50%) scale(1.1); }

.video-play-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 60px; height: 60px;
    background: rgba(0,0,0,0.7);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2em;
    color: white;
}

.media-badge {
    position: absolute;
    top: 10px; left: 10px;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.8em;
    font-weight: 600;
    text-transform: uppercase;
}
.badge-image { background: #17a2b8; color: white; }
.badge-video { background: #e74c3c; color: white; }
.badge-youtube { background: #ff0000; color: white; }

.featured-star {
    position: absolute;
    top: 10px; right: 10px;
    background: #ffc107;
    color: #000;
    width: 35px; height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2em;
}

.media-content { padding: 15px; }
.media-title { font-size: 1.1em; color: #2c3e50; margin-bottom: 8px; font-weight: 600; }
.media-description {
    color: #7f8c8d;
    font-size: 0.85em;
    line-height: 1.5;
    margin-bottom: 10px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.media-tags { display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 10px; }
.tag { background: #e9ecef; color: #495057; padding: 3px 10px; border-radius: 12px; font-size: 0.75em; font-weight: 500; }

.media-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid #e9ecef; }
.media-category { background: #e3f2fd; color: #1976d2; padding: 4px 10px; border-radius: 12px; font-size: 0.8em; font-weight: 600; }
.media-actions { display: flex; gap: 8px; }

.btn-action { padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8em; font-weight: 500; transition: all 0.3s ease; }
.btn-view { background: #17a2b8; color: white; }
.btn-edit { background: #3498db; color: white; }
.btn-delete { background: #e74c3c; color: white; }
.btn-featured { background: #ffc107; color: #000; }
.btn-action:hover { transform: translateY(-2px); box-shadow: 0 3px 10px rgba(0,0,0,0.2); }

.no-data { text-align: center; padding: 60px 20px; color: #7f8c8d; }
.no-data-icon { font-size: 4em; margin-bottom: 20px; }

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.9);
    align-items: center;
    justify-content: center;
}
.modal.show { display: flex; }
.modal-content { max-width: 90%; max-height: 90%; position: relative; }
.modal-content img,
.modal-content video { max-width: 100%; max-height: 90vh; border-radius: 8px; }
.modal-close {
    position: absolute;
    top: -40px; right: 0;
    font-size: 2em;
    color: white;
    cursor: pointer;
    background: rgba(0,0,0,0.5);
    width: 40px; height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* File upload field disabled look */
.file-optional {
    opacity: 0.5;
    pointer-events: none;
}
.file-enabled {
    opacity: 1;
    pointer-events: all;
}

@media (max-width: 768px) {
    .sidebar { width: 100%; height: auto; position: relative; }
    .main-content-area { margin-left: 0; padding: 15px; }
    .page-header { flex-direction: column; text-align: center; }
    .filter-bar { flex-direction: column; }
    .form-row { grid-template-columns: 1fr; }
    .media-grid { grid-template-columns: 1fr; }
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
        <a href="complaint.php" class="menu-item <?php echo $current_page == 'complaint.php' ? 'active' : ''; ?>">👨‍🏫 Complaint</a>
        <a href="Gallery.php" class="menu-item <?php echo $current_page == 'Gallery.php' ? 'active' : ''; ?>">🖼️ Gallery</a>
        <a href="Contact.php" class="menu-item <?php echo $current_page == 'Contact.php' ? 'active' : ''; ?>">📧 Contact</a>
        <a href="logout.php" class="menu-item">🚪 Logout</a>
    </nav>
</div>

<!-- MAIN CONTENT -->
<div class="main-content-area">

    <div class="page-header">
        <h2>🖼️ Gallery Management (<?php echo $total_media; ?>)</h2>
        <div class="header-actions">
            <?php if (!isset($_GET['edit_id'])): ?>
                <button class="btn-add" onclick="document.getElementById('add-multiple-form').style.display='block'; window.scrollTo(0,0);">📤 Multiple Upload</button>
                <button class="btn-add" onclick="document.getElementById('add-single-form').style.display='block'; window.scrollTo(0,0);">➕ Single Upload</button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success">✓ <?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-error">✗ <?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Filter Bar -->
    <?php if (!$edit_media && !isset($_GET['edit_id'])): ?>
    <div class="filter-bar">
        <div class="filter-tabs">
            <a href="Gallery.php?type=all" class="filter-tab <?php echo $filter_type == 'all' ? 'active' : ''; ?>">All Media</a>
            <a href="Gallery.php?type=image" class="filter-tab <?php echo $filter_type == 'image' ? 'active' : ''; ?>">📷 Images</a>
            <a href="Gallery.php?type=video" class="filter-tab <?php echo $filter_type == 'video' ? 'active' : ''; ?>">🎥 Videos</a>
        </div>
        <form method="GET" style="display: flex; gap: 10px; flex: 1;">
            <input type="hidden" name="type" value="<?php echo $filter_type; ?>">
            <div class="search-box">
                <input type="text" name="search" placeholder="🔍 Search by title, tags..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="filter-box">
                <select name="category" onchange="this.form.submit()">
                    <option value="all" <?php echo $filter_category == 'all' ? 'selected' : ''; ?>>All Categories</option>
                    <?php 
                    mysqli_data_seek($categories_result, 0);
                    while ($cat = mysqli_fetch_assoc($categories_result)): 
                    ?>
                        <option value="<?php echo $cat['category']; ?>" <?php echo $filter_category == $cat['category'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Multiple Upload Form -->
    <?php if (!$edit_media): ?>
    <div class="form-container" id="add-multiple-form" style="display:none;">
        <h3>📤 Upload Multiple Files (Images & Videos)</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Select Multiple Files *</label>
                <input type="file" name="media_files[]" class="form-control" accept="image/*,video/*" multiple required>
                <small style="color:#7f8c8d;">
                    <strong>Images:</strong> JPG, PNG, GIF, WEBP | 
                    <strong>Videos:</strong> MP4, WEBM, OGG, AVI, MOV<br>
                    <strong>Tip:</strong> Hold Ctrl (Windows) or Cmd (Mac) to select multiple files
                </small>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Default Category (Optional)</label>
                    <input type="text" name="default_category" class="form-control" placeholder="e.g. Campus, Events">
                </div>
                <div class="form-group">
                    <label>Default Tags (Optional)</label>
                    <input type="text" name="default_tags" class="form-control" placeholder="campus, students, event">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="featured" value="1">
                        <span>⭐ Mark all as Featured</span>
                    </label>
                </div>
            </div>
            <div style="margin-top:20px;">
                <button type="submit" name="add_multiple_media" class="btn-submit">📤 Upload All Files</button>
                <button type="button" class="btn-cancel" onclick="document.getElementById('add-multiple-form').style.display='none';">✕ Cancel</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Single Upload Form -->
    <?php if ($edit_media || !isset($_GET['edit_id'])): ?>
    <div class="form-container" id="add-single-form" style="<?php echo $edit_media ? 'display:block;' : 'display:none;'; ?>">
        <h3><?php echo $edit_media ? '✏️ Edit Media' : '➕ Add Single Media (Detailed)'; ?></h3>
        <form method="POST" enctype="multipart/form-data" id="singleMediaForm">

            <?php if ($edit_media): ?>
                <input type="hidden" name="media_id" value="<?php echo $edit_media['id']; ?>">
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label>Media Type *</label>
                    <select name="media_type" class="form-control" id="mediaTypeSelect"
                            <?php echo $edit_media ? 'disabled' : ''; ?>
                            onchange="toggleMediaFields(this.value)">
                        <option value="image" <?php echo ($edit_media && $edit_media['media_type'] == 'image') ? 'selected' : ''; ?>>📷 Image</option>
                        <option value="video" <?php echo ($edit_media && $edit_media['media_type'] == 'video') ? 'selected' : ''; ?>>🎥 Video</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" class="form-control" required
                           value="<?php echo $edit_media ? htmlspecialchars($edit_media['title']) : ''; ?>"
                           placeholder="Enter media title">
                </div>
            </div>

            <div class="form-group full-width">
                <label>Description</label>
                <textarea name="description" class="form-control" placeholder="Enter media description"><?php echo $edit_media ? htmlspecialchars($edit_media['description']) : ''; ?></textarea>
            </div>

            <!-- ===== VIDEO URL FIELD (YouTube/Vimeo) ===== -->
            <div id="video-url-field" style="display:none;">
                <div class="form-group full-width">
                    <label>🎬 YouTube / Vimeo URL</label>
                    <input type="url" name="video_url" id="videoUrlInput" class="form-control"
                           value="<?php echo $edit_media ? htmlspecialchars($edit_media['video_url']) : ''; ?>"
                           placeholder="https://www.youtube.com/watch?v=..."
                           oninput="toggleFileRequirement()">
                    <div class="youtube-hint">
                        ✅ <strong>Sirf YouTube/Vimeo URL daalo</strong> — file upload karne ki zarurat nahi hogi!<br>
                        Supported: youtube.com, youtu.be, vimeo.com, youtube shorts
                    </div>
                </div>
            </div>

            <!-- ===== FILE UPLOAD ===== -->
            <div class="form-row">
                <div class="form-group" id="file-upload-group">
                    <label>Upload File <span id="file-required-label">*</span></label>
                    <input type="file" name="media_file" class="form-control" 
                           id="mediaFileInput"
                           accept="image/*,video/*">
                    <small style="color:#7f8c8d;" id="file-hint-text">Images: JPG, PNG, GIF, WEBP | Videos: MP4, WEBM, OGG, AVI, MOV</small>
                    <?php if ($edit_media && !empty($edit_media['file_path'])): ?>
                        <div style="margin-top:10px;">
                            <?php if ($edit_media['media_type'] == 'image'): ?>
                                <img src="<?php echo htmlspecialchars($edit_media['file_path']); ?>" style="width:100px; height:70px; object-fit:cover; border-radius:8px;">
                            <?php else: ?>
                                <a href="<?php echo htmlspecialchars($edit_media['file_path']); ?>" target="_blank" style="color:#3498db;">📄 View Current File</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group" id="thumbnail-field" style="display:none;">
                    <label>Video Thumbnail (Optional)</label>
                    <input type="file" name="video_thumbnail" class="form-control" accept="image/*">
                    <small style="color:#7f8c8d;">Custom thumbnail for uploaded video</small>
                    <?php if ($edit_media && !empty($edit_media['thumbnail'])): ?>
                        <div style="margin-top:10px;">
                            <img src="<?php echo htmlspecialchars($edit_media['thumbnail']); ?>" style="width:100px; height:70px; object-fit:cover; border-radius:8px;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tags</label>
                    <input type="text" name="tags" class="form-control"
                           value="<?php echo $edit_media ? htmlspecialchars($edit_media['tags']) : ''; ?>"
                           placeholder="campus, students, event (comma separated)">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control"
                           value="<?php echo $edit_media ? htmlspecialchars($edit_media['category']) : ''; ?>"
                           placeholder="e.g. Campus, Events, Facilities">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active" <?php echo ($edit_media && $edit_media['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($edit_media && $edit_media['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="featured" value="1" <?php echo ($edit_media && $edit_media['featured']) ? 'checked' : ''; ?>>
                        <span>⭐ Mark as Featured</span>
                    </label>
                </div>
            </div>

            <div style="margin-top:20px;">
                <button type="submit" name="<?php echo $edit_media ? 'update_media' : 'add_media'; ?>" class="btn-submit">
                    <?php echo $edit_media ? '💾 Update Media' : '➕ Add Media'; ?>
                </button>
                <?php if ($edit_media): ?>
                    <a href="Gallery.php" class="btn-cancel">✕ Cancel</a>
                <?php else: ?>
                    <button type="button" class="btn-cancel" onclick="document.getElementById('add-single-form').style.display='none';">✕ Cancel</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Media Gallery Grid -->
    <div class="gallery-container">
        <h3>📋 Media Gallery</h3>

        <?php if ($total_media > 0): ?>
            <div class="media-grid">
                <?php while ($media = mysqli_fetch_assoc($media_result)): 
                    // Check if it's a YouTube video
                    $is_youtube = !empty($media['video_url']) && (strpos($media['video_url'], 'youtube.com') !== false || strpos($media['video_url'], 'youtu.be') !== false);
                    $is_vimeo = !empty($media['video_url']) && strpos($media['video_url'], 'vimeo.com') !== false;
                    
                    // Extract YouTube video ID
                    $yt_id = '';
                    if ($is_youtube) {
                        if (strpos($media['video_url'], 'youtu.be/') !== false) {
                            $yt_id = explode('?', explode('youtu.be/', $media['video_url'])[1])[0];
                        } elseif (strpos($media['video_url'], 'shorts/') !== false) {
                            $yt_id = explode('?', explode('shorts/', $media['video_url'])[1])[0];
                        } elseif (strpos($media['video_url'], 'v=') !== false) {
                            parse_str(parse_url($media['video_url'], PHP_URL_QUERY), $yt_params);
                            $yt_id = $yt_params['v'] ?? '';
                        }
                    }
                    $yt_thumb = $yt_id ? "https://img.youtube.com/vi/{$yt_id}/hqdefault.jpg" : '';
                ?>
                    <div class="media-card">

                        <?php if ($media['media_type'] == 'image'): ?>
                            <!-- IMAGE CARD -->
                            <?php if (!empty($media['file_path']) && file_exists($media['file_path'])): ?>
                                <img src="<?php echo htmlspecialchars($media['file_path']); ?>"
                                     alt="<?php echo htmlspecialchars($media['title']); ?>"
                                     class="media-thumbnail"
                                     style="cursor:pointer;"
                                     onclick="viewMedia('<?php echo htmlspecialchars($media['file_path']); ?>', 'image')">
                            <?php else: ?>
                                <div class="media-thumbnail" style="display:flex; align-items:center; justify-content:center; color:white; font-size:3em;">📷</div>
                            <?php endif; ?>
                            <span class="media-badge badge-image">Image</span>

                        <?php elseif ($is_youtube && $yt_id): ?>
                            <!-- YOUTUBE CARD -->
                            <div class="yt-thumb-wrapper" onclick="viewMedia('<?php echo htmlspecialchars($media['video_url']); ?>', 'video')">
                                <img src="<?php echo $yt_thumb; ?>" alt="<?php echo htmlspecialchars($media['title']); ?>">
                                <div class="yt-play-btn">▶</div>
                            </div>
                            <span class="media-badge badge-youtube">▶ YouTube</span>

                        <?php elseif ($is_vimeo): ?>
                            <!-- VIMEO CARD -->
                            <div class="media-thumbnail" style="display:flex; align-items:center; justify-content:center; background: #1ab7ea; cursor:pointer;"
                                 onclick="viewMedia('<?php echo htmlspecialchars($media['video_url']); ?>', 'video')">
                                <div style="color:white; font-size:3em; text-align:center;">
                                    <div>🎬</div>
                                    <div style="font-size:0.4em; margin-top:10px;">Vimeo Video</div>
                                </div>
                            </div>
                            <span class="media-badge badge-video">Vimeo</span>

                        <?php else: ?>
                            <!-- UPLOADED VIDEO CARD -->
                            <?php 
                            $thumb_src = !empty($media['thumbnail']) && file_exists($media['thumbnail']) 
                                ? $media['thumbnail'] 
                                : (!empty($media['file_path']) && file_exists($media['file_path']) ? $media['file_path'] : '');
                            ?>
                            <?php if ($thumb_src): ?>
                                <div style="position:relative;">
                                    <?php if (strpos($thumb_src, '.mp4') !== false || strpos($thumb_src, '.webm') !== false): ?>
                                        <video class="media-thumbnail" style="object-fit:cover;">
                                            <source src="<?php echo htmlspecialchars($thumb_src); ?>">
                                        </video>
                                    <?php else: ?>
                                        <img src="<?php echo htmlspecialchars($thumb_src); ?>" class="media-thumbnail">
                                    <?php endif; ?>
                                    <div class="video-play-icon" style="cursor:pointer;"
                                         onclick="viewMedia('<?php echo htmlspecialchars($media['file_path']); ?>', 'video')">▶</div>
                                </div>
                            <?php else: ?>
                                <div class="media-thumbnail" style="display:flex; align-items:center; justify-content:center; color:white; font-size:3em; cursor:pointer;"
                                     onclick="viewMedia('<?php echo htmlspecialchars($media['file_path']); ?>', 'video')">🎥</div>
                            <?php endif; ?>
                            <span class="media-badge badge-video">Video</span>
                        <?php endif; ?>

                        <?php if ($media['featured']): ?>
                            <span class="featured-star">⭐</span>
                        <?php endif; ?>

                        <div class="media-content">
                            <h4 class="media-title"><?php echo htmlspecialchars($media['title']); ?></h4>

                            <?php if ($media['description']): ?>
                                <p class="media-description"><?php echo htmlspecialchars($media['description']); ?></p>
                            <?php endif; ?>

                            <?php if ($media['tags']): ?>
                                <div class="media-tags">
                                    <?php 
                                    $tags = explode(',', $media['tags']);
                                    foreach (array_slice($tags, 0, 3) as $tag): 
                                    ?>
                                        <span class="tag">#<?php echo trim($tag); ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($tags) > 3): ?>
                                        <span class="tag">+<?php echo count($tags) - 3; ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="media-footer">
                                <?php if ($media['category']): ?>
                                    <span class="media-category"><?php echo htmlspecialchars($media['category']); ?></span>
                                <?php else: ?>
                                    <span></span>
                                <?php endif; ?>

                                <div class="media-actions">
                                    <a href="javascript:void(0)"
                                       onclick="viewMedia('<?php echo $is_youtube || $is_vimeo ? htmlspecialchars($media['video_url']) : htmlspecialchars($media['file_path']); ?>', '<?php echo $media['media_type']; ?>')"
                                       class="btn-action btn-view" title="View">👁️</a>
                                    <a href="?toggle_featured=<?php echo $media['id']; ?>" class="btn-action btn-featured" title="Toggle Featured">⭐</a>
                                    <a href="?edit_id=<?php echo $media['id']; ?>" class="btn-action btn-edit" title="Edit">✏️</a>
                                    <a href="?delete_id=<?php echo $media['id']; ?>"
                                       class="btn-action btn-delete"
                                       onclick="return confirm('Are you sure you want to delete this media?')"
                                       title="Delete">🗑️</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-data">
                <div class="no-data-icon">🖼️</div>
                <p style="font-size:1.2em; font-weight:600;">No media found</p>
                <p style="margin-top:10px;">Click "Multiple Upload" or "Single Upload" button above to add media!</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- View Media Modal -->
<div id="mediaModal" class="modal" onclick="closeModal()">
    <div class="modal-content" onclick="event.stopPropagation()">
        <span class="modal-close" onclick="closeModal()">×</span>
        <div id="modalMediaContent"></div>
    </div>
</div>

<script>
// ===== Toggle media fields based on type =====
function toggleMediaFields(type) {
    const thumbnailField = document.getElementById('thumbnail-field');
    const videoUrlField  = document.getElementById('video-url-field');
    const fileInput      = document.getElementById('mediaFileInput');
    const fileLabel      = document.getElementById('file-required-label');
    const fileHint       = document.getElementById('file-hint-text');

    if (type === 'video') {
        thumbnailField.style.display = 'block';
        videoUrlField.style.display  = 'block';
        fileInput.removeAttribute('required');
        fileLabel.textContent = '(Optional if YouTube URL given)';
        fileHint.innerHTML = '<strong>Upload a video file</strong> OR paste YouTube/Vimeo URL below';
        toggleFileRequirement(); // check if url already filled
    } else {
        thumbnailField.style.display = 'none';
        videoUrlField.style.display  = 'none';
        fileInput.setAttribute('required', 'required');
        fileLabel.textContent = '*';
        fileHint.textContent = 'Images: JPG, PNG, GIF, WEBP';
        fileInput.style.opacity = '1';
    }
}

// ===== If YouTube URL filled, file upload not required =====
function toggleFileRequirement() {
    const urlInput  = document.getElementById('videoUrlInput');
    const fileInput = document.getElementById('mediaFileInput');
    const fileGroup = document.getElementById('file-upload-group');

    if (urlInput && urlInput.value.trim() !== '') {
        fileInput.removeAttribute('required');
        fileGroup.style.opacity = '0.5';
        fileGroup.title = 'Not required when YouTube/Vimeo URL is provided';
    } else {
        fileGroup.style.opacity = '1';
        fileGroup.title = '';
    }
}

// ===== Initialize on page load =====
document.addEventListener('DOMContentLoaded', function() {
    const mediaTypeSelect = document.getElementById('mediaTypeSelect');
    if (mediaTypeSelect) {
        toggleMediaFields(mediaTypeSelect.value);
    }
});

// ===== Extract YouTube ID helper =====
function getYouTubeId(url) {
    let id = '';
    if (url.includes('youtu.be/')) {
        id = url.split('youtu.be/')[1].split('?')[0];
    } else if (url.includes('shorts/')) {
        id = url.split('shorts/')[1].split('?')[0];
    } else if (url.includes('v=')) {
        const params = new URLSearchParams(new URL(url).search);
        id = params.get('v') || '';
    }
    return id;
}

// ===== View Media Modal =====
function viewMedia(src, type) {
    const modal   = document.getElementById('mediaModal');
    const content = document.getElementById('modalMediaContent');
    content.innerHTML = '';

    if (type === 'image') {
        content.innerHTML = '<img src="' + src + '" style="max-width:100%; max-height:90vh; border-radius:8px;">';

    } else if (type === 'video') {
        if (src.includes('youtube.com') || src.includes('youtu.be')) {
            const videoId = getYouTubeId(src);
            content.innerHTML = '<iframe width="800" height="450" src="https://www.youtube.com/embed/' + videoId + '?autoplay=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen style="border-radius:8px;"></iframe>';

        } else if (src.includes('vimeo.com')) {
            const videoId = src.split('/').pop();
            content.innerHTML = '<iframe width="800" height="450" src="https://player.vimeo.com/video/' + videoId + '?autoplay=1" frameborder="0" allowfullscreen style="border-radius:8px;"></iframe>';

        } else {
            content.innerHTML = '<video controls autoplay style="max-width:100%; max-height:90vh; border-radius:8px;"><source src="' + src + '" type="video/mp4">Your browser does not support video.</video>';
        }
    }

    modal.classList.add('show');
}

// ===== Close Modal =====
function closeModal() {
    document.getElementById('mediaModal').classList.remove('show');
    document.getElementById('modalMediaContent').innerHTML = ''; // stop video
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>

<?php if (file_exists('footer.php')) {
    require_once 'footer.php';
} else {
    echo '</body></html>';
} ?>