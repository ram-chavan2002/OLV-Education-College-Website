<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$current_page = basename($_SERVER['PHP_SELF']);
$page_title = "Gallery - OLV School";
include(__DIR__ . '/header1.php');

// DB Connection
$host = 'localhost';
$username = 'sai7755_college';
$password = 'Admin_66666';
$database = 'sai7755_college';

$conn = mysqli_connect($host, $username, $password, $database);
if (!$conn) die('DB Connection Failed');
mysqli_set_charset($conn, 'utf8mb4');

$school_id = (int)($_GET['school_id'] ?? 3);

// Query 1: ALL MEDIA (Images + Videos Combined)
$stmt_all = $conn->prepare("
    SELECT * FROM media_sch 
    WHERE (school_id=? OR school_id IS NULL) 
      AND status IN ('active', '') 
      AND (file_path != '' OR thumbnail != '' OR video_url != '')
    ORDER BY featured DESC, display_order DESC, created_at DESC 
    LIMIT 72
");
$stmt_all->bind_param("i", $school_id);
$stmt_all->execute();
$all_result = $stmt_all->get_result();
$all_count = mysqli_num_rows($all_result);

// Query 2: Images Only
$stmt_images = $conn->prepare("
    SELECT * FROM media_sch 
    WHERE (school_id=? OR school_id IS NULL) 
      AND status IN ('active', '') 
      AND (file_path != '' OR thumbnail != '')
      AND (media_type != 'video' AND video_url = '' AND file_path NOT REGEXP '\\.(mp4|avi|mov|mkv|webm)$')
    ORDER BY featured DESC, display_order DESC, created_at DESC 
    LIMIT 36
");
$stmt_images->bind_param("i", $school_id);
$stmt_images->execute();
$images_result = $stmt_images->get_result();
$images_count = mysqli_num_rows($images_result);

// Query 3: Videos Only
$stmt_videos = $conn->prepare("
    SELECT * FROM media_sch 
    WHERE (school_id=? OR school_id IS NULL) 
      AND status IN ('active', '') 
      AND (video_url != '' OR media_type = 'video' OR file_path REGEXP '\\.(mp4|avi|mov|mkv|webm)$')
    ORDER BY featured DESC, display_order DESC, created_at DESC 
    LIMIT 36
");
$stmt_videos->bind_param("i", $school_id);
$stmt_videos->execute();
$videos_result = $stmt_videos->get_result();
$videos_count = mysqli_num_rows($videos_result);

function e($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function is_video_file(?string $path): bool {
    if (!$path) return false;
    return (bool)preg_match('~\.(mp4|avi|mov|mkv|webm)$~i', $path);
}

function normalize_url_https(string $url): string {
    $url = trim($url);
    if ($url === '') return '';
    // Convert http to https for embeds (helps mixed-content blocking)
    if (stripos($url, 'http://') === 0) {
        $url = 'https://' . substr($url, 7);
    }
    return $url;
}

function youtube_embed(string $url): string {
    $url = normalize_url_https($url);
    if ($url === '') return '';

    // Already embed
    if (stripos($url, 'youtube.com/embed/') !== false) return $url;

    // youtu.be/ID
    if (preg_match('~youtu\.be/([a-zA-Z0-9_-]{6,})~', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }

    // youtube.com/watch?v=ID
    if (preg_match('~[?&]v=([a-zA-Z0-9_-]{6,})~', $url, $m) && stripos($url, 'youtube.com') !== false) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }

    // youtube.com/shorts/ID
    if (preg_match('~youtube\.com/shorts/([a-zA-Z0-9_-]{6,})~', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }

    // If not youtube, return as-is (vimeo/others)
    return $url;
}

function detect_kind_and_src(array $row): array {
    $video_url = trim((string)($row['video_url'] ?? ''));
    $file_path = trim((string)($row['file_path'] ?? ''));

    $is_file_video = is_video_file($file_path);

    if ($video_url !== '') {
        $src = youtube_embed($video_url);
        return ['kind' => 'embed', 'src' => $src, 'is_video' => true];
    }

    if ($is_file_video) {
        return ['kind' => 'video', 'src' => $file_path, 'is_video' => true];
    }

    // default image
    $src = $file_path ?: (string)($row['thumbnail'] ?? '');
    return ['kind' => 'image', 'src' => $src, 'is_video' => false];
}
?>

<style>
:root {
    --primary: #1e3a8a;
    --primary-dark: #1e40af;
    --accent: #3b82f6;
    --glass-bg: rgba(255, 255, 255, 0.92);
    --glass-border: rgba(255, 255, 255, 0.25);
    --glass-shadow: 0 8px 32px rgba(31, 38, 135, 0.12);
    --glass-hover: 0 20px 48px rgba(31, 38, 135, 0.20);
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --bg-primary: #f8fafc;
    --bg-secondary: #ffffff;
}

* { box-sizing: border-box; margin: 0; padding: 0; }
body { 
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
    line-height: 1.6; 
    color: var(--text-primary);
    background: var(--bg-primary);
}

.hero-section {
    min-height: 80vh;
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="0.8" fill="rgba(255,255,255,0.08)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    background-blend-mode: overlay;
    background-size: cover, 200px 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.3);
    backdrop-filter: blur(1px);
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 800px;
    padding: 0 2rem;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    color: white;
    padding: 0.5rem 1.25rem;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 600;
    letter-spacing: 0.025em;
    margin-bottom: 2rem;
    border: 1px solid rgba(255,255,255,0.3);
}

.hero-title {
    font-size: clamp(3rem, 8vw, 6rem);
    font-weight: 800;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 1.5rem;
    line-height: 1.1;
}

.hero-subtitle {
    font-size: 1.375rem;
    color: rgba(255,255,255,0.95);
    margin-bottom: 2.5rem;
    font-weight: 400;
}

.hero-cta {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    color: var(--primary);
    padding: 1.125rem 2.5rem;
    border-radius: 16px;
    font-weight: 700;
    font-size: 1.125rem;
    text-decoration: none;
    border: 1px solid var(--glass-border);
    box-shadow: var(--glass-shadow);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hero-cta:hover {
    transform: translateY(-2px);
    box-shadow: var(--glass-hover);
    background: rgba(255,255,255,0.98);
}

.main-content {
    padding: 5rem 0;
    max-width: 1400px;
    margin: 0 auto;
    padding-left: 2rem;
    padding-right: 2rem;
}

.section-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 0.5rem;
}

.section-subtitle {
    color: var(--text-secondary);
    font-size: 1.125rem;
    max-width: 600px;
    margin: 0 auto;
}

.tabs-container {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 3rem;
    flex-wrap: wrap;
}

.tab-button {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    color: var(--text-primary);
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: var(--glass-shadow);
    position: relative;
    overflow: hidden;
}

.tab-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}

.tab-button:hover::before { left: 100%; }
.tab-button:hover { transform: translateY(-1px); box-shadow: var(--glass-hover); }

.tab-button.active {
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: white;
    border-color: transparent;
    box-shadow: 0 12px 40px rgba(30, 58, 138, 0.25);
}

.tab-panel { display: none; }
.tab-panel.active { display: block; }

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.gallery-card {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--glass-shadow);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    height: 420px;
    display: flex;
    flex-direction: column;
}

.gallery-card:hover { transform: translateY(-8px); box-shadow: var(--glass-hover); }

.card-media {
    position: relative;
    flex: 0 0 280px;
    overflow: hidden;
}

.card-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform 0.4s ease;
}

.gallery-card:hover .card-media img { transform: scale(1.05); }

.video-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(30,58,138,0.85), rgba(59,130,246,0.85));
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.25s ease;
}

