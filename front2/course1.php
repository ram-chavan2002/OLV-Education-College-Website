<?php
$page_title = "Courses - OLV Academy";
include(__DIR__ . '/header1.php'); // college/front2/header1.php
?>

<?php
// Database Connection
$conn = mysqli_connect('localhost', 'sai7755_college', 'Admin_66666', 'sai7755_college');
if (!$conn) die('DB Error');
mysqli_set_charset($conn, 'utf8mb4');

// 🔥 CORRECT QUERY - school_courses table
$result = mysqli_query($conn, "SELECT * FROM school_courses WHERE status='active' ORDER BY featured DESC, id DESC");
?>

<style>
:root {
    --gold: #d4a34d;
    --white: #ffffff;
    --text-main: #1a1a1a;
    --text-gray: #666;
}
* {margin:0;padding:0;box-sizing:border-box;}
body {font-family: 'Inter', sans-serif;}

/* Hero Section */
.courses-hero {
    height: 100vh;
    background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.9)), 
    url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=2070') center/cover;
    display: flex; align-items: center; justify-content: center; 
    text-align: center; color: white;
}
.hero-content {max-width: 900px; padding: 0 20px;}
.hero h1 {font-size: 4rem; font-weight: 900; margin: 0 0 20px;}
.hero h1 span {color: var(--gold);}
.hero p {font-size: 1.3rem; margin-bottom: 30px;}
.btn {padding: 15px 35px; font-weight: 700; text-decoration: none; 
      border-radius: 5px; display: inline-block; margin: 0 10px;
      font-family: 'Orbitron', sans-serif;}
.btn-gold {background: var(--gold); color: white;}
.btn-gold:hover {background: transparent; transform: translateY(-3px); transition: 0.3s;}

/* Courses Section */
.courses-section {padding: 100px 0; background: #f8f9fa;}
.container {max-width: 1200px; margin: 0 auto; padding: 0 20px;}
.section-header {text-align: center; margin-bottom: 60px;}
.section-header h2 {font-size: 3rem; font-weight: 900; margin-bottom: 15px;}
.section-header h2 span {color: var(--gold);}
.course-grid {
    display: grid; 
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}
.course-card {
    background: white; border-radius: 15px; overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: transform 0.4s, box-shadow 0.4s;
    text-decoration: none; color: inherit; display: block;
    position: relative;
}
.course-card:hover {
    transform: translateY(-15px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.2);
}
.course-image {width: 100%; height: 250px; object-fit: cover;}
.featured-badge {
    position: absolute; top: 20px; left: 20px; 
    background: var(--gold); color: white; padding: 8px 20px; 
    border-radius: 25px; font-weight: 700; z-index: 2;
}
.course-content {padding: 30px;}
.course-title {font-size: 1.8rem; font-weight: 800; margin-bottom: 15px;}
.course-meta {display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 0.95rem; color: #666;}
.course-fee {font-size: 2.2rem; font-weight: 900; color: var(--text-main);}
.course-fee small {font-size: 0.8rem; font-weight: 500; margin-left: 8px;}
.course-cta {
    display: block; width: 100%; padding: 15px; background: var(--text-main);
    color: white; text-align: center; border-radius: 8px;
    font-weight: 700; transition: 0.3s; margin-top: 20px;
}
.course-cta:hover {background: var(--gold);}

.no-courses {text-align: center; padding: 100px 20px;}
.no-courses h3 {font-size: 3rem; color: var(--text-gray); margin-bottom: 20px;}
.no-courses p {font-size: 1.2rem; color: #888;}

/* Responsive */
@media (max-width: 1200px) {
    .course-grid {grid-template-columns: repeat(2, 1fr);}
}
@media (max-width: 768px) {
    .course-grid {grid-template-columns: 1fr;}
    .hero h1 {font-size: 2.5rem;}
    .section-header h2 {font-size: 2rem;}
}
</style>

<!-- Hero -->
<section class="courses-hero">
    <div class="hero-content">
        <h1 class="hero">PREMIUM <span>COURSES</span></h1>
        <p>Industry-Leading Programs for Your Dream Career</p>
        <a href="#courses" class="btn btn-gold">Explore Courses</a>
        <a href="../contact.php" class="btn" style="background: white; color: var(--text-main);">Contact Us</a>
    </div>
</section>

<!-- Courses -->
<section class="courses-section" id="courses">
    <div class="container">
        <div class="section-header">
            <h2>Our <span>Programs</span></h2>
            <p>Choose from world-class courses designed by industry experts</p>
        </div>
        
        <?php if(mysqli_num_rows($result) > 0): ?>
            <div class="course-grid">
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <a href="course-details.php?id=<?php echo $row['id']; ?>" class="course-card">
                    <img src="<?php 
                        $img_path = !empty($row['course_image']) && file_exists('../orc_sch/' . $row['course_image']) 
                            ? '../orc_sch/' . $row['course_image'] 
                            : 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=500&fit=crop';
                        echo htmlspecialchars($img_path);
                    ?>" class="course-image" alt="<?php echo htmlspecialchars($row['course_name']); ?>">
                    
                    <?php if((int)$row['featured'] === 1): ?>
                        <div class="featured-badge">⭐ Featured</div>
                    <?php endif; ?>
                    
                    <div class="course-content">
                        <div class="course-title"><?php echo htmlspecialchars($row['course_name']); ?></div>
                        
                        <div class="course-meta">
                            <span>⏰ <?php echo htmlspecialchars($row['duration'] ?? '3 Years'); ?></span>
                            <span>📅 <?php echo htmlspecialchars($row['intake_year'] ?? '2026'); ?></span>
                        </div>
                        
                        <div class="course-fee">
                            ₹<?php echo number_format((float)($row['fees'] ?? 0), 0); ?>
                            <small><?php echo htmlspecialchars($row['fee_type'] ?? '/year'); ?></small>
                        </div>
                        
                        <div class="course-cta">View Complete Details →</div>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-courses">
                <h3>No Courses Found</h3>
                <p>Add courses from <strong>orc_sch/course.php</strong> admin panel first</p>
                <a href="../contact.php" class="btn btn-gold" style="padding: 15px 40px; font-size: 1.1rem; margin-top: 20px;">Contact for Courses</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php 
mysqli_free_result($result);
mysqli_close($conn);
include(__DIR__ . '/footer1.php'); // college/front2/footer1.php bhi hai toh
?>
