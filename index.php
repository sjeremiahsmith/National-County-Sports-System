<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$dbv = Database::getInstance();
$videos = $dbv->fetchAll("SELECT * FROM videos ORDER BY sort_order ASC, created_at DESC LIMIT 6");
$homePhotos = $dbv->fetchAll("SELECT * FROM gallery_photos ORDER BY id DESC LIMIT 8");

$pageTitle = 'Home';
include __DIR__ . '/templates/public_header.php';
?>

<!-- Hero Section -->
<section class="hero-section" id="home">
    <div class="hero-bg"></div>
    <div class="hero-particles" id="particles"></div>
    <div class="hero-content">
        <span class="hero-badge">Ministry of Youth & Sports &mdash; Republic of Liberia</span>
        <h1>National County<br><span class="highlight">Sports Meet</span></h1>
        <p>Uniting 15 counties across 4 groups through the power of sports. Football, Kickball, Basketball, and Athletics &mdash; celebrating athletic excellence nationwide.</p>
        <div class="hero-buttons">
            <a href="<?= APP_URL ?>pages/about.php" class="btn btn-primary-custom">
                <i class="bi bi-trophy me-2"></i>Learn About NCSM
            </a>
            <a href="https://www.facebook.com/moysliberia" target="_blank" rel="noopener noreferrer" class="btn btn-primary-custom">
                <i class="bi bi-facebook me-2"></i>Follow MOYS on Facebook
            </a>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="stat-num">15</div>
                <div class="stat-text">Counties</div>
            </div>
            <div class="hero-stat">
                <div class="stat-num">4</div>
                <div class="stat-text">Sports</div>
            </div>
            <div class="hero-stat">
                <div class="stat-num">4</div>
                <div class="stat-text">Groups</div>
            </div>
            <div class="hero-stat">
                <div class="stat-num">2026</div>
                <div class="stat-text">Season</div>
            </div>
        </div>
    </div>
</section>

