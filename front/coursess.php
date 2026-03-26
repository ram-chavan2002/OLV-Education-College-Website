<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "Courses - OLV Academy";
include(__DIR__ . '/header.php');

// 🔥 BUILT-IN CONNECTION
$host = 'localhost'; $username = 'sai7755_college'; $password = 'Admin_66666'; $database = 'sai7755_college';
$conn = mysqli_connect($host, $username, $password, $database);
if (!$conn) die('DB Error');
mysqli_set_charset($conn, 'utf8mb4');

$college_id = (int)($_GET['college_id'] ?? 1);

// 🔥 YOUR ADMIN TABLE: college_courses
$stmt = $conn->prepare("SELECT * FROM college_courses WHERE college_id=? AND status='active' ORDER BY featured DESC, id DESC");
$stmt->bind_param("i", $college_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<style>
:root {
    --gold: #d4a34d;
    --dark-bg: #0a0a0a;
    --card-bg: #ffffff;
    --text-main: #1a1a1a;
    --text-gray: #666666;
    --white: #ffffff;
    --overlay: rgba(0, 0, 0, 0.75);
}

/* HERO - SAME AS HOME */
.courses-hero {
    position: relative;
    height: 100vh;
    min-height: 750px;
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=2070') center/cover;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
}

.courses-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: linear-gradient(to bottom, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.75) 100%);
    z-index: 1;
}

.hero-content {position: relative; z-index: 2; max-width: 1000px; animation: fadeInUp 1.2s ease;}
.hero-tag {
    font-size: 0.8rem; letter-spacing: 5px; margin-bottom: 25px;
    border-bottom: 1px solid var(--gold); padding-bottom: 5px;
    text-transform: uppercase; color: var(--gold);
}
.hero h1 {
    font-size: clamp(3rem, 8vw, 6rem); font-weight: 900; line-height: 1.1;
    margin-bottom: 20px; letter-spacing: -2px;
}
.hero h1 span {color: var(--gold);}
.hero-sub {
    font-size: clamp(1.3rem, 2.5vw, 2rem); margin-bottom: 35px;
    font-weight: 300; letter-spacing: 1px;
}
.hero-desc {
    max-width: 750px; margin: 0 auto 45px; font-size: 1.1rem;
    opacity: 0.9; font-weight: 300;
}
.btn-group {display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;}
.btn {
    padding: 20px 45px; font-size: 0.95rem; font-weight: 700;
    text-transform: uppercase; text-decoration: none; border-radius: 4px;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    display: inline-flex; align-items: center; gap: 12px;
    font-family: 'Orbitron', sans-serif;
}
.btn-gold {background: var(--gold); color: white; border: 1px solid var(--gold);}
.btn-gold:hover {background: transparent; color: var(--gold); transform: translateY(-5px);}
.btn-outline {background: white; color: black; border: 1px solid white;}
.btn-outline:hover {background: transparent; color: white; transform: translateY(-5px);}

.courses-section {
    padding: 140px 0; background: #fcfcfc;
}
.section-header {
    text-align: center; margin-bottom: 90px;
}
.section-header h2 {
    font-size: clamp(2.8rem, 6vw, 4rem); font-weight: 900; margin-bottom: 25px;
}
.section-header h2 span {color: var(--gold);}
.section-header p {
    color: var(--text-gray); font-size: 1.25rem; max-width: 700px;
    margin-left: auto; margin-right: auto;
}
.container {max-width: 1400px; margin: 0 auto; padding: 0 20px;}

