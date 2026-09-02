<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$db = getDb();

$isSuperAdmin = hasRole('super_admin');

if ($isSuperAdmin) {
    $notifications = $db->fetchAll(
        "SELECT n.*, u.username, u.full_name, u.role
         FROM notifications n
         LEFT JOIN users u ON n.user_id = u.id
         WHERE n.title = 'Login Notification'
         ORDER BY n.created_at DESC
         LIMIT 200"
    );
    $pageTitle = 'Login Notifications';
} else {
    $notifications = $db->fetchAll(
        "SELECT n.*, u.username, u.full_name, u.role
         FROM notifications n
         LEFT JOIN users u ON n.user_id = u.id
         WHERE n.user_id = ?
         ORDER BY n.created_at DESC
         LIMIT 200",
        [$_SESSION['user_id']]
    );
    $pageTitle = 'My Notifications';
}

$totalNotifs = $db->fetchOne("SELECT COUNT(*) as c FROM notifications WHERE user_id = ?", [$_SESSION['user_id']])['c'];

$pageActions = '<a href="' . APP_URL . 'pages/dashboard.php" class="btn btn-outline-primary btn-sm"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>';
include __DIR__ . '/../templates/header.php';
?>

<?php if ($isSuperAdmin): ?>
<div class="alert alert-info py-2">
    <i class="bi bi-person-check me-1"></i>
    <strong>Login Monitor:</strong> showing account logins across the system (newest first).
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($notifications)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bell-slash" style="font-size:3rem;"></i>
            <p class="mt-2 mb-0">No notifications found.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px;"></th>
                        <th>Notification</th>
                        <th>Account</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notifications as $notif):
                        $icon = $notif['type'] === 'success' ? 'check-circle-fill text-success' : ($notif['type'] === 'danger' ? 'x-circle-fill text-danger' : ($notif['type'] === 'warning' ? 'exclamation-circle-fill text-warning' : 'info-circle-fill text-info'));
                    ?>
                    <tr class="<?= !$notif['is_read'] ? 'table-light' : '' ?>">
                        <td>
                            <?php if (!$notif['is_read']): ?>
                            <span class="badge bg-primary rounded-pill">New</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class="text-decoration-none" href="<?= APP_URL ?>pages/users/view.php?id=<?= (int)$notif['user_id'] ?>">
                                <strong><?= sanitize($notif['title']) ?></strong>
                                <br><small><?= sanitize($notif['message']) ?></small>
                            </a>
                        </td>
                        <td>
                            <strong><?= sanitize($notif['username'] ?? 'Unknown') ?></strong>
                            <?php if ($notif['full_name']): ?>
                            <br><small class="text-muted"><?= sanitize($notif['full_name']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><small class="text-muted"><?= formatDate($notif['created_at'], 'M d, Y h:i A') ?></small></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <div class="card-footer bg-white text-muted small">
        <?= count($notifications) ?> notification(s) shown &middot; You have <?= $totalNotifs ?> total.
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>