<!-- Sports Disciplines -->
<section class="section" id="sports">
    <div class="container">
        <div class="section-header">
            <span class="overline">Our Sports</span>
            <h2>Sports Disciplines</h2>
            <p>Four competitive disciplines bring together athletes from across all 15 counties of Liberia.</p>
        </div>
        <div class="row g-3 g-md-4">
            <div class="col-6 col-lg-3">
                <div class="sport-card sport-football">
                    <div class="sport-overlay">
                        <div style="font-size:4rem;margin-bottom:0.5rem;">⚽</div>
                        <h5>Football</h5>
                        <small>Liberia Football Association (LFA)</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="sport-card sport-kickball">
                    <div class="sport-overlay">
                        <div style="font-size:4rem;margin-bottom:0.5rem;">🥎</div>
                        <h5>Kickball</h5>
                        <small>Liberia Kickball Association (LKA)</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="sport-card sport-basketball">
                    <div class="sport-overlay">
                        <div style="font-size:4rem;margin-bottom:0.5rem;">🏀</div>
                        <h5>Basketball</h5>
                        <small>Liberia Basketball Association (LBA)</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="sport-card sport-athletics">
                    <div class="sport-overlay">
                        <div style="font-size:4rem;margin-bottom:0.5rem;">🏃</div>
                        <h5>Athletics</h5>
                        <small>Liberia Athletics Association (LAA)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="section section-dark">
    <div class="container">
        <div class="section-header">
            <span class="overline">Portal Features</span>
            <h2>Built for Sports Management</h2>
            <p>A comprehensive digital platform designed to streamline the National County Sports Meet operations.</p>
        </div>
        <div class="row g-3 g-md-4">
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-person-plus"></i>
                    </div>
                    <h5>Player Registration</h5>
                    <p>Register players from all 15 counties with complete profiles, photos, and documentation.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <h5>Approval Workflow</h5>
                    <p>Streamlined review process where Association Admins verify and approve player registrations.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-broadcast"></i>
                    </div>
                    <h5>Live Scores</h5>
                    <p>Real-time match scores and results with auto-refreshing live updates every 30 seconds.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-trophy"></i>
                    </div>
                    <h5>League Standings</h5>
                    <p>Auto-calculated standings with points, goal difference, and win/draw/loss records per group.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-clipboard-data"></i>
                    </div>
                    <h5>Reports & Analytics</h5>
                    <p>Comprehensive reports by county, sport, and group with CSV export capabilities.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5>Role-Based Access</h5>
                    <p>Secure system with Super Admin, County Coordinator, and Association Admin roles.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- County Groups -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="overline">County Groups</span>
            <h2>15 Counties, 4 Groups</h2>
            <p>All 15 Liberian counties are organized into four competitive groups for the sports meet.</p>
        </div>
        <div class="row g-3 g-md-4">
            <?php
            $groups = [
                'A' => ['color' => '#dc3545', 'counties' => [
                    ['name' => 'Nimba', 'host' => false],
                    ['name' => 'Grand Gedeh', 'host' => true],
                    ['name' => 'River Gee', 'host' => false],
                    ['name' => 'Gbarpolu', 'host' => false],
                ]],
                'B' => ['color' => '#0d6efd', 'counties' => [
                    ['name' => 'Grand Cape Mount', 'host' => false],
                    ['name' => 'Bong', 'host' => true],
                    ['name' => 'Maryland', 'host' => false],
                    ['name' => 'River Cess', 'host' => false],
                ]],
                'C' => ['color' => '#C8A032', 'counties' => [
                    ['name' => 'Grand Bassa', 'host' => false],
                    ['name' => 'Lofa', 'host' => true],
                    ['name' => 'Montserrado', 'host' => false],
                    ['name' => 'Sinoe', 'host' => false],
                ]],
                'D' => ['color' => '#1B5E20', 'counties' => [
                    ['name' => 'Margibi', 'host' => false],
                    ['name' => 'Grand Kru', 'host' => true],
                    ['name' => 'Bomi', 'host' => false],
                ]],
            ];
            foreach ($groups as $label => $group):
            ?>
            <div class="col-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <span class="group-badge group-<?= $label ?>" style="width:40px;height:40px;line-height:40px;font-size:1rem;"><?= $label ?></span>
                            <h5 class="ms-2 mb-0 fw-bold">Group <?= $label ?></h5>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($group['counties'] as $county): ?>
                            <div class="county-grid-item">
                                <?php
                                $flagFile = '';
                                $flagMap = [
                                    'Bomi' => 'Bomi.png', 'Bong' => 'Bong.png', 'Gbarpolu' => 'Gbarpolu.png',
                                    'Grand Bassa' => 'Grand Bassa.png', 'Grand Cape Mount' => 'Grand Cape Mount.png',
                                    'Grand Gedeh' => 'Grand Gedeh.png', 'Grand Kru' => 'Grand Kru.png',
                                    'Lofa' => 'Lofa.png', 'Margibi' => 'Margibi.png', 'Maryland' => 'Maryland.png',
                                    'Montserrado' => 'Montserrado.png', 'Nimba' => 'Nimba.png',
                                    'River Cess' => 'Rivercess.jpg', 'River Gee' => 'River Gee.png', 'Sinoe' => 'Sinoe.png',
                                ];
                                $flagFile = $flagMap[$county['name']] ?? '';
                                ?>
                                <?php if ($flagFile): ?>
                                <img src="<?= APP_URL ?>assets/images/<?= $flagFile ?>" alt="<?= $county['name'] ?>">
                                <?php endif; ?>
                                <div>
                                    <div class="county-name"><?= $county['name'] ?> <?php if ($county['host']): ?><span class="badge bg-warning text-dark ms-1" style="font-size:0.55rem;">HOST</span><?php endif; ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Videos -->
