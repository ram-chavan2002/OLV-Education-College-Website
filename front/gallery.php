<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "Gallery - OLV Academy";
include(__DIR__ . '/header.php');

// 🔥 DIRECT CONNECTION - YOUR CREDENTIALS
$host = 'localhost'; 
$username = 'sai7755_college'; 
$password = 'Admin_66666';  // Your password
$database = 'sai7755_college';

$conn = mysqli_connect($host, $username, $password, $database);
if (!$conn) die('DB Connection Failed');
mysqli_set_charset($conn, 'utf8mb4');

$college_id = (int)($_GET['college_id'] ?? 1);

// 🔥 YOUR GALLERY TABLE: college_gallery
$stmt = $conn->prepare("
    SELECT * FROM college_gallery 
    WHERE (college_id=? OR college_id IS NULL) 
    AND status IN ('active', '') 
    AND (file_path != '' OR thumbnail != '' OR video_url != '')
    ORDER BY featured DESC, created_at DESC 
    LIMIT 36
");
$stmt->bind_param("i", $college_id);
$stmt->execute();
$gallery_result = $stmt->get_result();
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

.gallery-hero {
    position: relative;
    height: 100vh;
    min-height: 750px;
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), 
                url('https://images.unsplash.com/photo-1523050853063-913894d92f5f?q=80&w=2070') center/cover;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
}