.course-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
    gap: 45px;
}
.course-card {
    background: var(--white); border-radius: 20px; overflow: hidden;
    box-shadow: 0 25px 60px rgba(0,0,0,0.08);
    transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
    position: relative; cursor: pointer;
}
.course-card:hover {
    transform: translateY(-25px); box-shadow: 0 50px 120px rgba(0,0,0,0.15);
}
.course-image-wrapper {
    height: 320px; position: relative; overflow: hidden;
}
.course-image {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform 1.5s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.course-card:hover .course-image {transform: scale(1.12);}
.course-badge {
    position: absolute; top: 30px; left: 30px;
    background: linear-gradient(135deg, var(--gold), #c4933d);
    color: white; padding: 15px 28px; border-radius: 50px;
    font-weight: 800; font-size: 0.95rem; letter-spacing: 1.5px;
    box-shadow: 0 12px 35px rgba(212,163,77,0.4);
    backdrop-filter: blur(15px); z-index: 2;
}
.course-content {padding: 50px;}
.course-type-badge {
    display: inline-block; background: linear-gradient(135deg, #667eea, #764ba2);
    color: white; padding: 12px 28px; border-radius: 30px;
    font-size: 0.9rem; font-weight: 700; margin-bottom: 25px;
    letter-spacing: 0.8px;
}
.course-title {
    font-size: 2.4rem; font-weight: 900; margin-bottom: 20px;
    line-height: 1.2; color: var(--text-main);
}
.course-meta {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 35px; padding: 30px 0;
    border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;
}
.course-meta span {
    display: flex; align-items: center; gap: 12px;
    background: #f8fafc; padding: 18px 28px; border-radius: 16px;
    font-weight: 700; color: #475569; font-size: 1rem;
}
.course-fee {
    font-size: 3.2rem; font-weight: 900;
    background: linear-gradient(135deg, var(--text-main), #4a5568);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    margin-bottom: 45px;
}
.course-fee small {
    font-size: 1.2rem; font-weight: 600; color: var(--text-gray);
    margin-left: 12px;
}
.course-cta {
    display: block; width: 100%; padding: 25px;
    background: linear-gradient(135deg, var(--text-main), #4a5568);
    color: white; text-align: center; text-decoration: none;
    border-radius: 20px; font-weight: 800; font-size: 1.2rem;
    transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
    position: relative; overflow: hidden;
}
.course-cta::before {
    content: ''; position: absolute; top: 0; left: -100%;
    width: 100%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.8s;
}
.course-cta:hover {
    background: linear-gradient(135deg, var(--gold), #c4933d);
    transform: translateY(-8px);
    box-shadow: 0 30px 70px rgba(212,163,77,0.4);
}
.course-cta:hover::before {left: 100%;}

.no-courses {
    text-align: center; padding: 160px 60px; color: var(--text-gray);
}
.no-courses h3 {
    font-size: 4.5rem; margin-bottom: 35px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}
.no-courses p {
    font-size: 1.6rem; margin-bottom: 55px; max-width: 700px;
    margin-left: auto; margin-right: auto;
}

/* ANIMATIONS */
@keyframes fadeInUp {
    from {opacity: 0; transform: translateY(40px);}
    to {opacity: 1; transform: translateY(0);}
}

/* RESPONSIVE */
@media (max-width: 1024px) {.course-grid {gap: 35px;}}
@media (max-width: 768px) {
    .courses-hero {height: 90vh; padding: 0 20px;}
    .course-grid {grid-template-columns: 1fr; gap: 35px;}
    .btn-group {flex-direction: column; align-items: center;}
    .course-meta {flex-direction: column; gap: 20px;}
    .course-meta span {justify-content: center;}
}
</style>

<!-- HERO -->
<section class="courses-hero">
    <div class="hero-content">
        <span class="hero-tag">Programs That Transform Careers</span>
        <h1>PREMIUM <span>COURSES</span></h1>
        <p class="hero-sub">Industry-Leading Curriculum</p>
        <p class="hero-desc">Choose from our carefully crafted programs designed by global experts to launch your dream career with cutting-edge skills and real-world exposure.</p>
        <div class="btn-group">
            <a href="#courses" class="btn btn-gold">Explore Programs</a>
            <a href="contact.php" class="btn btn-outline">Admission Enquiry</a>
        </div>
    </div>
</section>

<!-- COURSES GRID -->
<section class="courses-section" id="courses">
    <div class="container">
        <div class="section-header">
            <h2>Featured <span>Programs</span></h2>
            <p>Join 10,000+ students who transformed their careers through our world-class programs</p>
        </div>

        <?php if(mysqli_num_rows($result) > 0): ?>
            <div class="course-grid">
                <?php while($row = $result->fetch_assoc()): ?>
                <a href="course-details.php?id=<?php echo $row['id']; ?>" class="course-card">
                    <div class="course-image-wrapper">
                        <?php 
                        $img = !empty($row['course_image']) && file_exists(__DIR__ . '/../' . $row['course_image'])
                            ? '../' . $row['course_image']
                            : 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800&fit=crop';
                        ?>
                        <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($row['course_name']); ?>" class="course-image">
                        
                        <?php if($row['featured']): ?>
                            <div class="course-badge">Featured Program</div>
                        <?php endif; ?>
                    </div>

                    <div class="course-content">
                        <span class="course-type-badge">
                            <?php echo htmlspecialchars($row['course_type'] ?? 'UG'); ?> • 
                            <?php echo htmlspecialchars($row['category'] ?? $row['department'] ?? 'Premium'); ?>
                        </span>
                        
                        <h3 class="course-title"><?php echo htmlspecialchars($row['course_name']); ?></h3>
                        
                        <div class="course-meta">
                            <span>
                                🕒 <?php echo htmlspecialchars($row['duration'] ?? '3 Years'); ?>
                            </span>
                            <span>
                                📅 <?php echo htmlspecialchars($row['intake_year'] ?? '2026'); ?>
                            </span>
                        </div>

                        <div class="course-fee">
                            ₹<?php echo number_format($row['fees'] ?? 0); ?>
                            <small><?php echo htmlspecialchars($row['fee_type'] ?? '/year'); ?></small>
                        </div>

                        <div class="course-cta">
                            Explore Complete Details →
                        </div>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-courses">
                <h3>No Programs Available</h3>
                <p>Our premium programs are being curated for the upcoming academic session. Contact us for early access and personalized guidance.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php $stmt->close(); $conn->close(); include(__DIR__ . '/footer.php'); ?>