<?php if (!empty($videos)): ?>
<section class="section section-dark" id="videos">
    <div class="container">
        <div class="section-header">
            <span class="overline">Watch</span>
            <h2>Videos</h2>
            <p>Highlights, recaps, and memorable moments from the National County Sports Meet.</p>
        </div>
        <div class="row g-3 g-md-4">
            <?php foreach ($videos as $v): ?>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="video-card">
                    <?php if ($v['video_type'] === 'url'): ?>
                    <?php
                        $thumbUrl = !empty($v['thumbnail_path'])
                            ? APP_URL . 'uploads/gallery/' . rawurlencode($v['thumbnail_path'])
                            : APP_URL . 'assets/images/ncsm.png';
                        $embed = null;
                        if (preg_match('#(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})#', $v['embed_url'] ?? '', $m)) {
                            $embed = 'https://www.youtube.com/embed/' . $m[1];
                        }
                    ?>
                    <a href="<?= $embed ?: ($v['embed_url'] ?? '#') ?>" <?= $embed ? 'data-bs-toggle="modal" data-bs-target="#videoModal" data-video="' . htmlspecialchars($embed) . '"' : 'target="_blank" rel="noopener"' ?> class="text-decoration-none">
                        <div class="video-thumb">
                            <img src="<?= $thumbUrl ?>" alt="<?= htmlspecialchars($v['title']) ?>">
                            <div class="play-btn"><i class="bi bi-play-fill"></i></div>
                        </div>
                        <div class="video-info">
                            <h6><?= htmlspecialchars($v['title']) ?></h6>
                            <?php if (!empty($v['description'])): ?><small><i class="bi bi-clock me-1"></i><?= htmlspecialchars($v['description']) ?></small><?php endif; ?>
                        </div>
                    </a>
                    <?php elseif (!empty($v['video_path'])): ?>
                    <div class="video-thumb" style="cursor:pointer;" onclick="this.parentElement.querySelector('video').play();">
                        <video src="<?= APP_URL . 'uploads/videos/' . rawurlencode($v['video_path']) ?>" class="w-100" style="height:100%;object-fit:cover;" preload="metadata" controls></video>
                    </div>
                    <div class="video-info">
                        <h6><?= htmlspecialchars($v['title']) ?></h6>
                        <?php if (!empty($v['description'])): ?><small><i class="bi bi-clock me-1"></i><?= htmlspecialchars($v['description']) ?></small><?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?= APP_URL ?>pages/media.php" class="btn btn-outline-light">View All Videos</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Gallery -->
<?php if (!empty($homePhotos)): ?>
<section class="section" id="gallery">
    <div class="container">
        <div class="section-header">
            <span class="overline">Gallery</span>
            <h2>Photo Gallery</h2>
            <p>Browse through photos from the National County Sports Meet events.</p>
        </div>
        <div class="row g-2 g-md-3">
            <?php foreach ($homePhotos as $p): ?>
            <div class="col-6 col-lg-3">
                <a href="<?= APP_URL . 'uploads/gallery/' . rawurlencode($p['photo_path']) ?>" target="_blank" class="text-decoration-none">
                    <div class="gallery-item">
                        <img src="<?= APP_URL . 'uploads/gallery/' . rawurlencode($p['photo_path']) ?>" alt="<?= htmlspecialchars($p['caption'] ?: $p['category_title']) ?>">
                        <div class="gallery-overlay">
                            <h6 class="mb-0"><?= htmlspecialchars($p['caption'] ?: $p['category_title']) ?></h6>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?= APP_URL ?>pages/media.php" class="btn btn-outline-primary">View Full Gallery</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- MOYS Link -->
<section class="section" style="background:linear-gradient(135deg,#0a3d0a,#1a7a1a);color:#fff;">
    <div class="container text-center">
        <h3 class="fw-bold mb-2">Ministry of Youth & Sports</h3>
        <p class="mb-3" style="color:rgba(255,255,255,0.8);">The National County Sports Meet is organized under the auspices of the Ministry of Youth & Sports of Liberia.</p>
        <a href="https://moys.gov.lr" target="_blank" rel="noopener" class="btn btn-light btn-lg fw-semibold">
            <i class="bi bi-globe me-2"></i>Visit MOYS Website
        </a>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2>Ready to Get Started?</h2>
        <p>Access the portal to register players, manage matches, and view live scores for the National County Sports Meet.</p>
        <a href="<?= APP_URL ?>auth/login.php" class="btn btn-light btn-lg">
            <i class="bi bi-box-arrow-in-right me-2"></i>Login to Portal
        </a>
    </div>
</section>

<?php include __DIR__ . '/templates/public_footer.php'; ?>

<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0 py-2">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ratio ratio-16x9">
                    <iframe id="videoFrame" src="" title="Video" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('[data-bs-toggle="modal"][data-video]').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('videoFrame').src = btn.getAttribute('data-video');
    });
});
document.getElementById('videoModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('videoFrame').src = '';
});
</script>

<script>
(function() {
    const container = document.getElementById('particles');
    for (let i = 0; i < 25; i++) {
        const p = document.createElement('div');
        p.className = 'hero-particle';
        p.style.left = Math.random() * 100 + '%';
        p.style.animationDuration = (Math.random() * 12 + 10) + 's';
        p.style.animationDelay = (Math.random() * 12) + 's';
        p.style.width = p.style.height = (Math.random() * 3 + 2) + 'px';
        container.appendChild(p);
    }
})();
</script>
