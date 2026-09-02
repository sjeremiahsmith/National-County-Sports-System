<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireRole('super_admin');

$db = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_video'])) {
    $videoId = (int)($_POST['video_id'] ?? 0);
    if ($videoId > 0) {
        $video = $db->fetchOne("SELECT * FROM videos WHERE id = ?", [$videoId]);
        if ($video) {
            if ($video['video_type'] === 'file' && $video['video_path']) {
                $fullPath = VIDEO_PATH . $video['video_path'];
                if (file_exists($fullPath)) unlink($fullPath);
            }
            if ($video['thumbnail_path']) {
                $thumbPath = GALLERY_PATH . $video['thumbnail_path'];
                if (file_exists($thumbPath)) unlink($thumbPath);
            }
            $db->delete("DELETE FROM videos WHERE id = ?", [$videoId]);
            setFlash('success', 'Video deleted.');
        }
    }
    redirect(APP_URL . 'pages/media/videos.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_video'])) {
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $embedUrl = trim($_POST['embed_url'] ?? '');
    $hasFile = isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK;
    $errors = [];

    if (empty($title)) $errors[] = 'Video title is required.';
    if (!$hasFile && empty($embedUrl)) $errors[] = 'Provide a video file or an embed/YouTube URL.';

    if (empty($errors)) {
        $videoType = 'url';
        $videoPath = null;
        $videoFileName = null;
        $embedUrlStored = null;

        if ($hasFile) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['video']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, ALLOWED_VIDEO_TYPES)) {
                $errors[] = 'Invalid video type. Allowed: MP4, WebM, OGG, MOV.';
            } elseif ($_FILES['video']['size'] > MAX_VIDEO_SIZE) {
                $errors[] = 'Video too large. Max ' . round(MAX_VIDEO_SIZE / (1024 * 1024)) . 'MB.';
            } else {
                $ext = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
                $filename = 'video_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                if (!is_dir(VIDEO_PATH)) mkdir(VIDEO_PATH, 0777, true);
                if (move_uploaded_file($_FILES['video']['tmp_name'], VIDEO_PATH . $filename)) {
                    $videoType = 'file';
                    $videoPath = $filename;
                    $videoFileName = $_FILES['video']['name'];
                } else {
                    $errors[] = 'Failed to save video file.';
                }
            }
        } elseif (!empty($embedUrl)) {
            $embedUrlStored = $embedUrl;
        }

        if (empty($errors)) {
            $thumbnail = null;
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $tfinfo = finfo_open(FILEINFO_MIME_TYPE);
                $tmime = finfo_file($tfinfo, $_FILES['thumbnail']['tmp_name']);
                finfo_close($tfinfo);
                if (in_array($tmime, ALLOWED_PHOTO_TYPES) && $_FILES['thumbnail']['size'] <= MAX_PHOTO_SIZE) {
                    $text = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
                    $tname = 'thumb_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $text;
                    if (!is_dir(GALLERY_PATH)) mkdir(GALLERY_PATH, 0777, true);
                    if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], GALLERY_PATH . $tname)) {
                        $thumbnail = $tname;
                    }
                }
            }

            $maxOrder = $db->fetchOne("SELECT COALESCE(MAX(sort_order),0) + 1 AS next FROM videos");
            $db->insert(
                "INSERT INTO videos (title, video_type, video_path, embed_url, description, thumbnail_path, sort_order, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [$title, $videoType, $videoPath, $embedUrlStored, $description, $thumbnail, $maxOrder['next'], $_SESSION['user_id']]
            );
            logActivity('upload_video', "Uploaded video: $title");
            setFlash('success', 'Video uploaded successfully.');
            redirect(APP_URL . 'pages/media/videos.php');
        }
    }
}

$videos = $db->fetchAll(
    "SELECT v.*, u.full_name as uploaded_by_name
     FROM videos v
     LEFT JOIN users u ON v.uploaded_by = u.id
     ORDER BY v.sort_order ASC, v.created_at DESC"
);

$pageTitle = 'Manage Videos';
$pageActions = '<a href="' . APP_URL . 'pages/media/gallery.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-images me-1"></i>Gallery</a>';
include __DIR__ . '/../../templates/header.php';
?>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-upload me-2"></i>Upload Video</h5>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="upload_video" value="1">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Video Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. NCSM 2026 Opening Ceremony" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Video File <span class="text-muted small">(MP4, WebM, OGG, MOV)</span></label>
                    <input type="file" name="video" class="form-control" accept="video/mp4,video/webm,video/ogg,video/quicktime">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Or YouTube/Link URL</label>
                    <input type="url" name="embed_url" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                    <small class="text-muted">Provide a video file OR a URL, not both.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Thumbnail <span class="text-muted small">(optional, JPG/PNG/GIF)</span></label>
                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Optional description..."></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i>Upload Video</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= sanitize($e) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-camera-video me-2"></i>Uploaded Videos (<?= count($videos) ?>)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($videos)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-camera-video" style="font-size:3rem;"></i>
                <p class="mt-2">No videos uploaded yet.</p>
            </div>
        <?php else: ?>
        <div class="row g-3">
            <?php foreach ($videos as $v): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100">
                    <?php if ($v['video_type'] === 'url'): ?>
                    <?php
                        $thumbSrc = $v['thumbnail_path']
                            ? APP_URL . 'uploads/gallery/' . rawurlencode($v['thumbnail_path'])
                            : APP_URL . 'assets/images/ncsm.png';
                    ?>
                    <div class="position-relative" style="height:150px;overflow:hidden;background:#111;">
                        <img src="<?= $thumbSrc ?>" alt="<?= sanitize($v['title']) ?>" class="w-100 h-100" style="object-fit:cover;">
                        <div class="position-absolute top-50 start-50 translate-middle" style="background:rgba(0,0,0,0.6);border-radius:50%;width:48px;height:48px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-play-fill text-white" style="font-size:1.8rem;"></i>
                        </div>
                    </div>
                    <?php elseif ($v['video_path']): ?>
                    <video src="<?= APP_URL . 'uploads/videos/' . rawurlencode($v['video_path']) ?>" class="w-100" style="height:150px;object-fit:cover;" preload="metadata" controls muted></video>
                    <?php endif; ?>
                    <div class="card-body">
                        <h6 class="mb-1"><?= sanitize($v['title']) ?></h6>
                        <?php if (!empty($v['description'])): ?>
                        <small class="text-muted"><?= sanitize($v['description']) ?></small>
                        <?php endif; ?>
                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <small class="text-muted"><?= sanitize($v['uploaded_by_name'] ?? 'NCSM') ?> &middot; <?= formatDate($v['created_at'], 'M d, Y') ?></small>
                            <form method="POST" onsubmit="return confirm('Delete this video?');">
                                <input type="hidden" name="delete_video" value="1">
                                <input type="hidden" name="video_id" value="<?= $v['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>