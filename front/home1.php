<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="OLV Education - Choose from OLV School, Orchid School, and Degree College for quality education">
    <meta name="keywords" content="OLV Education, OLV School, Orchid School, Degree College, Education Portal">
    <meta name="author" content="OLV Education">
    <title>OLV Education - Choose Your Institution</title>
    <!-- Preload critical image for faster painting -->
    <link rel="preload" as="image" href="https://images.unsplash.com/photo-1562774053-701939374585?w=1920&q=80">
    <link rel="canonical" href="https://olveducation.com">
    
    <style>
        /* ============================================
           RESET & CORE STYLES
           ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: #0f172a; /* Fallback color */
        }

        /* ============================================
           HERO SECTION
           ============================================ */
        .hero-section {
            position: relative;
            width: 100%;
            /* Use min-height instead of height to allow content to grow on mobile landscape */
            min-height: 100vh;
            /* Modern mobile browser support */
            min-height: 100dvh; 
            
            /* University/College Building Background */
            background-image: url('https://images.unsplash.com/photo-1562774053-701939374585?w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed; /* Parallax effect for desktop */
            background-repeat: no-repeat;
            
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* Transparent Dark Overlay */
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                to bottom,
                rgba(0, 0, 0, 0.45) 0%,
                rgba(0, 0, 0, 0.65) 50%,
                rgba(0, 0, 0, 0.85) 100%
            );
            z-index: 1;
        }

        /* Logo Section */
        .logo-section {
            position: absolute;
            top: 30px;
            left: 40px;
            z-index: 3;
            animation: fadeIn 1.5s ease;
        }

        .logo-section img {
            height: 60px;
            width: auto;
            filter: drop-shadow(2px 2px 8px rgba(0, 0, 0, 0.5));
        }

        .logo-text {
            color: white;
            font-size: 1.6rem;
            font-weight: 600;
            margin-top: 5px;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
            letter-spacing: 0.5px;
        }

        /* Content Container */
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            padding: 20px;
            max-width: 1200px;
            width: 100%;
        }

        /* Main Heading */
        .hero-content h1 {
            font-size: 4.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 3px 3px 20px rgba(0, 0, 0, 0.6);
            animation: fadeInDown 1s ease;
            letter-spacing: 1px;
            line-height: 1.1;
        }

        .hero-content .subtitle {
            font-size: 1.4rem;
            margin-bottom: 12px;
            opacity: 0.92;
            font-weight: 400;
            animation: fadeInUp 1s ease 0.15s both;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.6);
            color: #f0f0f0;
        }

        .hero-content .tagline {
            font-size: 1.6rem;
            margin-bottom: 80px;
            opacity: 0.95;
            font-weight: 300;
            animation: fadeInUp 1s ease 0.25s both;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.6);
        }

        /* ============================================
           INSTITUTION CARDS
           ============================================ */
        .institution-cards {
            display: flex;
            justify-content: center;
            align-items: stretch; /* Ensures all cards are same height */
            gap: 35px;
            flex-wrap: wrap;
            animation: fadeInUp 1.2s ease 0.4s both;
        }

        /* Individual Card */
        .institution-card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 28px;
            padding: 50px 40px;
            width: 350px;
            min-height: 320px;
            cursor: pointer;
            transition: all 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 15px 60px rgba(0, 0, 0, 0.35);
            text-decoration: none;
            color: white;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* Shimmer Effect */
        .institution-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
            transition: left 0.7s;
        }

        .institution-card:hover::before {
            left: 100%;
        }

        /* Hover State */
        .institution-card:hover {
            transform: translateY(-22px) scale(1.05);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5);
            background: rgba(255, 255, 255, 0.22);
            border-color: rgba(255, 255, 255, 0.45);
        }

        /* Card Icon */
        .card-icon {
            font-size: 5.5rem;
            margin-bottom: 28px;
            display: block;
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.4));
            transition: transform 0.4s ease;
        }

        .institution-card:hover .card-icon {
            transform: scale(1.15) rotate(5deg);
        }

        /* Card Title */
        .institution-card h2 {
            font-size: 2.1rem;
            margin-bottom: 18px;
            color: white;
            font-weight: 600;
            text-shadow: 2px 2px 12px rgba(0, 0, 0, 0.5);
            transition: color 0.3s ease;
        }

        /* Card Description */
        .institution-card p {
            font-size: 1.08rem;
            color: rgba(255, 255, 255, 0.92);
            margin-bottom: 0;
            line-height: 1.7;
            text-shadow: 1px 1px 6px rgba(0, 0, 0, 0.5);
            font-weight: 300;
        }

        /* Individual Glow Effects */
        .card-olv:hover {
            box-shadow: 0 30px 80px rgba(49, 130, 206, 0.65), 0 0 50px rgba(49, 130, 206, 0.45);
        }

        .card-orchid:hover {
            box-shadow: 0 30px 80px rgba(56, 161, 105, 0.65), 0 0 50px rgba(56, 161, 105, 0.45);
        }

        .card-degree:hover {
            box-shadow: 0 30px 80px rgba(214, 158, 46, 0.65), 0 0 50px rgba(214, 158, 46, 0.45);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-60px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(60px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ============================================
           RESPONSIVE DESIGN - MOBILE OPTIMIZATION
           ============================================ */

        /* Tablet & Medium Devices (Landscape Tablets) */
        @media (max-width: 992px) {
            .hero-content h1 {
                font-size: 3.5rem;
            }

            .hero-content .subtitle {
                font-size: 1.3rem;
            }

            .hero-content .tagline {
                font-size: 1.4rem;
                margin-bottom: 60px;
            }

            .institution-card {
                width: 320px;
                padding: 45px 35px;
            }
        }

        /* Tablets Portrait & Large Phones */
        @media (max-width: 768px) {
            .hero-section {
                background-attachment: scroll; /* Fix for iOS scroll jitter */
                padding: 40px 15px;
                height: auto;
            }

            .logo-section {
                position: relative;
                top: auto;
                left: auto;
                margin-bottom: 20px;
                text-align: center;
            }

            .logo-section img {
                height: 50px;
            }

            .logo-text {
                font-size: 1.5rem;
            }

            .hero-content h1 {
                font-size: 3rem;
                margin-bottom: 12px;
            }

            .hero-content .subtitle {
                font-size: 1.2rem;
                margin-bottom: 10px;
            }

            .hero-content .tagline {
                font-size: 1.3rem;
                margin-bottom: 50px;
            }

            .institution-cards {
                gap: 28px;
            }

            .institution-card {
                width: 100%;
                max-width: 400px; /* Limit width on tablets */
                padding: 40px 30px;
                min-height: 280px;
            }

            .card-icon {
                font-size: 4.5rem;
                margin-bottom: 22px;
            }

            .institution-card h2 {
                font-size: 1.9rem;
                margin-bottom: 15px;
            }
        }

        /* Mobile Landscape & Large Phones */
        @media (max-width: 640px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-content .tagline {
                font-size: 1.2rem;
                margin-bottom: 45px;
            }

            .institution-card {
                width: 100%;
                max-width: 100%; /* Full width cards */
            }
        }

        /* Mobile Portrait - Small Screens */
        @media (max-width: 480px) {
            .hero-content {
                padding: 10px;
            }

            .hero-content h1 {
                font-size: 2.2rem;
                margin-bottom: 10px;
            }

            .hero-content .subtitle {
                font-size: 1rem;
            }

            .hero-content .tagline {
                font-size: 1.1rem;
                margin-bottom: 40px;
            }

            .institution-cards {
                gap: 22px;
            }

            .institution-card {
                padding: 30px 20px;
                min-height: auto; /* Allow natural height */
                border-radius: 24px;
            }

            .card-icon {
                font-size: 4rem;
                margin-bottom: 15px;
            }

            .institution-card h2 {
                font-size: 1.75rem;
                margin-bottom: 10px;
            }

            .institution-card p {
                font-size: 1rem;
                line-height: 1.5;
            }
        }

        /* Very Small Mobile */
        @media (max-width: 360px) {
            .hero-content h1 {
                font-size: 1.8rem;
            }

            .institution-card h2 {
                font-size: 1.5rem;
            }
        }

        /* Landscape Mode Optimization (Short heights) */
        @media (max-height: 600px) and (orientation: landscape) {
            .hero-section {
                padding: 20px;
                min-height: 100vh;
                height: auto; /* Let it grow */
            }

            .logo-section {
                display: none; /* Hide logo to save vertical space on landscape */
            }

            .hero-content .tagline {
                margin-bottom: 30px;
            }

            .institution-cards {
                gap: 15px;
            }

            .institution-card {
                width: 48%; /* 2 cards per row if possible */
                min-height: auto;
                padding: 20px;
            }
            
            .card-icon {
                font-size: 3rem;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Logo Section -->
    <div class="logo-section">
        <!-- Uncomment to use image logo -->
        <!-- <img src="images/olv-logo.png" alt="OLV Education Logo"> -->
        <div class="logo-text">OLV Education</div>
    </div>

    <!-- Main Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <h1>Welcome to OLV Education</h1>
            <p class="subtitle">olveducation.com</p>
            <p class="tagline">Choose your institution to begin your journey</p>
            
            <div class="institution-cards">
                <!-- OLV School Card -->
                <a href="olv-school.php" class="institution-card card-olv">
                    <span class="card-icon">🏫</span>
                    <h2>OLV School</h2>
                    <p>Excellence in primary and secondary education with modern facilities</p>
                </a>

                <!-- Orchid School Card -->
                <a href="../front2/home.php" class="institution-card card-orchid">
                    <span class="card-icon">🌸</span>
                    <h2>Orchid School</h2>
                    <p>Nurturing young minds with innovative learning approaches</p>
                </a>

                <!-- Degree College Card -->
                <a href="../front3/home3.php" class="institution-card card-degree">
                    <span class="card-icon">🎓</span>
                    <h2>Degree College</h2>
                    <p>Higher education programs for your professional career growth</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
