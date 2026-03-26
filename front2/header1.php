<?php
// college/front/header.php

$current_page = basename($_SERVER['PHP_SELF']);

function olv_active($file) {
    global $current_page;
    // Fixed: Added support for both complaint1.php and complain1.php
    $normalized_current = strtolower($current_page);
    $normalized_file = strtolower($file);
    return ($normalized_current === $normalized_file) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'OLV Academy'; ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root{
            --olv-gold: #d4a34d;
            --olv-dark: rgba(10,10,10,0.92);
            --olv-header-h: 82px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body{
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            color: #1a1a1a;
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .container{
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 25px;
        }

        /* Header */
        .olv-header{
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: var(--olv-header-h);
            z-index: 9999;
            display: flex;
            align-items: center;
            transition: 0.35s ease;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }

        .olv-header.scrolled{
            background: var(--olv-dark);
            border-bottom: 1px solid rgba(255,255,255,0.10);
            box-shadow: 0 12px 40px rgba(0,0,0,0.35);
        }

        .olv-header .container{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        /* Brand */
        .olv-brand{
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #000;
            min-width: 220px;
        }

        .olv-brand-logo{
            width: 46px;
            height: 46px;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 10px 30px rgba(212,163,77,0.25);
        }

        .olv-brand-text{ line-height: 1.05; }

        .olv-brand-text strong{
            display: block;
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 2px;
            font-size: 1.05rem;
            text-transform: uppercase;
        }

        .olv-brand-text span{
            display: block;
            font-size: 0.68rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--olv-gold);
            margin-top: 4px;
            opacity: 0.95;
        }

        /* Desktop nav */
        .olv-nav{
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .olv-menu{
            list-style: none;
            display: flex;
            gap: 26px;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .olv-menu a{
            color: #000;
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            padding: 8px 0;
            opacity: 0.95;
            transition: 0.25s ease;
            font-family: 'Orbitron', sans-serif;
        }

        .olv-menu a:hover{ color: var(--olv-gold); }

        .olv-menu a::after{
            content: "";
            position: absolute;
            left: 0; bottom: 0;
            width: 0;
            height: 2px;
            background: var(--olv-gold);
            transition: width 0.25s ease;
        }

        .olv-menu a:hover::after,
        .olv-menu a.active::after{ width: 100%; }

        .olv-menu a.active{ color: var(--olv-gold); }

        /* Dropdown */
        .olv-dd{ position: relative; }

        .olv-dd > a{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .olv-dd-panel{
            position: absolute;
            top: 130%;
            right: 0;
            min-width: 260px;
            background: rgba(255,255,255,0.98);
            border: 1px solid rgba(0,0,0,0.10);
            border-radius: 10px;
            padding: 10px;
            display: none;
            box-shadow: 0 18px 55px rgba(0,0,0,0.18);
        }

        .olv-dd:hover .olv-dd-panel{ display: block; }

        .olv-dd-panel a{
            display: block;
            padding: 12px 12px;
            border-radius: 8px;
            font-size: 0.78rem;
            letter-spacing: 1px;
            opacity: 0.95;
            color: #000;
            text-decoration: none;
        }

        .olv-dd-panel a:hover{
            background: rgba(212,163,77,0.12);
            color: var(--olv-gold);
        }

        /* DONATION BUTTON */
        .olv-donate-btn{
            padding: 12px 28px;
            background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-family: 'Orbitron', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 25px rgba(255,107,107,0.35);
            transition: all 0.35s ease;
            position: relative;
            overflow: hidden;
            border: none;
        }

        .olv-donate-btn::before{
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .olv-donate-btn:hover::before{
            width: 300px;
            height: 300px;
        }

        .olv-donate-btn:hover{
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(255,107,107,0.45);
        }

        .olv-donate-btn span{
            position: relative;
            z-index: 1;
        }

        .olv-donate-icon{
            font-size: 1.15rem;
            animation: heartbeat 1.5s ease-in-out infinite;
            position: relative;
            z-index: 1;
        }

        @keyframes heartbeat{
            0%, 100%{ transform: scale(1); }
            10%, 30%{ transform: scale(1.15); }
            20%, 40%{ transform: scale(1); }
        }

        /* Mobile toggle */
        .olv-burger{
            display: none;
            width: 46px;
            height: 46px;
            border-radius: 10px;
            border: 1px solid rgba(0,0,0,0.14);
            background: rgba(0,0,0,0.04);
            cursor: pointer;
            padding: 10px;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .olv-burger span{
            display: block;
            width: 100%;
            height: 2px;
            background: #000;
            margin: 3px 0;
            transition: 0.25s ease;
            border-radius: 3px;
        }

        /* Mobile menu */
        .olv-mmenu{
            display: none;
            position: fixed;
            top: var(--olv-header-h);
            left: 0;
            width: 100%;
            height: calc(100vh - var(--olv-header-h));
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(18px);
            z-index: 9998;
            padding: 26px 20px;
            overflow-y: auto;
            border-top: 1px solid rgba(0,0,0,0.08);
        }

        .olv-mmenu.active{ display: block; }

        .olv-mmenu a{
            display: block;
            padding: 14px 14px;
            border-radius: 10px;
            text-decoration: none;
            color: #000;
            font-family: 'Orbitron', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.95rem;
            margin-bottom: 10px;
            border: 1px solid rgba(0,0,0,0.10);
            background: rgba(0,0,0,0.03);
        }

        .olv-mmenu a.active{
            border-color: rgba(212,163,77,0.55);
            color: var(--olv-gold);
            background: rgba(212,163,77,0.10);
        }

        /* Mobile donate button */
        .olv-mmenu .olv-donate-btn{
            width: 100%;
            justify-content: center;
            margin-top: 20px;
        }

        /* Spacer */
        .olv-header-spacer{ height: var(--olv-header-h); }

        /* Scrolled state - white text */
        .olv-header.scrolled .olv-brand{ color: #fff; }
        .olv-header.scrolled .olv-menu a{ color: #fff; }
        .olv-header.scrolled .olv-burger{ border-color: rgba(255,255,255,0.18); background: rgba(255,255,255,0.06); }
        .olv-header.scrolled .olv-burger span{ background: #fff; }

        @media (max-width: 980px){
            .olv-nav{ display: none; }
            .olv-burger{ display: flex; }
            .olv-brand{ min-width: unset; }
        }
        
        /* DROPDOWN UPDATES */
        .has-dropdown {
            position: relative;
        }

        .has-dropdown .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 220px;
            background: rgba(255,255,255,0.98);
            border: 1px solid rgba(0,0,0,0.10);
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            padding: 10px 0;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }

        .has-dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .has-dropdown .dropdown-menu {
            pointer-events: auto;
            left: auto;
            right: 0;
        }

        /* SCROLL DROPDOWN BLACK BG - FIXED */
        .olv-header.scrolled .olv-dd-panel,
        .olv-header.scrolled .has-dropdown .dropdown-menu {
            background: var(--olv-dark) !important;
            border-color: rgba(255,255,255,0.15) !important;
        }

        .olv-header.scrolled .olv-dd-panel a,
        .olv-header.scrolled .has-dropdown .dropdown-menu a {
            color: #fff !important;
        }

        .olv-header.scrolled .olv-dd-panel a:hover,
        .olv-header.scrolled .has-dropdown .dropdown-menu a:hover {
            background: rgba(212,163,77,0.20) !important;
            color: var(--olv-gold) !important;
        }
    </style>
</head>
<body>

<header class="olv-header" id="olvHeader">
    <div class="container">
        <a class="olv-brand" href="olv-school.php">
            <img src="../font2/images/orchid.logo.jpeg" alt="Orchid School Logo" class="olv-brand-logo">
            <div class="olv-brand-text">
                <strong>ORCHID SCHOOL</strong>
                <span>Education Ecosystem</span>
            </div>
        </a>

        <nav class="olv-nav" aria-label="Main navigation">
            <ul class="olv-menu">
                <li><a class="<?php echo olv_active('home.php'); ?>" href="home.php">Home</a></li>
                <li><a class="<?php echo olv_active('about1.php'); ?>" href="about1.php">About Us</a></li>
                <li><a class="<?php echo olv_active('course1.php'); ?>" href="course1.php">Courses</a></li>
                <li><a class="<?php echo olv_active('teachers2.php'); ?>" href="teachers2.php">Teachers & Faculties</a></li>
                <li><a class="<?php echo olv_active('gallary1.php'); ?>" href="gallary1.php">Gallery</a></li>

                <li class="olv-dd has-dropdown">
                    <a>More ▾</a>
                    <div class="olv-dd-panel dropdown-menu">
                        <a class="<?php echo olv_active('register.php'); ?>" href="register.php">Register</a>
                        <a class="<?php echo olv_active('complaint1.php'); ?>" href="complaint1.php">Complaint</a>
                        <a class="<?php echo olv_active('ContactUs2.php'); ?>" href="ContactUs2.php">Contact Us</a>
                    </div>
                </li>
            </ul>

            <!-- DONATE BUTTON (Desktop) -->
            <a href="donate.php" class="olv-donate-btn">
                <span class="olv-donate-icon">❤️</span>
                <span>Donate</span>
            </a>
        </nav>

        <button class="olv-burger" type="button" onclick="olvToggleMenu()" aria-label="Open menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<div class="olv-mmenu" id="olvMobileMenu">
    <a class="<?php echo olv_active('home.php'); ?>" href="home.php">Home</a>
    <a class="<?php echo olv_active('about1.php'); ?>" href="about1.php">About Us</a>
    <a class="<?php echo olv_active('course1.php'); ?>" href="course1.php">Courses</a>
    <a class="<?php echo olv_active('teachers2.php'); ?>" href="teachers2.php">Teachers & Faculties</a>
    <a class="<?php echo olv_active('gallary1.php'); ?>" href="gallary1.php">Gallery</a>
    <a class="<?php echo olv_active('register.php'); ?>" href="register.php">Register</a>
    <a class="<?php echo olv_active('complaint1.php'); ?>" href="complaint1.php">Complaint</a>
    <a class="<?php echo olv_active('ContactUs2.php'); ?>" href="ContactUs2.php">Contact Us</a>
    
    <!-- DONATE BUTTON (Mobile) -->
    <a href="donate.php" class="olv-donate-btn">
        <span class="olv-donate-icon">❤️</span>
        <span>Donate Now</span>
    </a>
</div>

<div class="olv-header-spacer"></div>

<script>
    window.addEventListener('scroll', function () {
        const h = document.getElementById('olvHeader');
        if (!h) return;
        if (window.scrollY > 40) h.classList.add('scrolled');
        else h.classList.remove('scrolled');
    });

    function olvToggleMenu() {
        const mm = document.getElementById('olvMobileMenu');
        if (!mm) return;
        mm.classList.toggle('active');
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