.gallery-hero::before {
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
.hero-sub {font-size: clamp(1.3rem, 2.5vw, 2rem); margin-bottom: 35px; font-weight: 300;}
.hero-desc {max-width: 750px; margin: 0 auto 45px; font-size: 1.1rem; opacity: 0.9; font-weight: 300;}

.gallery-section {padding: 140px 0; background: #fcfcfc;}
.section-header {
    text-align: center; margin-bottom: 90px;
}
.section-header h2 {
    font-size: clamp(2.8rem, 6vw, 4rem); font-weight: 900; margin-bottom: 25px;
}
.section-header h2 span {color: var(--gold);}
.section-header p {color: var(--text-gray); font-size: 1.25rem; max-width: 700px; margin: 0 auto;}

.container {max-width: 1400px; margin: 0 auto; padding: 0 20px;}
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
    gap: 40px;
}
.gallery-card {
    background: var(--white);
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 25px 60px rgba(0,0,0,0.08);
    transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
    cursor: pointer;
    position: relative;
}
.gallery-card:hover {
    transform: translateY(-20px);
    box-shadow: 0 50px 120px rgba(0,0,0,0.15);
}

.gallery-image-wrapper {
    height: 400px;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
}
.gallery-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 1.2s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.gallery-card:hover .gallery-image {transform: scale(1.1);}
.media-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
    opacity: 0;
    transition: opacity 0.4s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.gallery-card:hover .media-overlay {opacity: 1;}
.play-icon {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.95);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    backdrop-filter: blur(20px);
    font-size: 2rem;
    color: #333;
}
.gallery-badge {
    position: absolute;
    top: 25px;
    left: 25px;
    background: linear-gradient(135deg, var(--gold), #c4933d);
    color: white;
    padding: 12px 24px;
    border-radius: 50px;
    font-weight: 800;
    font-size: 0.85rem;
    letter-spacing: 1.2px;
    box-shadow: 0 10px 30px rgba(212,163,77,0.4);
    z-index: 3;
    backdrop-filter: blur(15px);
}

.gallery-content {padding: 45px;}
.gallery-cat {
    display: inline-block;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 10px 24px;
    border-radius: 30px;
    font-size: 0.9rem;
    font-weight: 700;
    margin-bottom: 20px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.gallery-title {
    font-size: 2.2rem;
    font-weight: 900;
    margin-bottom: 18px;
    line-height: 1.25;
    color: var(--text-main);
}
.gallery-desc {
    color: var(--text-gray);
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 30px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.gallery-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 25px 0;
    border-top: 1px solid #f1f5f9;
    font-size: 1rem;
    color: var(--text-gray);
    font-weight: 600;
}
.gallery-meta span {
    display: flex;
    align-items: center;
    gap: 8px;
}
.views-count {color: var(--gold); font-weight: 800;}

.gallery-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.95);
    backdrop-filter: blur(20px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.gallery-modal.active {
    opacity: 1;
    visibility: visible;
}
.modal-content {
    max-width: 90vw;
    max-height: 90vh;
    position: relative;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 50px 150px rgba(0,0,0,0.8);
}
.modal-close {
    position: absolute;
    top: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border: none;
    border-radius: 50%;
    color: white;
    font-size: 1.8rem;
    cursor: pointer;
    backdrop-filter: blur(20px);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    z-index: 10;
}
.modal-close:hover {
    background: rgba(255,255,255,0.4);
    transform: scale(1.1);
}
.modal-media {
    width: 100%;
    height: 100%;
    object-fit: contain;
    background: #000;
}
.modal-video video {width: 100%; height: 100%; object-fit: contain; background: #000;}

@keyframes fadeInUp {
    from {opacity: 0; transform: translateY(40px);}
    to {opacity: 1; transform: translateY(0);}
}

.no-gallery {
    text-align: center;
    padding: 160px 60px;
    color: var(--text-gray);
}
.no-gallery-icon {
    font-size: 8rem;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 35px;
    opacity: 0.6;
}

/* RESPONSIVE */
@media (max-width: 1024px) {.gallery-grid {gap: 30px;}}
@media (max-width: 768px) {
    .gallery-grid {grid-template-columns: 1fr; gap: 30px;}
    .gallery-image-wrapper {height: 300px;}
    .gallery-content {padding: 30px;}
}

.banner-section-static {
    background-image: url('uploads/gallery/hero-static.webp');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

</style>

<!-- HERO SECTION -->
<section class="gallery-hero banner-section-static">
    <div class="hero-content">
        <span class="hero-tag">Campus Life Captured</span>
        <h1>EDUCATION <span>GALLERY</span></h1>
        <p class="hero-sub">Every Moment Preserved</p>
        <p class="hero-desc">
            Relive the journey through our stunning collection of photographs and videos showcasing academic excellence, campus life, and unforgettable memories.
        </p>
    </div>
</section>


<!-- GALLERY GRID -->
<section class="gallery-section" id="gallery">
    <div class="container">
        <div class="section-header">
            <h2>Campus <span>Memories</span></h2>
            <p>Thousands of moments captured across events, facilities, achievements, and everyday excellence</p>
        </div>

        <?php if(mysqli_num_rows($gallery_result) > 0): ?>
            <div class="gallery-grid">
                <?php while($row = $gallery_result->fetch_assoc()): ?>
                <div class="gallery-card" onclick="openGalleryModal(<?= json_encode($row) ?>)">
                    <div class="gallery-image-wrapper">
                        <?php 
                        // 🔥 YOUR EXACT IMAGE LOGIC
                        $thumb_path = !empty($row['thumbnail']) ? '../' . $row['thumbnail'] : '../' . $row['file_path'];
                        $preview_img = file_exists(__DIR__ . '/' . $row['thumbnail']) || file_exists(__DIR__ . '/../' . $row['thumbnail']) 
                                     ? $thumb_path 
                                     : (!empty($row['file_path']) ? '../' . $row['file_path'] : 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800');
                        
                        // Video thumbnail fallback
                        $is_video = $row['media_type'] === 'video';
                        ?>
                        <img src="<?= htmlspecialchars($preview_img) ?>" 
                             alt="<?= htmlspecialchars($row['title']) ?>" 
                             class="gallery-image"
                             onerror="this.src='https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800'">
                        
                        <?php if($row['featured']): ?>
                            <div class="gallery-badge">Featured Moment</div>
                        <?php endif; ?>
                        
                        <?php if($is_video): ?>
                            <div class="media-overlay">
                                <div class="play-icon">
                                    ▶
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="gallery-content">
                        <div class="gallery-cat">
                            <?= htmlspecialchars($row['category'] ?? $row['media_type'] ?? 'Campus') ?>
                        </div>
                        
                        <h3 class="gallery-title"><?= htmlspecialchars($row['title']) ?></h3>
                        
                        <?php if($row['description']): ?>
                        <p class="gallery-desc"><?= htmlspecialchars($row['description']) ?></p>
                        <?php endif; ?>
                        
                        <div class="gallery-meta">
                            <span>
                                📅 <?= date('M d, Y', strtotime($row['created_at'])) ?>
                            </span>
                            <span class="views-count">
                                👁 <?= number_format($row['views'] ?? 0) ?>
                            </span>
                        </div>
                        
                        <?php if($row['tags']): ?>
                        <div class="flex flex-wrap gap-2 mt-6 pt-6 border-t border-slate-200">
                            <?php foreach(explode(',', $row['tags']) as $tag): 
                                $tag = trim($tag);
                                if($tag): ?>
                            <span class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white text-xs font-bold rounded-full shadow-md">
                                #<?= htmlspecialchars($tag) ?>
                            </span>
                            <?php endif; endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-gallery">
                <div class="no-gallery-icon">📸</div>
                <h3>No Gallery Available</h3>
                <p>Gallery content is being prepared. Check back soon for amazing campus moments, events, and memories.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- GALLERY MODAL -->
<div class="gallery-modal" id="galleryModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeGalleryModal()">✕</button>
        <div id="modalMedia" class="w-full h-full"></div>
    </div>
</div>

<script>
function openGalleryModal(row) {
    const modal = document.getElementById('galleryModal');
    const media = document.getElementById('modalMedia');
    
    // 🔥 YOUR EXACT MODAL LOGIC
    const thumbPath = row.thumbnail ? '../' + row.thumbnail : '../' + row.file_path;
    const videoPath = '../' + (row.video_url || row.file_path);
    
    if(row.media_type === 'video') {
        media.innerHTML = `
            <video controls class="modal-video" poster="${thumbPath}">
                <source src="${videoPath}" type="video/mp4">
                Video not supported
            </video>
        `;
    } else {
        media.innerHTML = `
            <img src="${thumbPath}" alt="${row.title}" class="modal-media">
        `;
    }
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeGalleryModal() {
    document.getElementById('galleryModal').classList.remove('active');
    document.body.style.overflow = '';
}

// Close on ESC or outside click
document.addEventListener('keydown', e => {
    if(e.key === 'Escape') closeGalleryModal();
});
document.getElementById('galleryModal').onclick = e => {
    if(e.target.classList.contains('gallery-modal')) closeGalleryModal();
};
</script>

<?php 
$stmt->close();
mysqli_close($conn);
include(__DIR__ . '/footer.php');
?>
