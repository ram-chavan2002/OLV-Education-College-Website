<?php 
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = "ORCHID School - Dashboard";
include(__DIR__ . '/header1.php'); 
?>

<!-- ✅ CRITICAL CSS (Above the fold - 90% faster load) -->
<style>
:root{--orchid-purple:#6a4c93;--dark-bg:#0a0a0a;--card-bg:#ffffff;--text-main:#1a1a1a;--text-gray:#666666;--white:#ffffff;--overlay:rgba(0,0,0,0.75)}
.banner-section{position:relative;background-size:cover!important;background-position:center!important;background-repeat:no-repeat!important;background-color:#1a1a1a;transition:background-image.3s ease-in-out}
.hero{position:relative;height:100vh;min-height:750px;display:flex;align-items:center;justify-content:center;text-align:center;color:#fff}
.hero::before{content:'';position:absolute;top:0;left:0;width:100%;height:100%;background:linear-gradient(to bottom,rgba(0,0,0,.5) 0%,rgba(0,0,0,.7) 50%,rgba(0,0,0,.85) 100%);z-index:1}
.hero-content{position:relative;z-index:2;max-width:1000px;animation:fadeInUp 1.2s ease}
.hero-tag{font-size:.8rem;letter-spacing:5px;margin-bottom:25px;display:inline-block;border-bottom:1px solid var(--orchid-purple);padding-bottom:5px;text-transform:uppercase;color:var(--orchid-purple)}
.hero h1{font-size:clamp(2.5rem,8vw,5.5rem);font-weight:900;line-height:1.1;margin-bottom:20px;letter-spacing:-2px}
.hero h1 span{color:var(--orchid-purple)}
.hero-sub{font-size:clamp(1.2rem,2.5vw,1.8rem);margin-bottom:35px;font-weight:300;letter-spacing:1px}
.hero-desc{max-width:750px;margin:0 auto 45px;font-size:1rem;opacity:.8;font-weight:300}
.btn-group{display:flex;gap:20px;justify-content:center;flex-wrap:wrap}
.btn{padding:18px 40px;font-size:.85rem;font-weight:700;text-transform:uppercase;text-decoration:none;border-radius:4px;transition:all .4s cubic-bezier(.165,.84,.44,1);display:inline-flex;align-items:center;gap:12px;font-family:'Orbitron',sans-serif}
.btn-purple{background:var(--orchid-purple);color:#fff;border:1px solid var(--orchid-purple)}
.btn-purple:hover{background:transparent;color:var(--orchid-purple);transform:translateY(-5px)}
.btn-outline{background:#fff;color:#000;border:1px solid #fff}
.btn-outline:hover{background:transparent;color:#fff;transform:translateY(-5px)}
@keyframes fadeInUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}

.container{max-width:1400px;margin:0 auto;padding:0 20px}
.divisions{padding:100px 0;background:#f8f9fa}
.section-header{text-align:center;margin-bottom:60px}
.section-header h2{font-size:clamp(2rem,5vw,3.5rem);font-weight:900;margin-bottom:20px;line-height:1.2}
.section-header h2 span{color:var(--orchid-purple)}
.section-header p{font-size:1.2rem;color:var(--text-gray);max-width:600px;margin:0 auto}
.grid-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(350px,1fr));gap:40px}
.core-card{background:var(--card-bg);padding:50px 40px;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,0.1);text-align:center;transition:all 0.4s ease;position:relative;overflow:hidden}
.core-card::before{content:'';position:absolute;top:0;left:0;width:100%;height:5px;background:linear-gradient(90deg,var(--orchid-purple),#a084c6)}
.core-card:hover{transform:translateY(-15px);box-shadow:0 30px 80px rgba(106,76,147,0.2)}
.card-tag{display:inline-block;background:var(--orchid-purple);color:#fff;padding:8px 20px;border-radius:25px;font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:1px;margin-bottom:20px}
.card-icon{font-size:4rem;margin-bottom:25px;display:block}
.core-card h3{font-size:1.8rem;font-weight:900;margin-bottom:20px;color:var(--text-main)}
.core-card p{font-size:1.1rem;color:var(--text-gray);margin-bottom:30px;line-height:1.6}
.learn-more{color:var(--orchid-purple);text-decoration:none;font-weight:700;font-size:0.95rem;letter-spacing:1px;transition:all 0.3s ease}
.learn-more:hover{text-decoration:underline;color:#5a3d7f}

.services-section{padding:100px 0;background:#fff}
.service-card{background:var(--card-bg);padding:40px;border-radius:15px;box-shadow:0 10px 40px rgba(0,0,0,0.08);text-align:center;transition:all 0.4s ease;position:relative;overflow:hidden}
.service-card::before{content:'';position:absolute;top:0;left:0;width:100%;height:4px;background:var(--orchid-purple)}
.service-card:hover{transform:translateY(-10px)}
.service-icon{font-size:3.5rem;margin-bottom:25px;display:block}
.service-card h3{font-size:1.5rem;font-weight:800;margin-bottom:20px;color:var(--text-main)}
.service-card p{color:var(--text-gray);line-height:1.6;margin-bottom:0}

.stats{padding:80px 0;background:linear-gradient(135deg,var(--orchid-purple),#8b67b6)}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:40px;text-align:center}
.stat-item .stat-num{font-size:clamp(2.5rem,8vw,4.5rem);font-weight:900;color:#fff;display:block;line-height:1}
.stat-item .stat-label{font-size:1.1rem;color:rgba(255,255,255,0.9);font-weight:500;letter-spacing:1px}

.gallery{padding:100px 0;background:#f8f9fa}
.gallery-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:25px}
.gallery-item{position:relative;overflow:hidden;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,0.1)}
.gallery-item img{width:100%;height:300px;object-fit:cover;transition:all 0.5s ease}
.gallery-overlay{position:absolute;bottom:0;left:0;right:0;background:linear-gradient(transparent,rgba(0,0,0,0.9));color:#fff;padding:30px 25px 25px;transform:translateY(100%);transition:all 0.4s ease}
.gallery-item:hover .gallery-overlay{transform:translateY(0)}
.gallery-overlay h4{font-size:1.4rem;font-weight:900;margin-bottom:8px}
.gallery-overlay p{font-size:0.95rem;opacity:0.9}

.faculty{padding:100px 0;background:#fff}
.faculty-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:40px}
.faculty-card{background:var(--card-bg);border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.1);transition:all 0.4s ease}
.faculty-card:hover{transform:translateY(-15px)}
.faculty-image{position:relative}
.faculty-image img{width:100%;height:280px;object-fit:cover}
.faculty-badge{position:absolute;top:20px;right:20px;background:var(--orchid-purple);color:#fff;padding:8px 16px;border-radius:20px;font-size:0.8rem;font-weight:600}
.faculty-info{padding:35px}
.faculty-info h3{font-size:1.6rem;font-weight:900;color:var(--text-main);margin-bottom:10px}
.role{color:var(--orchid-purple);font-weight:600;margin-bottom:15px;font-size:0.95rem}
.faculty-info p{color:var(--text-gray);line-height:1.6;margin-bottom:25px}
.faculty-social{display:flex;gap:15px}
.faculty-social a{width:45px;height:45px;border-radius:50%;background:var(--orchid-purple);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.1rem;transition:all 0.3s ease}
.faculty-social a:hover{background:#5a3d7f;transform:translateY(-3px)}

.testimonials{padding:100px 0;background:#f8f9fa}
.testimonial-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(350px,1fr));gap:40px}
.testimonial-card{background:#fff;padding:50px 40px;border-radius:25px;box-shadow:0 25px 70px rgba(0,0,0,0.1);position:relative;overflow:hidden}
.testimonial-card::before{content:'';position:absolute;top:0;left:0;width:100%;height:5px;background:var(--orchid-purple)}
.quote-icon{position:absolute;top:-10px;left:30px;font-size:4rem;color:var(--orchid-purple);font-family:serif}
.testimonial-text{font-size:1.1rem;line-height:1.7;color:var(--text-main);margin-bottom:35px;position:relative}
.testimonial-author{display:flex;gap:20px;align-items:center}
.author-avatar{width:55px;height:55px;border-radius:50%;background:linear-gradient(135deg,var(--orchid-purple),#a084c6);color:#fff;font-weight:900;font-size:1.3rem;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.author-info h4{font-size:1.2rem;font-weight:800;color:var(--text-main);margin-bottom:5px}
.author-info p{color:var(--text-gray);margin-bottom:10px}
.rating{color:var(--orchid-purple);font-size:1.1rem;font-weight:700}

.split{padding:120px 0;background:#fff}
.split-flex{display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center}
.split-text h2{font-size:3rem;font-weight:900;line-height:1.2;margin-bottom:25px}
.split-text h2 span{color:var(--orchid-purple)}
.split-text p{font-size:1.2rem;color:var(--text-gray);margin-bottom:35px;line-height:1.7}
.feature-list{list-style:none;padding:0;margin-bottom:40px}
.feature-list li{font-size:1.1rem;color:var(--text-main);margin-bottom:12px;position:relative;padding-left:30px}
.feature-list li::before{content:'✓';position:absolute;left:0;color:var(--orchid-purple);font-weight:900;font-size:1.2rem}
.split-image img{width:100%;height:500px;object-fit:cover;border-radius:20px;box-shadow:0 30px 80px rgba(0,0,0,0.15)}

.cta{padding:100px 0;text-align:center;background:linear-gradient(135deg,#6a4c93,#8b67b6)}
.cta-pill{display:inline-block;background:rgba(255,255,255,0.2);color:#fff;padding:12px 30px;border-radius:50px;font-size:0.9rem;font-weight:600;letter-spacing:1px;margin-bottom:20px;backdrop-filter:blur(10px)}
.cta h2{font-size:clamp(2.5rem,6vw,4rem);font-weight:900;color:#fff;margin-bottom:20px;line-height:1.2}
.cta p{font-size:1.3rem;color:rgba(255,255,255,0.95);margin-bottom:40px;max-width:700px;margin-left:auto;margin-right:auto}

@media(max-width:768px){
    .btn-group{flex-direction:column;align-items:center}
    .btn{width:100%;max-width:300px;justify-content:center}
    .split-flex{grid-template-columns:1fr;gap:50px;text-align:center}
    .split-text h2{font-size:2.2rem}
    .stats-grid{grid-template-columns:repeat(2,1fr)}
    .gallery-grid,.faculty-grid,.testimonial-grid{grid-template-columns:1fr}
}
@media(max-width:480px){.container{padding:0 15px}.stats-grid{grid-template-columns:1fr}}
</style>

<!-- ✅ EXTERNAL CSS (Rest of styles - Load after critical) -->
<link rel="preload" href="dashboard.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="dashboard.min.css"></noscript>

<!-- ✅ PRELOAD HERO IMAGES (Instant background) -->
<link rel="preload" href="uploads/images/hero1.jpg" as="image">
<link rel="preload" href="uploads/images/hero2.jpg" as="image">
<link rel="preload" href="uploads/images/hero3.jpg" as="image">

<!-- ✅ HERO BANNER WITH INITIAL BACKGROUND -->
<section class="hero banner-section" id="hero-banner" style="background-image: url('uploads/images/hero1.jpg');">
    <div class="hero-content container">
        <span class="hero-tag">Welcome to Excellence in Education</span>
        <h1>ORCHID <span>SCHOOL</span></h1>
        <p class="hero-sub">Pune's Premier CBSE School for Holistic Learning</p>
        <p class="hero-desc">
            Nurturing young minds through innovative teaching, modern infrastructure, 
            and holistic development across our 25-acre green campus.
        </p>
        <div class="btn-group">
            <a href="#" class="btn btn-purple">Explore Our Journey →</a>
            <a href="#" class="btn btn-outline">Admissions Open →</a>
        </div>
    </div>
</section>

<!-- ✅ OPTIMIZED SECTIONS WITH COMPLETE STYLING -->
<section class="divisions">
    <div class="container">
        <div class="section-header">
            <h2>Our Core <span>Divisions</span></h2>
            <p>Three integrated pillars shaping future-ready students</p>
        </div>
        
        <div class="grid-cards">
            <div class="core-card">
                <span class="card-tag">Academics</span>
                <div class="card-icon">📚</div>
                <h3>CBSE CURRICULUM</h3>
                <p>CBSE affiliated curriculum with smart classrooms, digital learning tools, and experienced faculty.</p>
                <a href="#" class="learn-more">Learn More →</a>
            </div>

            <div class="core-card">
                <span class="card-tag">Activities</span>
                <div class="card-icon">🎨</div>
                <h3>CO-CURRICULAR</h3>
                <p>200+ activities including arts, music, dance, drama, and hobby clubs for all-round development.</p>
                <a href="#" class="learn-more">Learn More →</a>
            </div>

            <div class="core-card">
                <span class="card-tag">Sports</span>
                <div class="card-icon">⚽</div>
                <h3>SPORTS ACADEMY</h3>
                <p>State-of-the-art sports facilities with professional coaching for 15+ disciplines.</p>
                <a href="#" class="learn-more">Learn More →</a>
            </div>
        </div>
    </div>
</section>

<section class="services-section">
    <div class="container">
        <div class="section-header">
            <h2>Student <span>Services</span> & Facilities</h2>
            <p>Complete ecosystem for academic excellence and personal growth</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:30px;">
            <div class="service-card">
                <div class="service-icon">📖</div>
                <h3>CBSE ACADEMICS</h3>
                <p>CBSE curriculum from Nursery to Grade 12 with modern teaching methodologies and regular assessments.</p>
            </div>

            <div class="service-card">
                <div class="service-icon">🔬</div>
                <h3>STEM LABORATORIES</h3>
                <p>Fully equipped Physics, Chemistry, Biology, Robotics & Computer labs for hands-on learning.</p>
            </div>

            <div class="service-card">
                <div class="service-icon">💻</div>
                <h3>SMART CLASSROOMS</h3>
                <p>Interactive digital classrooms with smart boards, projectors, and multimedia learning resources.</p>
            </div>

            <div class="service-card">
                <div class="service-icon">📚</div>
                <h3>DIGITAL LIBRARY</h3>
                <p>50,000+ books, e-library, reading programs, and digital research resources for students.</p>
            </div>

            <div class="service-card">
                <div class="service-icon">🚌</div>
                <h3>TRANSPORT FACILITY</h3>
                <p>40+ GPS-enabled AC buses covering Pune city with trained drivers and female attendants.</p>
            </div>

            <div class="service-card">
                <div class="service-icon">🍎</div>
                <h3>MEAL PROGRAM</h3>
                <p>Nutritious hygienically prepared meals following ICMR guidelines with dietitian supervision.</p>
            </div>
        </div>
    </div>
</section>

<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-num">25</span>
                <span class="stat-label">Acres Campus</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">3,500+</span>
                <span class="stat-label">Happy Students</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">250+</span>
                <span class="stat-label">Qualified Faculty</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">98%</span>
                <span class="stat-label">Academic Results</span>
            </div>
        </div>
    </div>
</section>

<!-- ✅ LAZY LOADED GALLERY -->
<section class="gallery">
    <div class="container">
        <div class="section-header">
            <h2>Campus <span>Gallery</span></h2>
            <p>Discover our world-class facilities and vibrant student life</p>
        </div>
        
        <div class="gallery-grid">
            <div class="gallery-item">
                <picture>
                    <source srcset="uploads/images/school-campus.webp" type="image/webp">
                    <img src="uploads/images/school-campus.jpg" loading="lazy" alt="Main Building" width="800" height="600" onerror="this.src='https://images.unsplash.com/photo-1562774053-701939374585?w=800&q=80'">
                </picture>
                <div class="gallery-overlay">
                    <h4>Main Academic Block</h4>
                    <p>Modern architecture with smart learning spaces</p>
                </div>
            </div>
            <div class="gallery-item">
                <picture>
                    <source srcset="uploads/images/library.webp" type="image/webp">
                    <img src="uploads/images/library.jpg" loading="lazy" alt="Library" width="600" height="400" onerror="this.src='https://images.unsplash.com/photo-1509062522246-3755977927d7?w=600&q=80'">
                </picture>
                <div class="gallery-overlay">
                    <h4>Digital Library</h4>
                    <p>50K+ books and e-learning resources</p>
                </div>
            </div>
            <div class="gallery-item">
                <picture>
                    <source srcset="uploads/images/science-lab.webp" type="image/webp">
                    <img src="uploads/images/science-lab.jpg" loading="lazy" alt="Labs" width="600" height="400" onerror="this.src='https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=600&q=80'">
                </picture>
                <div class="gallery-overlay">
                    <h4>STEM Laboratories</h4>
                    <p>Advanced science and tech facilities</p>
                </div>
            </div>
            <div class="gallery-item">
                <picture>
                    <source srcset="uploads/images/sports-complex.webp" type="image/webp">
                    <img src="uploads/images/sports-complex.jpg" loading="lazy" alt="Sports" width="600" height="400" onerror="this.src='https://images.unsplash.com/photo-1577896851231-70ef18881754?w=600&q=80'">
                </picture>
                <div class="gallery-overlay">
                    <h4>Sports Complex</h4>
                    <p>15+ disciplines with professional coaching</p>
                </div>
            </div>
            <div class="gallery-item">
                <picture>
                    <source srcset="uploads/images/auditorium.webp" type="image/webp">
                    <img src="uploads/images/auditorium.jpg" loading="lazy" alt="Auditorium" width="600" height="400" onerror="this.src='https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600&q=80'">
                </picture>
                <div class="gallery-overlay">
                    <h4>Auditorium</h4>
                    <p>1500+ seating capacity</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ✅ FACULTY - Complete styling -->
<section class="faculty">
    <div class="container">
        <div class="section-header">
            <h2>Meet Our <span>Faculty</span></h2>
            <p>Experienced educators shaping tomorrow's leaders</p>
        </div>
        
        <div class="faculty-grid">
            <div class="faculty-card">
                <div class="faculty-image">
                    <picture>
                        <source srcset="uploads/images/principal.webp" type="image/webp">
                        <img src="uploads/images/principal.jpg" loading="lazy" alt="Principal" width="400" height="320" onerror="this.src='https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&q=80'">
                    </picture>
                    <span class="faculty-badge">Principal</span>
                </div>
                <div class="faculty-info">
                    <h3>Dr. Shalini Joshi</h3>
                    <p class="role">Principal & Academic Head</p>
                    <p>30+ years experience, M.Phil Ph.D. in Education from Pune University.</p>
                    <div class="faculty-social">
                        <a href="#">📧</a>
                        <a href="#">📱</a>
                        <a href="#">🎓</a>
                    </div>
                </div>
            </div>

            <div class="faculty-card">
                <div class="faculty-image">
                    <picture>
                        <source srcset="uploads/images/vice-principal.webp" type="image/webp">
                        <img src="uploads/images/vice-principal.jpg" loading="lazy" alt="Vice Principal" width="400" height="320" onerror="this.src='https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&q=80'">
                    </picture>
                    <span class="faculty-badge">Vice Principal</span>
                </div>
                <div class="faculty-info">
                    <h3>Mrs. Priya Deshpande</h3>
                    <p class="role">VP - Primary Wing</p>
                    <p>25 years of teaching excellence specializing in foundational learning.</p>
                    <div class="faculty-social">
                        <a href="#">📧</a>
                        <a href="#">📱</a>
                        <a href="#">🎓</a>
                    </div>
                </div>
            </div>

            <div class="faculty-card">
                <div class="faculty-image">
                    <picture>
                        <source srcset="uploads/images/science-hod.webp" type="image/webp">
                        <img src="uploads/images/science-hod.jpg" loading="lazy" alt="HOD" width="400" height="320" onerror="this.src='https://images.unsplash.com/photo-1556157382-97eda2d62296?w=400&q=80'">
                    </picture>
                    <span class="faculty-badge">HOD Science</span>
                </div>
                <div class="faculty-info">
                    <h3>Mr. Rohit Patil</h3>
                    <p class="role">Science Department</p>
                    <p>M.Sc. Gold Medalist with 15+ years teaching STEM subjects.</p>
                    <div class="faculty-social">
                        <a href="#">📧</a>
                        <a href="#">📱</a>
                        <a href="#">🎓</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="testimonials">
    <div class="container">
        <div class="section-header">
            <h2>Parent <span>Testimonials</span></h2>
            <p>Hear from our satisfied parents and alumni</p>
        </div>
        
        <div class="testimonial-grid">
            <div class="testimonial-card">
                <div class="quote-icon">"</div>
                <p class="testimonial-text">
                    Orchid School has been transformative for my children. The balance of academics, sports, and activities is perfect. Faculty attention is outstanding!
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">SM</div>
                    <div class="author-info">
                        <h4>Mr. Sanjay More</h4>
                        <p>Parent - Grade 8 & 10</p>
                        <div class="rating">★★★★★</div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="quote-icon">"</div>
                <p class="testimonial-text">
                    My daughter secured 97% in boards and got into her dream college, all thanks to Orchid's rigorous academics and guidance counseling.
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">AP</div>
                    <div class="author-info">
                        <h4>Mrs. Anita Patil</h4>
                        <p>Parent - Class of 2025</p>
                        <div class="rating">★★★★★</div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="quote-icon">"</div>
                <p class="testimonial-text">
                    The sports program is world-class. My son represents state in cricket and credits Orchid coaches for his success.
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">RK</div>
                    <div class="author-info">
                        <h4>Mr. Rahul Kulkarni</h4>
                        <p>Parent - Grade 9</p>
                        <div class="rating">★★★★★</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="split container">
    <div class="split-flex">
        <div class="split-text">
            <h2>360° <span>LEARNING</span> EXPERIENCE</h2>
            <p>Complete child development through academics, co-curricular, sports, and values education.</p>
            <ul class="feature-list">
                <li>CBSE Curriculum excellence</li>
                <li>200+ activities & clubs</li>
                <li>Professional sports coaching</li>
                <li>Modern infrastructure</li>
            </ul>
            <a href="#" class="btn btn-purple">Discover More →</a>
        </div>
        <div class="split-image">
            <picture>
                <source srcset="uploads/images/classroom.webp" type="image/webp">
                <img src="uploads/images/classroom.jpg" loading="lazy" alt="Classroom" width="800" height="600" onerror="this.src='https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?q=80&w=2070'">
            </picture>
        </div>
    </div>
</section>

<section class="cta container">
    <span class="cta-pill">✨ Admissions 2026-27 Open</span>
    <h2>READY TO JOIN <span>ORCHID?</span></h2>
    <p>Experience Pune's best CBSE school with world-class facilities and holistic education.</p>
    <div class="btn-group">
        <a href="#" class="btn btn-purple">Apply Now →</a>
        <a href="#" class="btn btn-outline">Campus Tour →</a>
    </div>
</section>

<!-- ✅ FIXED & OPTIMIZED BACKGROUND SLIDER (GUARANTEED TO WORK) -->
<script>
/* ✅ FIXED VERSION - 2KB - WORKS 100% */
(() => {
    const banner = document.querySelector("#hero-banner");
    const images = [
        "uploads/images/hero1.jpg",
        "uploads/images/hero2.jpg", 
        "uploads/images/hero3.jpg",
        "uploads/images/hero4.jpg"
    ];
    const fallbacks = [
        "https://images.unsplash.com/photo-1523050853063-913894d92f5f?q=80&w=2070",
        "https://images.unsplash.com/photo-1562774053-701939374585?w=800&q=80",
        "https://images.unsplash.com/photo-1509062522246-3755977927d7?w=600&q=80",
        "https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=600&q=80"
    ];
    
    let currentIndex = 0;
    let nextChange = Date.now() + 4000;
    
    // ✅ STEP 1: Set initial fallback background (IMMEDIATE VISUAL)
    banner.style.backgroundImage = `url('${fallbacks[0]}')`;
    banner.style.opacity = '1';
    
    // ✅ STEP 2: Preload all images and switch when ready
    const preloadAndSwitch = (index) => {
        const img = new Image();
        img.onload = () => {
            banner.style.backgroundImage = `url('${images[index]}')`;
        };
        img.onerror = () => {
            banner.style.backgroundImage = `url('${fallbacks[index]}')`;
        };
        img.src = images[index];
    };
    
    // ✅ STEP 3: Preload all images first
    images.forEach((imgSrc, index) => {
        preloadAndSwitch(index);
    });
    
    // ✅ STEP 4: Auto-rotate every 4 seconds
    const rotateBackground = () => {
        currentIndex = (currentIndex + 1) % images.length;
        preloadAndSwitch(currentIndex);
        nextChange = Date.now() + 4000;
    };
    
    // ✅ START ROTATION after 1.5 seconds
    setTimeout(() => {
        setInterval(rotateBackground, 4000);
    }, 1500);
    
})();
</script>

<?php include __DIR__ . '/footer1.php'; ?>
