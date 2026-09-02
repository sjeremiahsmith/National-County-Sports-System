<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireRole('super_admin');

$db = getDb();

$galleryCategories = [
    'opening-ceremony' => 'Opening Ceremony',
    'champion-2024' => 'Nimba County Champion 2024/2025',
    'bong-county' => 'Bong County Team',
    'grand-gedeh' => 'Grand Gedeh County',
    'river-gee' => 'River Gee County',
    'lofa-county' => 'Lofa County Team',
    'grand-bassa' => 'Grand Bassa County',
    'margibi-county' => 'Margibi County Team',
    'grand-kru' => 'Grand Kru County',
    'river-cess' => 'River Cess County',
    'bomi-county' => 'Bomi County Team',
    'grand-cape-mount' => 'Grand Cape Mount County',
    'montserrado-county' => 'Montserrado County Team',
    'sinoe-county' => 'Sinoe County',
    'gbarpolu-county' => 'Gbarpolu County Team',
    'maryland-county' => 'Maryland County',
];

$filterSlug = trim($_GET['cat'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_photo'])) {
    $photoId = (int)($_POST['photo_id'] ?? 0);
    if ($photoId > 0) {
        $photo = $db->fetchOne("SELECT photo_path FROM gallery_photos WHERE id = ?", [$photoId]);
        if ($photo) {
            $fullPath = GALLERY_PATH . $photo['photo_path'];
            if (file_exists($fullPath)) unlink($fullPath);
            $db->delete("DELETE FROM gallery_photos WHERE id = ?", [$photoId]);
            setFlash('success', 'Photo deleted.');
        }
    }
    redirect(APP_URL . 'pages/media/gallery.php' . ($filterSlug ? '?cat=' . urlencode($filterSlug) : ''));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_photo'])) {
    $caption = sanitize($_POST['caption'] ?? '');
    $slug = trim($_POST['category_slug'] ?? '');
    $title = trim($_POST['category_title'] ?? '');
    $errors = [];

    if (empty($slug)) $errors[] = 'Category is required.';
    if (empty($title)) $errors[] = 'Category title is required.';

    if (empty($errors) && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['photo']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, ALLOWED_PHOTO_TYPES)) {
            $errors[] = 'Only JPG, PNG, or GIF images are allowed.';
        } elseif ($_FILES['photo']['size'] > MAX_PHOTO_SIZE) {
            $errors[] = 'Photo too large. Max 2MB.';
        } else {
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $filename = 'gallery_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            if (!is_dir(GALLERY_PATH)) mkdir(GALLERY_PATH, 0777, true);

            if (move_uploaded_file($_FILES['photo']['tmp_name'], GALLERY_PATH . $filename)) {
                $maxOrder = $db->fetchOne("SELECT COALESCE(MAX(sort_order),0) + 1 AS next FROM gallery_photos WHERE category_slug = ?", [$slug]);
                $db->insert(
                    "INSERT INTO gallery_photos (category_slug, category_title, photo_path, caption, sort_order, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)",
                    [$slug, $title, $filename, $caption, $maxOrder['next'], $_SESSION['user_id']]
                );
                logActivity('upload_gallery_photo', "Uploaded photo to gallery: $title");
                setFlash('success', 'Photo uploaded successfully.');
                redirect(APP_URL . 'pages/media/gallery.php' . ($slug ? '?cat=' . urlencode($slug) : ''));
            } else {
                $errors[] = 'Failed to move uploaded file. Please try again.';
            }
        }
    } elseif (empty($errors)) {
        $errors[] = 'Please select a photo to upload.';
    }
}

$sql = "SELECT g.*, u.full_name as uploaded_by_name, c.name as county_name
        FROM gallery_photos g
        LEFT JOIN users u ON g.uploaded_by = u.id
        LEFT JOIN counties c ON g.uploaded_by = c.id";
$params = [];
if ($filterSlug) {
    $sql .= " WHERE g.category_slug = ?";
    $params[] = $filterSlug;
}
$sql .= " ORDER BY g.sort_order ASC, g.created_at DESC";
$photos = $db->fetchAll($sql, $params);

$countByCategory = $db->fetchAll("SELECT category_slug, COUNT(*) as total FROM gallery_photos GROUP BY category_slug");

$pageTitle = 'Manage Gallery';
$pageActions = '<a href="' . APP_URL . 'pages/media/videos.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-camera-video me-1"></i>Videos</a>';
include __DIR__ . '/../../templates/header.php';
?>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-upload me-2"></i>Upload Photo</h5>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="upload_photo" value="1">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <select name="category_slug" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php foreach ($galleryCategories as $slug => $label): ?>
                        <option value="<?= $slug ?>" <?= $filterSlug === $slug ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Category Title <span class="text-danger">*</span></label>
                    <input type="text" name="category_title" class="form-control" placeholder="e.g. Opening Ceremony">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Photo <span class="text-danger">*</span></label>
                    <input type="file" name="photo" class="form-control" accept="image/*" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Caption</label>
                    <input type="text" name="caption" class="form-control" placeholder="Optional caption...">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i>Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-images me-2"></i>Categories</h5>
    </div>
    <div class="card-body py-2">
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= APP_URL ?>pages/media/gallery.php" class="btn btn-sm <?= $filterSlug ? 'btn-outline-secondary' : 'btn-primary' ?>">All (<?= count($photos) ?>)</a>
            <?php foreach ($countByCategory as $c): ?>
            <a href="?cat=<?= urlencode($c['category_slug']) ?>" class="btn btn-sm <?= $filterSlug === $c['category_slug'] ? 'btn-primary' : 'btn-outline-secondary' ?>">
                <?= sanitize($galleryCategories[$c['category_slug']] ?? $c['category_slug']) ?> (<?= $c['total'] ?>)
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= sanitize($e) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-image me-2"></i><?= $filterSlug ? sanitize($galleryCategories[$filterSlug] ?? $filterSlug) : 'All Photos' ?> (<?= count($photos) ?>)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($photos)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-image" style="font-size:3rem;"></i>
                <p class="mt-2">No photos uploaded yet.</p>
            </div>
        <?php else: ?>
        <div class="row g-3">
            <?php foreach ($photos as $p): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100">
                    <a href="<?= APP_URL . 'uploads/gallery/' . rawurlencode($p['photo_path']) ?>" target="_blank">
                        <img src="<?= APP_URL . 'uploads/gallery/' . rawurlencode($p['photo_path']) ?>" alt="<?= sanitize($p['caption'] ?: $p['category_title']) ?>" class="w-100" style="height:180px;object-fit:cover;">
                    </a>
                    <div class="card-body py-2">
                        <small class="text-muted d-block"><?= sanitize($p['caption'] ?: $p['category_title']) ?></small>
                        <small class="text-muted d-block"><?= sanitize($galleryCategories[$p['category_slug']] ?? $p['category_title']) ?>&middot; <?= formatDate($p['created_at'], 'M d, Y') ?></small>
                        <form method="POST" class="mt-2" onsubmit="return confirm('Delete this photo?');">
                            <input type="hidden" name="delete_photo" value="1">
                            <input type="hidden" name="photo_id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm w-100"><i class="bi bi-trash me-1"></i>Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>