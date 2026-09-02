<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
requireRole(['super_admin']);

$db = getDb();
$id = (int)($_GET['id'] ?? 0);

$user = $db->fetchOne(
    "SELECT u.*, c.name as county_name, c.group_label, s.name as sport_name, s.association_name
     FROM users u
     LEFT JOIN counties c ON u.county_id = c.id
     LEFT JOIN sports_disciplines s ON u.association_id = s.id
     WHERE u.id = ?",
    [$id]
);

if (!$user) {
    setFlash('error', 'Account not found.');
    redirect(APP_URL . 'pages/notifications.php');
}

$recentLogins = $db->fetchAll(
    "SELECT n.message, n.created_at
     FROM notifications n
     WHERE n.user_id = ? AND n.title = 'Login Notification'
     ORDER BY n.created_at DESC
     LIMIT 10",
    [$id]
);

$pageTitle = 'Account: ' . sanitize($user['username']);
$pageActions = '<a href="' . APP_URL . 'pages/notifications.php" class="btn btn-outline-primary btn-sm"><i class="bi bi-bell me-1"></i>Notifications</a>';
include __DIR__ . '/../../templates/header.php';
?>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <img src="<?= APP_URL ?>assets/images/default-avatar.svg" class="rounded-circle mb-3" style="height:96px;width:96px;object-fit:cover;" alt="">
                <h4><?= sanitize($user['full_name']) ?></h4>
                <p class="text-muted mb-0">@<?= sanitize($user['username']) ?></p>
                <div class="mt-2">
                    <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?>"><?= ucfirst($user['status']) ?></span>
                    <span class="badge bg-primary"><?= getRoleLabel($user['role']) ?></span>
                </div>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-clock-history me-1"></i>Recent Logins</h6>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentLogins)): ?>
                <div class="text-center py-4 text-muted small">No recent logins recorded.</div>
                <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($recentLogins as $l): ?>
                    <li class="list-group-item small">
                        <i class="bi bi-box-arrow-in-right me-1 text-success"></i><?= sanitize($l['message']) ?>
                        <br><small class="text-muted"><?= formatDate($l['created_at'], 'M d, Y h:i A') ?></small>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Account Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><strong>Username</strong><br><?= sanitize($user['username']) ?></div>
                    <div class="col-md-6"><strong>Full Name</strong><br><?= sanitize($user['full_name']) ?></div>
                    <div class="col-md-6"><strong>Email</strong><br><?= sanitize($user['email']) ?></div>
                    <div class="col-md-6"><strong>Phone</strong><br><?= sanitize($user['phone'] ?: '-') ?></div>
                    <div class="col-md-6"><strong>Role</strong><br><?= getRoleLabel($user['role']) ?></div>
                    <div class="col-md-6">
                        <strong>County</strong><br>
                        <?= $user['county_name'] ? sanitize($user['county_name']) . ' <span class="group-badge group-' . $user['group_label'] . '">' . $user['group_label'] . '</span>' : '-' ?>
                    </div>
                    <div class="col-md-6"><strong>Association</strong><br><?= sanitize($user['sport_name'] ?? '-') ?></div>
                    <div class="col-md-6">
                        <strong>Account Created</strong><br><?= formatDate($user['created_at'], 'M d, Y h:i A') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>