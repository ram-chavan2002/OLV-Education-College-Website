<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// HEADER GUARD - 100% SAFE
if (!defined('HEADER3_LOADED')) {
    define('HEADER3_LOADED', true);
    require_once(__DIR__ . '/header3.php');
}

$page_title = "Courses - OLV Academy";

$host = 'localhost'; $username = 'sai7755_college'; $password = 'Admin_66666'; $database = 'sai7755_college';
$conn = mysqli_connect($host, $username, $password, $database);
if (!$conn) die('DB Error: ' . mysqli_connect_error());
mysqli_set_charset($conn, 'utf8mb4');

$college_id = (int)($_GET['college_id'] ?? 1);

$stmt = $conn->prepare("SELECT * FROM mum_courese WHERE college_id=? AND status='active' ORDER BY featured DESC, id DESC LIMIT 8");
$stmt->bind_param("i", $college_id);
$stmt->execute();
$result = $stmt->get_result();
$courses = [];
while($row = $result->fetch_assoc()) {
    $courses[] = $row;
}
$debug_stmt = $conn->prepare("SELECT COUNT(*) as count FROM mum_courese WHERE college_id=? AND status='active'");
$debug_stmt->bind_param("i", $college_id);
$debug_stmt->execute();
$debug_count = $debug_stmt->get_result()->fetch_assoc();
error_log("Courses count: " . $debug_count['count']);
?>



<style>


