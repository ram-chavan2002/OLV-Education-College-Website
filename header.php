<?php
/**
 * Header Template - Centered Layout
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
$admin_name = htmlspecialchars($_SESSION['username'] ?? 'Admin');
$admin_email = htmlspecialchars($_SESSION['email'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Dashboard'; ?> | Multi-School Management</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --text-dark: #333;
            --text-light: #666;
            --shadow-md: 0 4px 16px rgba(0,0,0,0.12);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fc;
            color: var(--text-dark);
        }

        /* ==================== HEADER ==================== */
        .header-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: white;
            box-shadow: var(--shadow-md);
        }

        /* ==================== TOP BAR (CENTERED) ==================== */
        .top-bar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 15px 30px;
            display: flex;
            justify-content: center; /* CENTER ALIGNMENT */
            align-items: center;
            position: relative;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .brand h1 {
            font-size: 20px;
            font-weight: 600;
            letter-spacing: -0.3px;
        }

        /* Logout Button - Positioned on Right */
        .user-section {
            position: absolute;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            text-align: right;
            color: white;
        }

        .user-info .name {
            font-size: 14px;
            font-weight: 600;
        }

        .user-info .email {
            font-size: 12px;
            opacity: 0.9;
        }

        .btn-logout {
            background: rgba(255,255,255,0.15);
            color: white;
            padding: 8px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: var(--transition);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .btn-logout:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-1px);
        }

        /* ==================== TABS (CENTERED) ==================== */
        .tabs-nav {
            background: white;
            padding: 0 30px;
            display: flex;
            justify-content: center; /* CENTER ALIGNMENT */
            align-items: center;
            gap: 8px;
            min-height: 60px;
        }

        .tab-link {
            padding: 16px 28px;
            color: var(--text-light);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            transition: var(--transition);
            border-bottom: 3px solid transparent;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tab-link:hover {
            color: var(--primary-color);
            background: rgba(102, 126, 234, 0.05);
            border-radius: 8px 8px 0 0;
        }

        .tab-link.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            background: rgba(102, 126, 234, 0.05);
            font-weight: 600;
        }

        .tab-icon {
            font-size: 18px;
        }

        /* ==================== CONTENT ==================== */
        .content-wrapper {
            padding-top: 125px;
            min-height: 100vh;
        }

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 768px) {
            .top-bar {
                flex-direction: column;
                padding: 15px;
                gap: 12px;
            }

            .user-section {
                position: static;
                transform: none;
                width: 100%;
                justify-content: space-between;
            }

            .brand h1 {
                font-size: 18px;
            }

            .tabs-nav {
                flex-direction: column;
                padding: 10px;
                gap: 5px;
            }

            .tab-link {
                width: 100%;
                justify-content: center;
                padding: 14px 20px;
                border-bottom: none;
                border-left: 3px solid transparent;
                border-radius: 8px;
            }

            .tab-link.active {
                border-left-color: var(--primary-color);
                border-bottom: none;
            }

            .content-wrapper {
                padding-top: 200px;
            }
        }

        @media (max-width: 480px) {
            .brand h1 {
                font-size: 16px;
            }

            .user-info {
                display: none;
            }
        }
    </style>
</head>
<body>

<!-- HEADER WRAPPER -->
<div class="header-wrapper">
    
    <!-- TOP BAR (CENTERED) -->
    <div class="top-bar">
        <div class="brand">
            <div class="brand-icon">📚</div>
            <h1>Multi-School Management</h1>
        </div>
        
        <!-- User Section (Right Side) -->
        <div class="user-section">
            <div class="user-info">
                <div class="name"><?php echo $admin_name; ?></div>
                <div class="email"><?php echo $admin_email; ?></div>
            </div>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>

    <!-- TABS NAVIGATION (CENTERED) -->
    <nav class="tabs-nav">
        <a href="https://sai7755.com/college/dashboard.php" 
           class="tab-link <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>">
            <span class="tab-icon">🏫</span>
            OLV School
        </a>
        
        <a href="https://sai7755.com/college/orc_sch/dashbored1.php" 
           class="tab-link <?php echo ($current_page === 'dashboard_ocr_sch.php') ? 'active' : ''; ?>">
            <span class="tab-icon">🌸</span>
            ORCHID School
        </a>
        
        <a href="https://sai7755.com/college/mum_unu/dashboard1.php" 
           class="tab-link <?php echo ($current_page === 'dashboard_mum_uni.php') ? 'active' : ''; ?>">
            <span class="tab-icon">🎓</span>
            Mumbai University
        </a>
    </nav>
    
</div>

<!-- CONTENT WRAPPER -->
<div class="content-wrapper">
