<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$current_page = basename($_SERVER['PHP_SELF']);
$page_title = "Gallery - College";
include(__DIR__ . '/header3.php');

// DB Connection
$host     = 'localhost';
$username = 'sai7755_college';
$password = 'Admin_66666';
$database = 'sai7755_college';

$conn = mysqli_connect($host, $username, $password, $database);
if (!$conn) die('DB Connection Failed: ' . mysqli_connect_error());
mysqli_set_charset($conn, 'utf8mb4');

$school_id = (int)($_GET['school_id'] ?? 3);

// Query 1: ALL MEDIA
$stmt_all = $conn->prepare("
    SELECT * FROM `mum_gallery`
    WHERE school_id = ?
      AND status = 'active'
      AND (file_path != '' OR video_url IS NOT NULL AND video_url != '')
    ORDER BY featured DESC, display_order DESC, created_at DESC
    LIMIT 72
");
$stmt_all->bind_param("i", $school_id);
$stmt_all->execute();
$all_result = $stmt_all->get_result();
$all_count  = mysqli_num_rows($all_result);

// Query 2: Images Only
$stmt_images = $conn->prepare("
    SELECT * FROM `mum_gallery`
    WHERE school_id = ?
      AND status = 'active'
      AND media_type = 'image'
    ORDER BY featured DESC, display_order DESC, created_at DESC
    LIMIT 36
");
$stmt_images->bind_param("i", $school_id);
$stmt_images->execute();
$images_result = $stmt_images->get_result();
$images_count  = mysqli_num_rows($images_result);

// Query 3: Videos Only
$stmt_videos = $conn->prepare("
    SELECT * FROM `mum_gallery`
    WHERE school_id = ?
      AND status = 'active'
      AND media_type = 'video'
    ORDER BY featured DESC, display_order DESC, created_at DESC
    LIMIT 36
");
$stmt_videos->bind_param("i", $school_id);
$stmt_videos->execute();
$videos_result = $stmt_videos->get_result();
$videos_count  = mysqli_num_rows($videos_result);

// ─── Helper Functions ───────────────────────────────────────────────────────
function e($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function is_video_file(?string $path): bool {
    if (!$path) return false;
    return (bool)preg_match('~\.(mp4|avi|mov|mkv|webm)$~i', $path);
}

function normalize_https(string $url): string {
    $url = trim($url);
    if (stripos($url, 'http://') === 0) {
        $url = 'https://' . substr($url, 7);
    }
    return $url;
}

function youtube_embed(string $url): string {
    $url = normalize_https($url);
    if ($url === '') return '';
    if (stripos($url, 'youtube.com/embed/') !== false) return $url;
    if (preg_match('~youtu\.be/([a-zA-Z0-9_-]{6,})~', $url, $m))
        return 'https://www.youtube.com/embed/' . $m[1];
    if (preg_match('~[?&]v=([a-zA-Z0-9_-]{6,})~', $url, $m) && stripos($url, 'youtube.com') !== false)
        return 'https://www.youtube.com/embed/' . $m[1];
    if (preg_match('~youtube\.com/shorts/([a-zA-Z0-9_-]{6,})~', $url, $m))
        return 'https://www.youtube.com/embed/' . $m[1];
    return $url;
}

function youtube_thumb(string $url): string {
    if (preg_match('~youtu\.be/([a-zA-Z0-9_-]{6,})~', $url, $m))
        return 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg';
    if (preg_match('~[?&]v=([a-zA-Z0-9_-]{6,})~', $url, $m))
        return 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg';
    if (preg_match('~youtube\.com/shorts/([a-zA-Z0-9_-]{6,})~', $url, $m))
        return 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg';
    if (preg_match('~youtube\.com/embed/([a-zA-Z0-9_-]{6,})~', $url, $m))
        return 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg';
    return '';
}

function detect_kind_and_src(array $row): array {
    $video_url = trim((string)($row['video_url'] ?? ''));
    $file_path = trim((string)($row['file_path'] ?? ''));
    $media_type = $row['media_type'] ?? 'image';

    if ($video_url !== '') {
        return ['kind' => 'embed', 'src' => youtube_embed($video_url), 'is_video' => true];
    }
    if ($media_type === 'video' || is_video_file($file_path)) {
        return ['kind' => 'video', 'src' => $file_path, 'is_video' => true];
    }
    $src = $file_path ?: (string)($row['thumbnail'] ?? '');
    return ['kind' => 'image', 'src' => $src, 'is_video' => false];
}

function format_size(?int $bytes): string {
    if (!$bytes) return '';
    if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024)    return round($bytes / 1024, 0) . ' KB';
    return $bytes . ' B';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title) ?></title>
<style>
/* ── Reset & Variables ───────────────────────────────────────── */
:root {
    --primary:      #1e3a8a;
    --primary-dark: #1e40af;
    --accent:       #3b82f6;
    --accent-light: #93c5fd;
    --glass-bg:     rgba(255,255,255,0.92);
    --glass-border: rgba(255,255,255,0.25);
    --glass-shadow: 0 8px 32px rgba(31,38,135,0.12);
    --glass-hover:  0 20px 48px rgba(31,38,135,0.22);
    --text-primary: #1f2937;
    --text-secondary:#6b7280;
    --bg-primary:   #f0f4f8;
    --radius-lg:    20px;
    --radius-md:    12px;
}
*{box-sizing:border-box;margin:0;padding:0;}
body{
    font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;
    line-height:1.6;
    color:var(--text-primary);
    background:var(--bg-primary);
}

/* ── Hero ────────────────────────────────────────────────────── */
.hero-section{
    min-height:78vh;
    background:linear-gradient(135deg,var(--primary) 0%,var(--accent) 100%);
    display:flex;align-items:center;justify-content:center;
    text-align:center;position:relative;overflow:hidden;
}
.hero-particles{
    position:absolute;inset:0;overflow:hidden;pointer-events:none;
}
.hero-particles span{
    position:absolute;border-radius:50%;
    background:rgba(255,255,255,0.08);
    animation:float linear infinite;
}
@keyframes float{
    0%  {transform:translateY(100vh) scale(0);}
    100%{transform:translateY(-100px) scale(1);}
}
.hero-overlay{
    position:absolute;inset:0;
    background:rgba(0,0,0,0.28);
    backdrop-filter:blur(1px);
}
.hero-content{
    position:relative;z-index:2;
    max-width:820px;padding:0 2rem;
}
.hero-badge{
    display:inline-flex;align-items:center;gap:.5rem;
    background:rgba(255,255,255,0.18);backdrop-filter:blur(10px);
    color:#fff;padding:.5rem 1.25rem;border-radius:50px;
    font-size:.875rem;font-weight:600;letter-spacing:.03em;
    margin-bottom:1.75rem;border:1px solid rgba(255,255,255,0.3);
}
.hero-title{
    font-size:clamp(3rem,8vw,6rem);font-weight:800;
    color:#fff;margin-bottom:1rem;line-height:1.1;
    text-shadow:0 4px 24px rgba(0,0,0,0.25);
}
.hero-title span{
    background:linear-gradient(90deg,#fff 0%,var(--accent-light) 100%);
    -webkit-background-clip:text;background-clip:text;
    -webkit-text-fill-color:transparent;
}
.hero-subtitle{
    font-size:1.25rem;color:rgba(255,255,255,0.9);
    margin-bottom:2.5rem;font-weight:400;
}
.hero-stats{
    display:flex;gap:2rem;justify-content:center;flex-wrap:wrap;
    margin-bottom:2.5rem;
}
.stat-pill{
    background:rgba(255,255,255,0.15);backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,0.25);
    color:#fff;padding:.6rem 1.5rem;border-radius:50px;
    font-weight:700;font-size:1rem;
}
.stat-pill small{display:block;font-size:.7rem;font-weight:400;opacity:.85;}
.hero-cta{
    display:inline-flex;align-items:center;gap:.75rem;
    background:rgba(255,255,255,0.95);backdrop-filter:blur(20px);
    color:var(--primary);padding:1rem 2.25rem;
    border-radius:var(--radius-md);font-weight:700;font-size:1.1rem;
    text-decoration:none;border:1px solid var(--glass-border);
    box-shadow:var(--glass-shadow);transition:all .3s ease;
}
.hero-cta:hover{transform:translateY(-3px);box-shadow:var(--glass-hover);background:#fff;}

/* ── Main ────────────────────────────────────────────────────── */
.main-content{
    max-width:1400px;margin:0 auto;
    padding:5rem 2rem;
}
.section-header{text-align:center;margin-bottom:3rem;}
.section-title{
    font-size:2.5rem;font-weight:800;
    background:linear-gradient(135deg,var(--primary),var(--accent));
    -webkit-background-clip:text;background-clip:text;
    -webkit-text-fill-color:transparent;margin-bottom:.5rem;
}
.section-subtitle{color:var(--text-secondary);font-size:1.1rem;max-width:600px;margin:0 auto;}

/* ── Tabs ────────────────────────────────────────────────────── */
.tabs-container{
    display:flex;justify-content:center;gap:1rem;
    margin-bottom:2.5rem;flex-wrap:wrap;
}
.tab-button{
    background:var(--glass-bg);backdrop-filter:blur(20px);
    border:1px solid var(--glass-border);color:var(--text-primary);
    padding:.875rem 1.75rem;border-radius:var(--radius-md);
    font-weight:700;font-size:.95rem;cursor:pointer;
    transition:all .25s ease;box-shadow:var(--glass-shadow);
}
.tab-button:hover{transform:translateY(-2px);box-shadow:var(--glass-hover);}
.tab-button.active{
    background:linear-gradient(135deg,var(--primary),var(--accent));
    color:#fff;border-color:transparent;
    box-shadow:0 12px 40px rgba(30,58,138,0.28);
}
.tab-panel{display:none;}
.tab-panel.active{display:block;animation:fadeIn .3s ease;}
@keyframes fadeIn{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:none;}}

/* ── Category Filter ────────────────────────────────────────── */
.filter-bar{
    display:flex;gap:.75rem;flex-wrap:wrap;
    justify-content:center;margin-bottom:2rem;
}
.filter-chip{
    background:rgba(255,255,255,0.7);border:1px solid rgba(0,0,0,0.1);
    padding:.4rem 1rem;border-radius:50px;font-size:.82rem;
    font-weight:600;cursor:pointer;transition:all .2s ease;
    color:var(--text-secondary);
}
.filter-chip:hover,.filter-chip.active{
    background:var(--primary);color:#fff;border-color:var(--primary);
}

/* ── Grid ────────────────────────────────────────────────────── */
.gallery-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(340px,1fr));
    gap:1.5rem;
}

/* ── Card ────────────────────────────────────────────────────── */
.gallery-card{
    background:var(--glass-bg);backdrop-filter:blur(20px);
    border:1px solid var(--glass-border);border-radius:var(--radius-lg);
    overflow:hidden;box-shadow:var(--glass-shadow);
    transition:all .3s cubic-bezier(.4,0,.2,1);
    cursor:pointer;display:flex;flex-direction:column;
}
.gallery-card:hover{transform:translateY(-8px);box-shadow:var(--glass-hover);}

.card-media{
    position:relative;width:100%;height:220px;overflow:hidden;flex-shrink:0;
}
.card-media img{
    width:100%;height:100%;object-fit:cover;object-position:center;
    transition:transform .4s ease;display:block;
}
.gallery-card:hover .card-media img{transform:scale(1.07);}

.card-media .no-thumb{
    width:100%;height:100%;
    background:linear-gradient(135deg,var(--primary),var(--accent));
    display:flex;align-items:center;justify-content:center;
    font-size:3.5rem;color:#fff;
}

.video-badge{
    position:absolute;top:.75rem;left:.75rem;
    background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);
    color:#fff;padding:.3rem .75rem;border-radius:50px;
    font-size:.72rem;font-weight:700;letter-spacing:.05em;
}
.featured-badge{
    position:absolute;top:.75rem;right:.75rem;
    background:linear-gradient(135deg,#f59e0b,#f97316);
    color:#fff;padding:.3rem .75rem;border-radius:50px;
    font-size:.72rem;font-weight:700;
}

.play-overlay{
    position:absolute;inset:0;
    background:linear-gradient(135deg,rgba(30,58,138,.8),rgba(59,130,246,.8));
    display:flex;align-items:center;justify-content:center;
    opacity:0;transition:opacity .25s ease;
}
.play-overlay .play-btn{
    width:64px;height:64px;border-radius:50%;
    background:rgba(255,255,255,.95);
    display:flex;align-items:center;justify-content:center;
    font-size:1.4rem;color:var(--primary);
    box-shadow:0 8px 24px rgba(0,0,0,.3);
}
.gallery-card:hover .play-overlay{opacity:1;}

.card-content{
    padding:1.25rem 1.5rem;flex:1;
    display:flex;flex-direction:column;gap:.4rem;
}
.card-title{
    font-size:1rem;font-weight:800;
    color:var(--text-primary);line-height:1.3;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
}
.card-meta{
    display:flex;gap:.75rem;align-items:center;
    color:var(--text-secondary);font-size:.8rem;flex-wrap:wrap;
}
.card-meta .dot{opacity:.4;}
.card-cat{
    display:inline-block;background:rgba(30,58,138,.1);
    color:var(--primary);padding:.2rem .65rem;border-radius:50px;
    font-size:.72rem;font-weight:700;margin-top:auto;align-self:flex-start;
}

/* ── Empty State ─────────────────────────────────────────────── */
.empty-state{
    text-align:center;padding:4rem 2rem;
    background:var(--glass-bg);border:2px dashed rgba(107,114,128,.25);
    border-radius:var(--radius-lg);color:var(--text-secondary);
}
.empty-icon{font-size:4rem;margin-bottom:1rem;opacity:.45;}

/* ── Modal ───────────────────────────────────────────────────── */
.modal-backdrop{
    position:fixed;inset:0;
    background:rgba(0,0,0,0.93);backdrop-filter:blur(14px);
    display:none;align-items:center;justify-content:center;
    z-index:10000;padding:1.5rem;
}
.modal-backdrop.active{display:flex;}

.modal-container{
    background:#090f1a;border-radius:var(--radius-lg);overflow:hidden;
    width:min(1200px,96vw);max-height:92vh;
    box-shadow:0 40px 120px rgba(0,0,0,.75);
    border:1px solid rgba(255,255,255,.07);
    display:flex;flex-direction:column;
}
.modal-header{
    display:flex;justify-content:space-between;align-items:center;
    padding:1.1rem 1.6rem;
    background:rgba(255,255,255,.04);
    border-bottom:1px solid rgba(255,255,255,.07);
    flex-shrink:0;
}
.modal-title{font-size:1.05rem;font-weight:800;color:#fff;opacity:.95;}
.modal-meta-row{
    display:flex;gap:1rem;flex-wrap:wrap;
    padding:.65rem 1.6rem;
    background:rgba(255,255,255,.02);
    border-bottom:1px solid rgba(255,255,255,.05);
    flex-shrink:0;
}
.modal-meta-row span{color:rgba(255,255,255,.55);font-size:.78rem;}
.modal-meta-row span strong{color:rgba(255,255,255,.85);font-weight:600;}

.modal-close{
    background:rgba(255,255,255,.1);border:none;color:#fff;
    width:38px;height:38px;border-radius:8px;cursor:pointer;
    font-size:1.3rem;font-weight:700;
    display:flex;align-items:center;justify-content:center;
    transition:all .2s ease;flex-shrink:0;
}
.modal-close:hover{background:rgba(255,255,255,.2);transform:scale(1.06);}

.modal-body{
    background:#000;flex:1;overflow:hidden;
    min-height:300px;
}
.modal-body img,
.modal-body video,
.modal-body iframe{
    width:100%;height:100%;display:block;object-fit:contain;border:0;
}
.modal-body .video-wrapper{width:100%;height:100%;background:#000;}

/* ── Responsive ──────────────────────────────────────────────── */
@media(max-width:1024px){
    .gallery-grid{grid-template-columns:repeat(auto-fill,minmax(300px,1fr));}
    .card-media{height:200px;}
}
@media(max-width:768px){
    .main-content{padding:3rem 1.25rem;}
    .gallery-grid{grid-template-columns:1fr;gap:1rem;}
    .tabs-container{flex-direction:column;align-items:center;}
    .tab-button{width:260px;text-align:center;}
    .hero-stats{gap:1rem;}
    .modal-backdrop{padding:.75rem;}
    .modal-body{min-height:250px;height:58vh;}
    .card-media{height:200px;}
}
@media(max-width:480px){
    .hero-title{font-size:2.5rem;}
    .section-title{font-size:1.9rem;}
    .card-media{height:180px;}
}
</style>

<!-- Hero -->
<section class="hero-section">
    <!-- Floating particles -->
    <div class="hero-particles" id="particles"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-badge">📸 College Media Gallery</div>
        <h1 class="hero-title">Our <span>Gallery</span></h1>
        <p class="hero-subtitle">Relive the moments — events, achievements & campus life</p>
        <div class="hero-stats">
            <div class="stat-pill">
                <?= (int)$all_count ?>
                <small>Total Media</small>
            </div>
            <div class="stat-pill">
                <?= (int)$images_count ?>
                <small>Images</small>
            </div>
            <div class="stat-pill">
                <?= (int)$videos_count ?>
                <small>Videos</small>
            </div>
        </div>
        <a href="#gallery" class="hero-cta" id="scrollBtn">
            Explore Gallery
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </div>
</section>

<!-- Main Gallery -->
<main id="gallery" class="main-content">
    <div class="section-header">
        <h2 class="section-title">Media Gallery</h2>
        <p class="section-subtitle">High-quality images and videos from college events, seminars & activities</p>
    </div>

    <!-- Tabs -->
    <div class="tabs-container" role="tablist">
        <button class="tab-button active" data-tab="all"    role="tab">📂 All (<?= (int)$all_count ?>)</button>
        <button class="tab-button"        data-tab="images" role="tab">🖼️ Images (<?= (int)$images_count ?>)</button>
        <button class="tab-button"        data-tab="videos" role="tab">🎥 Videos (<?= (int)$videos_count ?>)</button>
    </div>

    <!-- ── ALL TAB ── -->
    <div id="all-tab" class="tab-panel active">
        <?php if ($all_count > 0): ?>
            <div class="gallery-grid" id="all-grid">
                <?php while ($row = $all_result->fetch_assoc()): ?>
                    <?php
                        $title    = trim((string)($row['title'] ?? $row['description'] ?? 'Media Item'));
                        $thumb    = trim((string)($row['thumbnail'] ?? $row['file_path'] ?? ''));
                        $cat      = trim((string)($row['category'] ?? ''));
                        $dur      = trim((string)($row['duration'] ?? ''));
                        $fsize    = format_size((int)($row['file_size'] ?? 0));
                        $views    = (int)($row['views'] ?? 0);
                        $date     = $row['created_at'] ?? '';
                        $featured = !empty($row['featured']);
                        $d        = detect_kind_and_src($row);
                        $kind     = $d['kind'];
                        $src      = $d['src'];
                        $is_video = $d['is_video'];
                        if ($src === '') continue;

                        // Auto YouTube thumbnail if thumb missing
                        if ($thumb === '' && !empty($row['video_url'])) {
                            $thumb = youtube_thumb((string)$row['video_url']);
                        }

                        // Build extra data attrs for modal
                        $extra_json = json_encode([
                            'cat'   => $cat,
                            'dur'   => $dur,
                            'size'  => $fsize,
                            'views' => $views,
                            'date'  => $date,
                            'desc'  => trim((string)($row['description'] ?? '')),
                        ]);
                    ?>
                    <div class="gallery-card <?= $is_video ? 'video' : 'image' ?>"
                         data-modal="<?= e($kind) ?>"
                         data-src="<?= e($src) ?>"
                         data-title="<?= e($title) ?>"
                         data-extra="<?= e($extra_json) ?>"
                         data-cat="<?= e($cat) ?>">
                        <div class="card-media">
                            <?php if ($thumb !== ''): ?>
                                <img src="<?= e($thumb) ?>" alt="<?= e($title) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="no-thumb"><?= $is_video ? '▶' : '📷' ?></div>
                            <?php endif; ?>
                            <?php if ($is_video): ?>
                                <div class="video-badge">VIDEO<?= $dur ? ' · '.$dur : '' ?></div>
                                <div class="play-overlay"><div class="play-btn">▶</div></div>
                            <?php endif; ?>
                            <?php if ($featured): ?><div class="featured-badge">⭐ Featured</div><?php endif; ?>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?= e($title) ?></h3>
                            <div class="card-meta">
                                <?= $date ? date('d M Y', strtotime($date)) : '' ?>
                                <?php if ($views): ?><span class="dot">•</span><?= number_format($views) ?> views<?php endif; ?>
                            </div>
                            <?php if ($cat): ?><span class="card-cat"><?= e($cat) ?></span><?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state"><div class="empty-icon">📂</div><h3>No Media Found</h3><p>Upload media to get started.</p></div>
        <?php endif; ?>
    </div>

    <!-- ── IMAGES TAB ── -->
    <div id="images-tab" class="tab-panel">
        <?php if ($images_count > 0): ?>
            <div class="gallery-grid">
                <?php while ($row = $images_result->fetch_assoc()): ?>
                    <?php
                        $title    = trim((string)($row['title'] ?? 'Gallery Image'));
                        $thumb    = $row['thumbnail'] ?: $row['file_path'];
                        $full     = $row['file_path'] ?: $row['thumbnail'];
                        $cat      = (string)($row['category'] ?? '');
                        $date     = $row['created_at'] ?? '';
                        $featured = !empty($row['featured']);
                        $views    = (int)($row['views'] ?? 0);
                        $extra_json = json_encode([
                            'cat'=>$cat,'dur'=>'','size'=>format_size((int)($row['file_size']??0)),
                            'views'=>$views,'date'=>$date,'desc'=>(string)($row['description']??'')
                        ]);
                        if (!$full) continue;
                    ?>
                    <div class="gallery-card image"
                         data-modal="image"
                         data-src="<?= e($full) ?>"
                         data-title="<?= e($title) ?>"
                         data-extra="<?= e($extra_json) ?>">
                        <div class="card-media">
                            <img src="<?= e($thumb) ?>" alt="<?= e($title) ?>" loading="lazy">
                            <?php if ($featured): ?><div class="featured-badge">⭐ Featured</div><?php endif; ?>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?= e($title) ?></h3>
                            <div class="card-meta">
                                <?= $date ? date('d M Y', strtotime($date)) : '' ?>
                                <?php if ($views): ?><span class="dot">•</span><?= number_format($views) ?> views<?php endif; ?>
                            </div>
                            <?php if ($cat): ?><span class="card-cat"><?= e($cat) ?></span><?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state"><div class="empty-icon">🖼️</div><h3>No Images Found</h3><p>Images will appear here when uploaded.</p></div>
        <?php endif; ?>
    </div>

    <!-- ── VIDEOS TAB ── -->
    <div id="videos-tab" class="tab-panel">
        <?php if ($videos_count > 0): ?>
            <div class="gallery-grid">
                <?php while ($row = $videos_result->fetch_assoc()): ?>
                    <?php
                        $title    = trim((string)($row['title'] ?? 'College Video'));
                        $thumb    = (string)($row['thumbnail'] ?? '');
                        $cat      = (string)($row['category'] ?? '');
                        $dur      = (string)($row['duration'] ?? '');
                        $date     = $row['created_at'] ?? '';
                        $featured = !empty($row['featured']);
                        $views    = (int)($row['views'] ?? 0);
                        $d        = detect_kind_and_src($row);
                        $kind     = $d['kind'];
                        $src      = $d['src'];
                        // Auto YouTube thumbnail if thumb missing
                        if ($thumb === '' && !empty($row['video_url'])) {
                            $thumb = youtube_thumb((string)$row['video_url']);
                        }
                        $extra_json = json_encode([
                            'cat'=>$cat,'dur'=>$dur,'size'=>format_size((int)($row['file_size']??0)),
                            'views'=>$views,'date'=>$date,'desc'=>(string)($row['description']??'')
                        ]);
                        if ($src === '' || !$d['is_video']) continue;
                    ?>
                    <div class="gallery-card video"
                         data-modal="<?= e($kind) ?>"
                         data-src="<?= e($src) ?>"
                         data-title="<?= e($title) ?>"
                         data-extra="<?= e($extra_json) ?>">
                        <div class="card-media">
                            <?php if ($thumb !== ''): ?>
                                <img src="<?= e($thumb) ?>" alt="<?= e($title) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="no-thumb">▶</div>
                            <?php endif; ?>
                            <div class="video-badge">VIDEO<?= $dur ? ' · '.$dur : '' ?></div>
                            <div class="play-overlay"><div class="play-btn">▶</div></div>
                            <?php if ($featured): ?><div class="featured-badge">⭐ Featured</div><?php endif; ?>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?= e($title) ?></h3>
                            <div class="card-meta">
                                <?= $date ? date('d M Y', strtotime($date)) : '' ?>
                                <?php if ($views): ?><span class="dot">•</span><?= number_format($views) ?> views<?php endif; ?>
                            </div>
                            <?php if ($cat): ?><span class="card-cat"><?= e($cat) ?></span><?php endif; ?>
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
<div class="modal-backdrop" id="mediaModal" role="dialog" aria-modal="true">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Media Preview</h3>
            <button class="modal-close" id="modalClose" type="button" aria-label="Close">×</button>
        </div>
        <div class="modal-meta-row" id="modalMeta"></div>
        <div class="modal-body" id="modalBody"></div>
    </div>
</div>

<script>
(() => {
    /* ── Particles ── */
    const pc = document.getElementById('particles');
    if (pc) {
        for (let i = 0; i < 12; i++) {
            const s = document.createElement('span');
            const sz = Math.random() * 80 + 20;
            s.style.cssText = `
                width:${sz}px;height:${sz}px;
                left:${Math.random()*100}%;
                bottom:-${sz}px;
                animation-duration:${8+Math.random()*10}s;
                animation-delay:${Math.random()*6}s;
            `;
            pc.appendChild(s);
        }
    }

    /* ── Smooth scroll ── */
    document.getElementById('scrollBtn')?.addEventListener('click', e => {
        e.preventDefault();
        document.getElementById('gallery')?.scrollIntoView({behavior:'smooth'});
    });

    /* ── Tabs ── */
    const tabBtns   = document.querySelectorAll('.tab-button');
    const tabPanels = document.querySelectorAll('.tab-panel');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const t = btn.dataset.tab;
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanels.forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(`${t}-tab`)?.classList.add('active');
        });
    });

    /* ── Modal ── */
    const modal      = document.getElementById('mediaModal');
    const modalBody  = document.getElementById('modalBody');
    const modalTitle = document.getElementById('modalTitle');
    const modalMeta  = document.getElementById('modalMeta');
    const modalClose = document.getElementById('modalClose');

    function openModal(card) {
        const kind  = card.dataset.modal;
        const src   = card.dataset.src;
        const title = card.dataset.title || 'Media Preview';
        let   extra = {};
        try { extra = JSON.parse(card.dataset.extra || '{}'); } catch(e){}

        modalTitle.textContent = title;
        modalBody.innerHTML    = '';

        /* meta bar */
        const parts = [];
        if (extra.date) parts.push(`<span><strong>Date:</strong> ${new Date(extra.date).toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'})}</span>`);
        if (extra.cat)  parts.push(`<span><strong>Category:</strong> ${extra.cat}</span>`);
        if (extra.dur)  parts.push(`<span><strong>Duration:</strong> ${extra.dur}</span>`);
        if (extra.size) parts.push(`<span><strong>Size:</strong> ${extra.size}</span>`);
        if (extra.views)parts.push(`<span><strong>Views:</strong> ${Number(extra.views).toLocaleString()}</span>`);
        modalMeta.innerHTML = parts.join('');
        modalMeta.style.display = parts.length ? '' : 'none';

        if (!src) return;

        if (kind === 'image') {
            const img   = document.createElement('img');
            img.src     = src;
            img.alt     = title;
            modalBody.style.height = 'min(76vh, 720px)';
            modalBody.appendChild(img);
        } else if (kind === 'video') {
            const video = document.createElement('video');
            video.controls    = true;
            video.playsInline = true;
            video.preload     = 'metadata';
            video.style.cssText = 'width:100%;height:100%;';
            const source = document.createElement('source');
            source.src  = src;
            if (/\.mp4$/i.test(src))  source.type = 'video/mp4';
            if (/\.webm$/i.test(src)) source.type = 'video/webm';
            if (/\.mov$/i.test(src))  source.type = 'video/quicktime';
            video.appendChild(source);
            modalBody.style.height = 'min(76vh, 720px)';
            modalBody.appendChild(video);
            video.play().catch(()=>{});
        } else {
            /* embed (youtube/vimeo) */
            const wrap   = document.createElement('div');
            wrap.className = 'video-wrapper';
            const iframe = document.createElement('iframe');
            // append autoplay
            const sep = src.includes('?') ? '&' : '?';
            iframe.src = src + sep + 'autoplay=1';
            iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
            iframe.allowFullscreen = true;
            wrap.appendChild(iframe);
            modalBody.style.height = 'min(76vh, 720px)';
            modalBody.appendChild(wrap);
        }

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        modalBody.innerHTML = '';
    }

    document.querySelectorAll('.gallery-card').forEach(c => {
        c.addEventListener('click', () => openModal(c));
        c.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') openModal(c); });
        c.setAttribute('tabindex','0');
        c.setAttribute('role','button');
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

$footer = __DIR__ . '/footer3.php';
if (file_exists($footer)) include($footer);
?>