.video-overlay span {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: rgba(255,255,255,0.95);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 900;
    color: var(--primary);
    box-shadow: 0 8px 24px rgba(0,0,0,0.3);
    backdrop-filter: blur(10px);
}

.gallery-card.video:hover .video-overlay { opacity: 1; }

.card-content {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
    line-height: 1.3;
}

.card-meta {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.card-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: white;
    padding: 0.375rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    align-self: flex-start;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--glass-bg);
    border: 2px dashed rgba(107,114,128,0.3);
    border-radius: 20px;
    color: var(--text-secondary);
}

.empty-icon { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; }

/* MODAL (bigger) */
.modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.92);
    backdrop-filter: blur(12px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    padding: 2rem;
}
.modal-backdrop.active { display: flex; }

.modal-container {
    background: #0b1220;
    border-radius: 20px;
    overflow: hidden;
    width: min(1200px, 96vw);
    max-height: 92vh;
    box-shadow: 0 40px 120px rgba(0,0,0,0.7);
    border: 1px solid rgba(255,255,255,0.08);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.75rem;
    background: rgba(255,255,255,0.04);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

.modal-title { font-size: 1.15rem; font-weight: 800; color: #fff; opacity: 0.95; }

.modal-close {
    background: rgba(255,255,255,0.12);
    border: none;
    color: #fff;
    width: 42px;
    height: 42px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 1.35rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-close:hover { background: rgba(255,255,255,0.2); transform: scale(1.06); }

.modal-body {
    background: #000;
    height: min(76vh, 720px);
}

.modal-body img,
.modal-body video,
.modal-body iframe {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: contain;
    border: 0;
}

.video-wrapper {
    width: 100%;
    height: 100%;
    background: #000;
}

@media (max-width: 1024px) {
    .gallery-grid { grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.25rem; }
    .gallery-card { height: 380px; }
    .card-media { flex: 0 0 250px; }
    .modal-body { height: 68vh; }
}
@media (max-width: 768px) {
    .main-content { padding-left: 1.5rem; padding-right: 1.5rem; padding-top: 3rem; }
    .gallery-grid { grid-template-columns: 1fr; gap: 1rem; }
    .tabs-container { flex-direction: column; align-items: center; }
    .tab-button { width: 280px; text-align: center; }
    .modal-backdrop { padding: 1rem; }
    .modal-body { height: 60vh; }
}
@media (max-width: 480px) {
    .hero-title { font-size: 2.5rem; }
    .section-title { font-size: 2rem; }
    .modal-body { height: 55vh; }
}
</style>

<section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-badge">📸 OLV School Memories</div>
        <h1 class="hero-title">Gallery</h1>
        <p class="hero-subtitle">Professional showcase of our cherished moments, events, and achievements</p>
        <a href="#gallery" class="hero-cta">
            Explore Gallery
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </div>
</section>

<main id="gallery" class="main-content">
    <div class="section-header">
        <h2 class="section-title">Media Gallery</h2>
        <p class="section-subtitle">High-quality images and videos from school events and activities</p>
    </div>

    <div class="tabs-container" role="tablist">
        <button class="tab-button active" data-tab="all" role="tab">📂 All (<?= (int)$all_count ?>)</button>
        <button class="tab-button" data-tab="images" role="tab">🖼️ Images (<?= (int)$images_count ?>)</button>
        <button class="tab-button" data-tab="videos" role="tab">🎥 Videos (<?= (int)$videos_count ?>)</button>
    </div>

    <!-- ALL TAB -->
    <div id="all-tab" class="tab-panel active">
        <?php if ($all_count > 0): ?>
            <div class="gallery-grid">
                <?php while ($row = $all_result->fetch_assoc()): ?>
                    <?php
                        $title = $row['title'] ?? $row['caption'] ?? 'Media Item';
                        $thumb = (string)($row['thumbnail'] ?? '');
                        $d = detect_kind_and_src($row);
                        $kind = $d['kind'];
                        $src  = $d['src'];
                        $is_video = $d['is_video'];
                        if ($src === '') continue;

                        if ($thumb === '') {
                            $thumb = (string)($row['file_path'] ?? '');
                        }

                        $cardClass = $is_video ? 'gallery-card video' : 'gallery-card';
                    ?>
                    <div class="<?= $cardClass ?>"
                         data-modal="<?= e($kind) ?>"
                         data-src="<?= e($src) ?>"
                         data-title="<?= e($title) ?>">
                        <div class="card-media">
                            <?php if ($thumb !== ''): ?>
                                <img src="<?= e($thumb) ?>" alt="<?= e($title) ?>" loading="lazy">
                            <?php else: ?>
                                <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;color:white;font-size:4rem;font-weight:800;">📷</div>
                            <?php endif; ?>
                            <?php if ($is_video): ?>
                                <div class="video-overlay"><span>▶</span></div>
                            <?php endif; ?>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?= e($title) ?></h3>
                            <p class="card-meta"><?= e($row['created_at'] ?? '') ?></p>
                            <?php if (!empty($row['featured'])): ?><span class="card-badge">⭐ Featured</span><?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state"><div class="empty-icon">📂</div><h3>No Media Found</h3><p>Upload your first media item to get started.</p></div>
        <?php endif; ?>
    </div>

    <!-- IMAGES TAB -->
    <div id="images-tab" class="tab-panel">
        <?php if ($images_count > 0): ?>
            <div class="gallery-grid">
                <?php while ($row = $images_result->fetch_assoc()): ?>
                    <?php
                        $title = $row['title'] ?? $row['caption'] ?? 'Gallery Image';
                        $thumb = $row['thumbnail'] ?: $row['file_path'];
                        $full  = $row['file_path'] ?: $row['thumbnail'];
                        if (!$full) continue;
                    ?>
                    <div class="gallery-card" data-modal="image" data-src="<?= e($full) ?>" data-title="<?= e($title) ?>">
                        <div class="card-media"><img src="<?= e($thumb) ?>" alt="<?= e($title) ?>" loading="lazy"></div>
                        <div class="card-content">
                            <h3 class="card-title"><?= e($title) ?></h3>
                            <p class="card-meta"><?= e($row['created_at'] ?? '') ?></p>
                            <?php if (!empty($row['featured'])): ?><span class="card-badge">⭐ Featured</span><?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state"><div class="empty-icon">🖼️</div><h3>No Images Found</h3><p>Images will appear here when uploaded.</p></div>
        <?php endif; ?>
    </div>

    <!-- VIDEOS TAB -->
    <div id="videos-tab" class="tab-panel">
        <?php if ($videos_count > 0): ?>
            <div class="gallery-grid">
                <?php while ($row = $videos_result->fetch_assoc()): ?>
                    <?php
                        $title = $row['title'] ?? $row['caption'] ?? 'School Video';
                        $thumb = (string)($row['thumbnail'] ?? '');
                        $d = detect_kind_and_src($row);
                        $kind = $d['kind'];
                        $src  = $d['src'];
                        if ($src === '' || !$d['is_video']) continue;
                    ?>
                    <div class="gallery-card video" data-modal="<?= e($kind) ?>" data-src="<?= e($src) ?>" data-title="<?= e($title) ?>">
                        <div class="card-media">
                            <?php if ($thumb !== ''): ?>
                                <img src="<?= e($thumb) ?>" alt="<?= e($title) ?>" loading="lazy">
                            <?php else: ?>
                                <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;color:white;font-size:4rem;font-weight:800;">▶</div>
                            <?php endif; ?>
                            <div class="video-overlay"><span>▶</span></div>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?= e($title) ?></h3>
                            <p class="card-meta"><?= e($row['created_at'] ?? '') ?></p>
                            <?php if (!empty($row['featured'])): ?><span class="card-badge">⭐ Featured</span><?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state"><div class="empty-icon">🎥</div><h3>No Videos Found</h3><p>Videos will appear here when uploaded.</p></div>
        <?php endif; ?>
    </div>
</main>

<!-- MODAL -->
<div class="modal-backdrop" id="mediaModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Media Preview</h3>
            <button class="modal-close" id="modalClose" type="button">×</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
    </div>
</div>

<script>
(() => {
    document.querySelector('.hero-cta')?.addEventListener('click', e => {
        e.preventDefault();
        document.getElementById('gallery').scrollIntoView({ behavior: 'smooth' });
    });

    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanels  = document.querySelectorAll('.tab-panel');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.tab;
            tabButtons.forEach(b => b.classList.remove('active'));
            tabPanels.forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(`${target}-tab`).classList.add('active');
        });
    });

    const modal      = document.getElementById('mediaModal');
    const modalBody  = document.getElementById('modalBody');
    const modalTitle = document.getElementById('modalTitle');
    const modalClose = document.getElementById('modalClose');

    function openModal(card) {
        const kind  = card.dataset.modal;
        const src   = card.dataset.src;
        const title = card.dataset.title;

        modalTitle.textContent = title || 'Media Preview';
        modalBody.innerHTML = '';

        if (!src) return;

        if (kind === 'image') {
            const img = document.createElement('img');
            img.src = src;
            img.alt = title || 'Image';
            modalBody.appendChild(img);
        } else if (kind === 'video') {
            const video = document.createElement('video');
            video.controls = true;
            video.playsInline = true;
            video.preload = 'metadata';
            video.style.width = '100%';
            video.style.height = '100%';

            const source = document.createElement('source');
            source.src = src;
            // Let browser decide if not mp4, still ok
            if (src.toLowerCase().endsWith('.mp4')) source.type = 'video/mp4';
            if (src.toLowerCase().endsWith('.webm')) source.type = 'video/webm';
            video.appendChild(source);

            modalBody.appendChild(video);
            video.play().catch(() => {});
        } else {
            // embed
            const wrapper = document.createElement('div');
            wrapper.className = 'video-wrapper';

            const iframe = document.createElement('iframe');
            iframe.src = src;
            iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
            iframe.allowFullscreen = true;

            wrapper.appendChild(iframe);
            modalBody.appendChild(wrapper);
        }

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        modalBody.innerHTML = '';
    }

    document.querySelectorAll('.gallery-card').forEach(card => {
        card.addEventListener('click', () => openModal(card));
    });

    modalClose.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
})();
</script>

<?php
$stmt_all->close();
$stmt_images->close();
$stmt_videos->close();
mysqli_close($conn);

if (file_exists(__DIR__ . '/footer1.php')) {
    include(__DIR__ . '/footer1.php');
}
?>