/* Hero */
.courses-hero {
    height: clamp(400px, 70vh, 600px);
    background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.8)), 
    url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=2070') center/cover fixed;
    display: flex; align-items: center; justify-content: center; text-align: center; color: white;
}
.hero-content { max-width: 900px; animation: fadeIn 1s ease-out; }
@keyframes fadeIn { from {opacity:0; transform:translateY(20px);} }
.hero-tag { 
    font-size: clamp(0.85rem,2vw,1rem); letter-spacing: 3px; margin-bottom: 1rem; 
    color: #d4a34d; padding: 0.5rem 1.5rem; border: 1px solid #d4a34d; 
    border-radius: 25px; display: inline-block; animation: pulse 2s infinite;
}
@keyframes pulse { 50% { transform: scale(1.05); } }
.hero h1 { font-size: clamp(2.5rem,7vw,4.5rem); font-weight: 800; margin-bottom: 1rem; }
.hero h1 span { color: #d4a34d; }
.hero-sub { font-size: clamp(1.2rem,3vw,1.8rem); margin-bottom: 2rem; }

.btn-group { gap: 1rem; }
.btn-custom { 
    padding: 1rem 2rem; font-weight: 700; border-radius: 12px; border: none; 
    cursor: pointer; transition: all 0.3s ease; position: relative; overflow: hidden;
}
.btn-custom::before { 
    content: ''; position: absolute; inset: 0; background: linear-gradient(90deg,transparent,rgba(255,255,255,0.2),transparent);
    transform: translateX(-100%); transition: transform 0.5s;
}
.btn-custom:hover::before { transform: translateX(100%); }
.btn-gold { background: linear-gradient(135deg,#d4a34d,#b8860b); color: white; }
.btn-gold:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(212,163,77,0.4); }
.btn-outline { background: rgba(255,255,255,0.15); color: white; border: 2px solid rgba(255,255,255,0.5); }
.btn-outline:hover { background: white; color: #333; transform: translateY(-3px); }

/* Courses */
.courses-section { padding: 5rem 0; background: linear-gradient(to bottom,#f8f9fa,#e9ecef); }
.section-header { text-align: center; margin-bottom: 4rem; }
.section-header h2 { 
    font-size: clamp(2.2rem,5vw,3.5rem); margin-bottom: 1rem; font-weight: 700;
    color: #1f2937; position: relative;
}
.section-header h2::after {
    content: ''; position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%);
    width: 80px; height: 3px; background: linear-gradient(90deg,#d4a34d,#6a4c93); border-radius: 2px;
}
.section-header p { color: #6c757d; font-size: 1.15rem; max-width: 600px; margin: 0 auto; }

.container-fluid { padding: 0 2rem; }
.course-grid { opacity: 0; animation: slideUp 1s ease forwards 0.5s; }
@keyframes slideUp { to { opacity: 1; transform: translateY(0); } }

.card { 
    border: none; border-radius: 16px; height: 100%; overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08); transition: all 0.4s ease;
}
.card:hover { transform: translateY(-12px); box-shadow: 0 25px 45px rgba(0,0,0,0.15); }
.card-img-wrapper { height: 240px; position: relative; overflow: hidden; }
.card-img-top { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s; }
.card:hover .card-img-top { transform: scale(1.1); }
.featured-badge {
    position: absolute; top: 15px; left: 15px; background: linear-gradient(135deg,#d4a34d,#b8860b);
    color: white; padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700;
}
.type-badge { 
    background: linear-gradient(135deg,#6a4c93,#5a3a7d); color: white; padding: 0.35rem 1rem;
    border-radius: 20px; font-size: 0.8rem; font-weight: 600; margin-bottom: 1rem;
}
.card-title { font-size: 1.45rem; font-weight: 700; margin-bottom: 0.8rem; color: #1f2937; }
.card-meta { display: flex; justify-content: space-between; margin-bottom: 1rem; font-size: 0.9rem; color: #6c757d; }
.fee-amount { font-size: 1.85rem; font-weight: 800; color: #d4a34d; }
.card-cta { 
    width: 100%; padding: 0.85rem; background: #1f2937; color: white; border-radius: 10px;
    font-weight: 700; font-size: 0.9rem; transition: all 0.3s ease; margin-top: auto;
}
.card-cta:hover { background: #d4a34d; transform: translateY(-2px); }

.no-courses { text-align: center; padding: 6rem 2rem; background: white; border-radius: 16px; margin: 2rem auto; max-width: 500px; box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
.no-courses h3 { font-size: 2.5rem; color: #6a4c93; margin-bottom: 1rem; }

/* Responsive */
@media (max-width: 768px) { .container-fluid { padding: 0 1rem; } .courses-hero { background-attachment: scroll; } }
</style>

<!-- Hero -->
<section class="courses-hero">
    <div class="hero-content">
        <span class="hero-tag">Premium Education</span>
        <h1>Our <span>Courses</span></h1>
        <p class="hero-sub">Transform Your Future</p>
        <div class="btn-group">
            <a href="#courses" class="btn-custom btn-gold">Explore Courses</a>
            <a href="contact.php" class="btn-custom btn-outline">Contact Us</a>
        </div>
    </div>
</section>

<!-- Courses -->
<section class="courses-section" id="courses">
    <div class="container-fluid">
        <div class="section-header">
            <h2>Featured <span>Programs</span></h2>
            <p>Premium courses with guaranteed placement assistance</p>
        </div>

        <?php if(empty($courses)): ?>
            <div class="no-courses">
                <h3>No Courses Found</h3>
                <p>Contact us for upcoming academic programs</p>
                <a href="contact.php" class="btn-custom btn-gold">Contact Admissions</a>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4 course-grid">
                <?php foreach($courses as $row): ?>
                <?php 
                $img = !empty($row['course_image']) ? htmlspecialchars($row['course_image']) : "https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=500&q=85";
                if (!str_starts_with($img, 'http')) $img = '/uploads/courses/' . basename($img);
                ?>
                <div class="col">
                    <a href="course-details.php?id=<?php echo $row['id']; ?>" class="card h-100 text-decoration-none">
                        <div class="card-img-wrapper">
                            <img src="<?php echo $img; ?>" class="card-img-top" loading="lazy" alt="Course" 
                                 onerror="this.src='https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=500&q=85'">
                            <?php if($row['featured']): ?><div class="featured-badge">Featured</div><?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <span class="type-badge"><?php echo htmlspecialchars($row['course_type'] ?? 'UG'); ?></span>
                            <h5 class="card-title"><?php echo htmlspecialchars($row['course_name']); ?></h5>
                            <div class="card-meta">
                                <span><?php echo htmlspecialchars($row['duration'] ?? '3 Years'); ?></span>
                                <span><?php echo htmlspecialchars($row['intake_year'] ?? '2026'); ?></span>
                            </div>
                            <div class="fee-amount">₹<?php echo number_format($row['fees'] ?? 0); ?></div>
                            <button class="card-cta mt-auto">View Details →</button>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.querySelectorAll('.course-grid .col').forEach((col,i) => {
    col.style.opacity = '0'; col.style.transform = 'translateY(30px)';
    col.style.transition = `all 0.6s ease ${i*0.1}s`;
});
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
});
document.querySelectorAll('.course-grid .col').forEach(col => observer.observe(col));
</script>

<?php $stmt->close(); $conn->close(); include(__DIR__ . '/footer3.php'); ?>
