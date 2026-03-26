<?php 
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = "OLV Academy - Home";
include(__DIR__ . '/header3.php'); 
?>

<style>
    :root {
        --gold: #00ffff;
        --dark-bg: #0a0a0a;
        --card-bg: #ffffff;
        --text-main: #1a1a1a;
        --text-gray: #666666;
        --white: #ffffff;
        --overlay: rgba(0, 0, 0, 0.75);
    }
    
    .banner-section {
        position: relative;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        transition: background-image 1s ease-in-out;
    }

    /* --- HERO SECTION --- */
    .hero {
        position: relative;
        height: 100vh;
        min-height: 750px;
        /* Initial background */
        background: url('uploads/images/banner1.webp') center/cover no-repeat;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        background-attachment: fixed;
    }

    .hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            to bottom,
            rgba(0, 0, 0, 0.5) 0%,
            rgba(0, 0, 0, 0.7) 50%,
            rgba(0, 0, 0, 0.85) 100%
        );
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 1000px;
        animation: fadeInUp 1.2s ease;
    }

    .hero-tag {
        font-size: 0.8rem;
        letter-spacing: 5px;
        margin-bottom: 25px;
        display: inline-block;
        border-bottom: 1px solid var(--gold);
        padding-bottom: 5px;
        text-transform: uppercase;
        color: var(--gold);
    }

    .hero h1 {
        font-size: clamp(2.5rem, 8vw, 5.5rem);
        font-weight: 900;
        line-height: 1.1;
        margin-bottom: 20px;
        letter-spacing: -2px;
    }

    .hero h1 span {
        color: var(--gold);
    }

    .hero-sub {
        font-size: clamp(1.2rem, 2.5vw, 1.8rem);
        margin-bottom: 35px;
        font-weight: 300;
        letter-spacing: 1px;
    }

    .hero-desc {
        max-width: 750px;
        margin: 0 auto 45px;
        font-size: 1rem;
        opacity: 0.8;
        font-weight: 300;
    }

    .btn-group {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        padding: 18px 40px;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        text-decoration: none;
        border-radius: 4px;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: inline-flex;
        align-items: center;
        gap: 12px;
        font-family: 'Orbitron', sans-serif;
    }

    .btn-gold {
        background: var(--gold);
        color: white;
        border: 1px solid var(--gold);
    }

    .btn-gold:hover {
        background: transparent;
        color: var(--gold);
        transform: translateY(-5px);
    }

    .btn-outline {
        background: white;
        color: black;
        border: 1px solid white;
    }

    .btn-outline:hover {
        background: transparent;
        color: white;
        transform: translateY(-5px);
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .divisions {
        padding: 120px 0;
        background-color: #fcfcfc;
        text-align: center;
    }

    .section-header h2 {
        font-size: 2.8rem;
        margin-bottom: 15px;
    }

    .section-header h2 span { 
        color: var(--gold); 
    }

    .section-header p {
        color: var(--text-gray);
        margin-bottom: 70px;
        font-size: 1.1rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .grid-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
    }

    .core-card {
        background: var(--white);
        padding: 60px 45px;
        border-radius: 4px;
        text-align: left;
        position: relative;
        box-shadow: 0 20px 40px rgba(0,0,0,0.03);
        transition: 0.4s;
        border-top: 3px solid transparent;
    }

    .core-card:hover {
        transform: translateY(-15px);
        border-top: 3px solid var(--gold);
        box-shadow: 0 30px 60px rgba(0,0,0,0.08);
    }

    .card-tag {
        position: absolute;
        top: 35px;
        right: 40px;
        font-size: 0.65rem;
        background: #f4f4f4;
        padding: 4px 12px;
        border-radius: 20px;
        text-transform: uppercase;
        font-weight: 700;
        color: #888;
    }

    .card-icon {
        font-size: 2.2rem;
        margin-bottom: 30px;
        color: var(--gold);
    }

    .core-card h3 {
        font-size: 1.3rem;
        margin-bottom: 18px;
        color: #000;
    }

    .core-card p {
        color: var(--text-gray);
        font-size: 0.95rem;
        margin-bottom: 30px;
        min-height: 80px;
    }

    .learn-more {
        color: var(--gold);
        text-decoration: none;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .services-section {
        padding: 100px 0;
        background-color: #fdfdfd;
    }

    .service-card {
        background: #ffffff;
        padding: 50px 40px;
        border-radius: 10px;
        border: 1px solid #eee;
        transition: all 0.4s ease;
        text-align: left;
    }

    .service-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        border-color: #d4a34d;
    }

    .service-icon {
        font-size: 2.5rem;
        margin-bottom: 25px;
        color: #d4a34d;
    }

    .service-card h3 {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 18px;
        color: #000;
        letter-spacing: 1px;
        line-height: 1.4;
    }

    .service-card p {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .stats {
        background: var(--dark-bg);
        padding: 100px 0;
        color: white;
        text-align: center;
        border-top: 1px solid #222;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .stat-item {
        position: relative;
    }

    .stat-item:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0;
        top: 20%;
        height: 60%;
        width: 1px;
        background: #333;
    }

    .stat-num {
        font-family: 'Orbitron', sans-serif;
        font-size: 4.5rem;
        color: var(--gold);
        display: block;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .stat-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: #777;
    }

    .gallery {
        padding: 120px 0;
        background: #fff;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-top: 60px;
    }

    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        height: 280px;
        cursor: pointer;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .gallery-item:first-child {
        grid-column: span 2;
        grid-row: span 2;
        height: auto;
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .gallery-item:hover img {
        transform: scale(1.15);
    }

    .gallery-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        padding: 25px;
        color: white;
        transform: translateY(100%);
        transition: transform 0.4s ease;
    }

    .gallery-item:hover .gallery-overlay {
        transform: translateY(0);
    }

    .gallery-overlay h4 {
        font-size: 1.1rem;
        margin-bottom: 5px;
    }

    .gallery-overlay p {
        font-size: 0.85rem;
        opacity: 0.9;
    }

    .faculty {
        padding: 120px 0;
        background: #fcfcfc;
    }

    .faculty-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 40px;
        margin-top: 60px;
    }

    .faculty-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0,0,0,0.05);
        transition: 0.4s;
        position: relative;
    }

    .faculty-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 60px rgba(0,0,0,0.12);
    }

    .faculty-image {
        width: 100%;
        height: 320px;
        overflow: hidden;
        position: relative;
    }

    .faculty-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.6s;
    }

    .faculty-card:hover .faculty-image img {
        transform: scale(1.1);
    }

    .faculty-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: var(--gold);
        color: white;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 0.7rem;
        text-transform: uppercase;
        font-weight: 700;
    }

    .faculty-info {
        padding: 30px 25px;
        text-align: center;
    }

    .faculty-info h3 {
        font-size: 1.3rem;
        margin-bottom: 8px;
        color: #000;
    }

    .faculty-info .role {
        color: var(--gold);
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .faculty-info p {
        color: var(--text-gray);
        font-size: 0.9rem;
        line-height: 1.7;
        margin-bottom: 20px;
    }

    .faculty-social {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .faculty-social a {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: #f4f4f4;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #666;
        font-size: 0.9rem;
        transition: 0.3s;
    }

    .faculty-social a:hover {
        background: var(--gold);
        color: white;
        transform: translateY(-3px);
    }

    .testimonials {
        padding: 120px 0;
        background: var(--dark-bg);
        color: white;
    }

    .testimonials .section-header h2 { 
        color: white; 
    }
    .testimonials .section-header p { 
        color: #999; 
    }

    .testimonial-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 35px;
        margin-top: 60px;
    }

    .testimonial-card {
        background: rgba(255,255,255,0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.1);
        padding: 45px 40px;
        border-radius: 8px;
        position: relative;
        transition: 0.4s;
    }

    .testimonial-card:hover {
        background: rgba(255,255,255,0.08);
        border-color: var(--gold);
        transform: translateY(-10px);
    }

    .quote-icon {
        font-size: 3.5rem;
        color: var(--gold);
        opacity: 0.3;
        position: absolute;
        top: 20px;
        right: 30px;
    }

    .testimonial-text {
        font-size: 1.05rem;
        line-height: 1.8;
        margin-bottom: 30px;
        color: #ddd;
        font-style: italic;
    }

    .testimonial-author {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .author-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--gold), #c4933d);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
    }

    .author-info h4 {
        font-size: 1.1rem;
        margin-bottom: 5px;
        color: white;
    }

    .author-info p {
        font-size: 0.85rem;
        color: #888;
    }

    .rating {
        color: var(--gold);
        font-size: 0.9rem;
        margin-top: 8px;
    }

    .split {
        padding: 120px 0;
        background: #fff;
    }

    .split-flex {
        display: flex;
        align-items: center;
        gap: 80px;
        flex-wrap: wrap;
    }

    .split-text { 
        flex: 1; 
        min-width: 400px; 
    }
    .split-image { 
        flex: 1; 
        min-width: 400px; 
        position: relative; 
    }

    .split-text h2 {
        font-size: 3.2rem;
        margin-bottom: 35px;
        line-height: 1.1;
    }

    .split-text h2 span { 
        color: var(--gold); 
    }

    .split-text p {
        color: var(--text-gray);
        font-size: 1.1rem;
        margin-bottom: 40px;
    }

    .feature-list { 
        list-style: none; 
        margin-bottom: 45px; 
    }
    .feature-list li {
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 15px;
        font-weight: 500;
        font-size: 1rem;
    }

    .feature-list li::before {
        content: '';
        width: 8px;
        height: 8px;
        background: var(--gold);
        border-radius: 50%;
    }

    .img-container {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 40px 80px rgba(0,0,0,0.15);
    }

    .img-container img { 
        width: 100%; 
        display: block; 
        transition: 0.6s; 
    }
    .img-container:hover img { 
        transform: scale(1.08); 
    }

    .cta {
        padding: 120px 0;
        text-align: center;
        border-top: 1px solid #eee;
    }

    .cta-pill {
        display: inline-block;
        padding: 8px 24px;
        background: #f8f8f8;
        border: 1px solid #ddd;
        border-radius: 50px;
        font-size: 0.7rem;
        text-transform: uppercase;
        font-weight: 700;
        margin-bottom: 30px;
        letter-spacing: 1px;
    }

    .cta h2 { 
        font-size: 3.8rem; 
        margin-bottom: 25px; 
    }
    .cta h2 span { 
        color: var(--gold); 
    }
    .cta p { 
        max-width: 650px; 
        margin: 0 auto 50px; 
        color: var(--text-gray); 
        font-size: 1.2rem; 
    }

    @keyframes fadeInUp {
        from { 
            opacity: 0; 
            transform: translateY(30px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }

    @media (max-width: 1024px) {
        .stats-grid { 
            grid-template-columns: repeat(2, 1fr); 
            gap: 60px; 
        }
        .stat-item::after { 
            display: none; 
        }
        .split-text, .split-image { 
            min-width: 100%; 
        }
        .split-text { 
            text-align: center; 
        }
        .feature-list li { 
            justify-content: center; 
        }
        .gallery-grid { 
            grid-template-columns: repeat(2, 1fr); 
        }
        .gallery-item:first-child { 
            grid-column: span 1; 
            grid-row: span 1; 
        }
    }

    @media (max-width: 768px) {
        .hero h1 { 
            font-size: 3.2rem; 
        }
        .cta h2 { 
            font-size: 2.5rem; 
        }
        .stat-num { 
            font-size: 3.2rem; 
        }
        .hero { 
            background-attachment: scroll; 
        }
        .gallery-grid { 
            grid-template-columns: 1fr; 
        }
        .gallery-item { 
            height: 250px; 
        }
        .testimonial-grid { 
            grid-template-columns: 1fr; 
        }
        .faculty-grid { 
            grid-template-columns: 1fr; 
        }
        .grid-cards { 
            grid-template-columns: 1fr; 
        }
    }
</style>

<!-- HERO BANNER SECTION -->
<section class="hero banner-section" id="heroBanner">
    <div class="hero-content container">
        <span class="hero-tag">Welcome to the Future of Learning</span>
        <h1>OLV <span>ACADEMY</span></h1>
        <p class="hero-sub">Asia's Largest Integrated Educational Ecosystem</p>
        <p class="hero-desc">
            Architecting the future of global leaders through integrated academic excellence,
            research, and holistic development across our 50-acre smart campus.
        </p>
        <div class="btn-group">
            <a href="#" class="btn btn-gold">Explore Our Story →</a>
            <a href="#" class="btn btn-outline">Get In Touch →</a>
        </div>
    </div>
</section>

<!-- CORE DIVISIONS SECTION -->
<section class="divisions">
    <div class="container">
        <div class="section-header">
            <h2>Our Core <span>Divisions</span></h2>
            <p>Three integrated pillars driving innovation in global education standards</p>
        </div>
        
        <div class="grid-cards">
            <div class="core-card">
                <span class="card-tag">Infrastructure</span>
                <div class="card-icon">🏛️</div>
                <h3>ACADEMICS</h3>
                <p>State-of-the-art learning infrastructure spanning Smart Classrooms and futuristic Digital Libraries.</p>
                <a href="#" class="learn-more">Learn More →</a>
            </div>

            <div class="core-card">
                <span class="card-tag">Platform</span>
                <div class="card-icon">📡</div>
                <h3>DIGITAL LEARNING</h3>
                <p>Multi-platform reach through our proprietary LMS, AI-driven portals, and global expert networks.</p>
                <a href="#" class="learn-more">Learn More →</a>
            </div>

            <div class="core-card">
                <span class="card-tag">Technology</span>
                <div class="card-icon">🔬</div>
                <h3>INNOVATION LABS</h3>
                <p>Advanced skill development through AI, Robotics, and Research labs for next-gen mastery.</p>
                <a href="#" class="learn-more">Learn More →</a>
            </div>
        </div>
    </div>
</section>

<!-- STUDENT SERVICES SECTION -->
<section class="services-section">
    <div class="container">
        <div class="section-header">
            <h2>Student <span>Services</span> & Facilities</h2>
            <p>Comprehensive support ecosystem ensuring academic excellence and holistic development</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 30px;">
            <div class="service-card">
                <div class="service-icon">📘</div>
                <h3>ACADEMICS & CURRICULUM</h3>
                <p>Structured curriculum, regular assessments, and skill-focused learning designed for strong foundations and future readiness.</p>
            </div>

            <div class="service-card">
                <div class="service-icon">🧪</div>
                <h3>SCIENCE & INNOVATION LABS</h3>
                <p>Hands-on learning through Science, Robotics, and Innovation labs to build practical understanding and problem-solving.</p>
            </div>

            <div class="service-card">
                <div class="service-icon">💻</div>
                <h3>DIGITAL LEARNING & SMART CLASSES</h3>
                <p>Smart classroom tools, digital content, and modern teaching aids to make learning interactive and outcome-driven.</p>
            </div>

            <div class="service-card">
                <div class="service-icon">📚</div>
                <h3>LIBRARY & KNOWLEDGE CENTER</h3>
                <p>Curated books, reference resources, and reading programs that strengthen comprehension and lifelong learning habits.</p>
            </div>

            <div class="service-card">
                <div class="service-icon">🚌</div>
                <h3>TRANSPORT & STUDENT SAFETY</h3>
                <p>Route-based transport support with disciplined pickup/drop processes and student safety-first handling.</p>
            </div>

            <div class="service-card">
                <div class="service-icon">🤝</div>
                <h3>COUNSELING & PARENT SUPPORT</h3>
                <p>Student guidance, wellbeing support, and parent communication channels for consistent progress and care.</p>
            </div>
        </div>
    </div>
</section>

<!-- STATS SECTION -->
<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-num">10,000</span>
                <span class="stat-label">Acres of Campus</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">50+</span>
                <span class="stat-label">Smart Labs</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">500M+</span>
                <span class="stat-label">Global Alumnis</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">24/7</span>
                <span class="stat-label">Academic Support</span>
            </div>
        </div>
    </div>
</section>

<!-- GALLERY SECTION -->
<section class="gallery">
    <div class="container">
        <div class="section-header">
            <h2>Campus <span>Gallery</span></h2>
            <p>Experience our world-class infrastructure and vibrant campus life</p>
        </div>
        
        <div class="gallery-grid">
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1562774053-701939374585?w=800&q=80" alt="Main Campus">
                <div class="gallery-overlay">
                    <h4>Main Campus Building</h4>
                    <p>Architectural excellence meets functionality</p>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?w=600&q=80" alt="Library">
                <div class="gallery-overlay">
                    <h4>Digital Library</h4>
                    <p>Knowledge hub with 100K+ resources</p>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=600&q=80" alt="Labs">
                <div class="gallery-overlay">
                    <h4>Innovation Labs</h4>
                    <p>Cutting-edge research facilities</p>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?w=600&q=80" alt="Sports">
                <div class="gallery-overlay">
                    <h4>Sports Complex</h4>
                    <p>Olympic-standard facilities</p>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600&q=80" alt="Auditorium">
                <div class="gallery-overlay">
                    <h4>Grand Auditorium</h4>
                    <p>5000+ seating capacity</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FACULTY SECTION -->
<section class="faculty">
    <div class="container">
        <div class="section-header">
            <h2>Meet Our <span>Faculty</span></h2>
            <p>World-class educators and industry experts guiding your journey</p>
        </div>
        
        <div class="faculty-grid">
            <div class="faculty-card">
                <div class="faculty-image">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&q=80" alt="Faculty">
                    <span class="faculty-badge">Dean</span>
                </div>
                <div class="faculty-info">
                    <h3>Dr. Rajesh Kumar</h3>
                    <p class="role">Dean of Academics</p>
                    <p>Ph.D. from MIT with 25+ years of educational leadership and innovation experience.</p>
                    <div class="faculty-social">
                        <a href="#">🔗</a>
                        <a href="#">📧</a>
                        <a href="#">🎓</a>
                    </div>
                </div>
            </div>

            <div class="faculty-card">
                <div class="faculty-image">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&q=80" alt="Faculty">
                    <span class="faculty-badge">Professor</span>
                </div>
                <div class="faculty-info">
                    <h3>Prof. Anita Desai</h3>
                    <p class="role">Head of Research</p>
                    <p>Leading AI and Machine Learning expert with 50+ published research papers globally.</p>
                    <div class="faculty-social">
                        <a href="#">🔗</a>
                        <a href="#">📧</a>
                        <a href="#">🎓</a>
                    </div>
                </div>
            </div>

            <div class="faculty-card">
                <div class="faculty-image">
                    <img src="https://images.unsplash.com/photo-1556157382-97eda2d62296?w=400&q=80" alt="Faculty">
                    <span class="faculty-badge">Director</span>
                </div>
                <div class="faculty-info">
                    <h3>Mr. Vikram Singh</h3>
                    <p class="role">Director of Innovation</p>
                    <p>Former Google engineer, championing tech integration in modern education systems.</p>
                    <div class="faculty-social">
                        <a href="#">🔗</a>
                        <a href="#">📧</a>
                        <a href="#">🎓</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TESTIMONIALS SECTION -->
<section class="testimonials">
    <div class="container">
        <div class="section-header">
            <h2>Student <span>Success Stories</span></h2>
            <p>Hear from our alumni who are making a difference globally</p>
        </div>
        
        <div class="testimonial-grid">
            <div class="testimonial-card">
                <div class="quote-icon">"</div>
                <p class="testimonial-text">
                    OLV Academy transformed my life. The faculty mentorship and state-of-the-art facilities gave me the edge I needed to secure a position at Microsoft straight out of college.
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">SK</div>
                    <div class="author-info">
                        <h4>Sanjay Kulkarni</h4>
                        <p>Software Engineer, Microsoft</p>
                        <div class="rating">★★★★★</div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="quote-icon">"</div>
                <p class="testimonial-text">
                    The research opportunities and innovation labs at OLV Academy are unparalleled. I published my first paper in my second year, which opened doors to global conferences.
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">PM</div>
                    <div class="author-info">
                        <h4>Priya Mehta</h4>
                        <p>Research Scholar, Stanford</p>
                        <div class="rating">★★★★★</div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="quote-icon">"</div>
                <p class="testimonial-text">
                    Beyond academics, OLV Academy shaped my personality. The holistic approach to education prepared me not just for a career, but for life itself.
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">AR</div>
                    <div class="author-info">
                        <h4>Amit Rao</h4>
                        <p>Entrepreneur, Tech Startup Founder</p>
                        <div class="rating">★★★★★</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SPLIT CONTENT SECTION -->
<section class="split">
    <div class="container">
        <div class="split-flex">
            <div class="split-text">
                <h2>COMPLETE <span>CREATIVE</span> DEVELOPMENT</h2>
                <p>Unlike traditional institutions, we seamlessly integrate content learning, physical growth, and emotional intelligence for a 360-degree transformation.</p>
                <ul class="feature-list">
                    <li>End-to-end curriculum control</li>
                    <li>Multiple talent development channels</li>
                    <li>Direct industry mentorship access</li>
                    <li>Premium international standards</li>
                </ul>
                <a href="#" class="btn btn-gold" style="background: #000;">Learn More →</a>
            </div>
            <div class="split-image">
                <div class="img-container">
                    <img src="https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?q=80&w=2070" alt="Innovation Lab">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA SECTION -->
<section class="cta">
    <div class="container">
        <span class="cta-pill">✨ Join Our Ecosystem</span>
        <h2>READY TO PARTNER <span>WITH US?</span></h2>
        <p>Discover how we're transforming education through integrated infrastructure and world-class innovation.</p>
        <div class="btn-group">
            <a href="#" class="btn btn-gold" style="background: #000; color: #fff;">Register Now →</a>
            <a href="#" class="btn btn-outline" style="border: 1px solid #000; color: #000;">Explore Facilities →</a>
        </div>
    </div>
</section>

<!-- HERO BANNER SLIDESHOW SCRIPT - FIXED PATHS -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const heroBanner = document.getElementById("heroBanner");
    const images = [
        "uploads/images/banner1.webp",
        "uploads/images/banner2.webp", 
        "uploads/images/banner3.webp",
        "uploads/images/banner4.webp"
    ];
    let currentIndex = 0;
    let slideInterval;

    function changeBackground() {
        heroBanner.style.backgroundImage = `url('${images[currentIndex]}')`;
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % images.length;
        changeBackground();
    }

    function startSlider() {
        slideInterval = setInterval(nextSlide, 4000);
    }

    function stopSlider() {
        clearInterval(slideInterval);
    }

    // Start slider on load
    changeBackground();
    startSlider();

    // Pause slider on hover
    heroBanner.addEventListener('mouseenter', stopSlider);
    heroBanner.addEventListener('mouseleave', startSlider);

    // Touch support for mobile
    let touchStartX = 0;
    heroBanner.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    });

    heroBanner.addEventListener('touchend', (e) => {
        let touchEndX = e.changedTouches[0].screenX;
        let diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > 50) {
            if (diff > 0) {
                nextSlide();
            } else {
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                changeBackground();
            }
        }
    });
});
</script>

<?php include __DIR__ . '/footer3.php'; ?>
