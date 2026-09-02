<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Accessible by super admin, or when no users exist yet (initial setup)
$db = getDb();
$userCount = $db->fetchOne("SELECT COUNT(*) as c FROM users")['c'] ?? 0;
if ($userCount > 0) {
    requireRole(['super_admin']);
}

$db = getDb();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Create counties
        $counties = [
            ['Nimba', 'A', 'NM'],
            ['Grand Gedeh', 'A', 'GG'],
            ['River Gee', 'A', 'RG'],
            ['Gbarpolu', 'A', 'GP'],
            ['Grand Cape Mount', 'D', 'GM'],
            ['Bong', 'B', 'BG'],
            ['Maryland', 'C', 'ML'],
            ['River Cess', 'B', 'RC'],
            ['Grand Bassa', 'C', 'GB'],
            ['Lofa', 'C', 'LF'],
            ['Montserrado', 'C', 'MG'],
            ['Sinoe', 'C', 'SN'],
            ['Margibi', 'D', 'MR'],
            ['Grand Kru', 'D', 'GK'],
            ['Bomi', 'D', 'BM'],
        ];

        foreach ($counties as $c) {
            $existing = $db->fetchOne("SELECT id FROM counties WHERE code = ?", [$c[2]]);
            if (!$existing) {
                $db->insert("INSERT INTO counties (name, group_label, code) VALUES (?, ?, ?)", $c);
            }
        }

        // 2. Create sports disciplines
        $sports = [
            ['Football', 'Liberia Football Association', 'LFA'],
            ['Kickball', 'Liberia Kickball Association', 'LKA'],
            ['Basketball', 'Liberia Basketball Association', 'LBA'],
            ['Athletics', 'Liberia Athletics Association', 'LAA'],
        ];

        $sportIds = [];
        foreach ($sports as $s) {
            $existing = $db->fetchOne("SELECT id FROM sports_disciplines WHERE association_code = ?", [$s[2]]);
            if (!$existing) {
                $db->insert("INSERT INTO sports_disciplines (name, association_name, association_code) VALUES (?, ?, ?)", $s);
                $sportIds[$s[2]] = $db->getConnection()->lastInsertId();
            } else {
                $sportIds[$s[2]] = $existing['id'];
            }
        }

        // 3. Create admin users
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);

        // Super Admin
        $existingAdmin = $db->fetchOne("SELECT id FROM users WHERE username = 'admin'");
        if (!$existingAdmin) {
            $db->insert(
                "INSERT INTO users (username, password, email, full_name, role, status) VALUES (?, ?, ?, ?, ?, 'active')",
                ['admin', $adminPass, 'admin@sportsmeet.gov.lr', 'System Administrator', 'super_admin']
            );
        }

        // County Coordinators
        $countyCoords = [
            ['gedeh_coord', 'Grand Gedeh Coordinator', 'GG'],
            ['bong_coord', 'Bong Coordinator', 'BG'],
            ['lofa_coord', 'Lofa Coordinator', 'LF'],
            ['kru_coord', 'Grand Kru Coordinator', 'GK'],
        ];
        foreach ($countyCoords as $cc) {
            $existing = $db->fetchOne("SELECT id FROM users WHERE username = ?", [$cc[0]]);
            if (!$existing) {
                $countyId = $db->fetchOne("SELECT id FROM counties WHERE code = ?", [$cc[2]])['id'];
                $db->insert(
                    "INSERT INTO users (username, password, email, full_name, role, county_id, status) VALUES (?, ?, ?, ?, ?, ?, 'active')",
                    [$cc[0], $adminPass, $cc[0] . '@sportsmeet.gov.lr', $cc[1], 'county_coordinator', $countyId]
                );
            }
        }

        // County Admins
        $countyAdmins = [
            ['gedeh_admin', 'Grand Gedeh Admin', 'GG'],
            ['bong_admin', 'Bong Admin', 'BG'],
            ['lofa_admin', 'Lofa Admin', 'LF'],
            ['kru_admin', 'Grand Kru Admin', 'GK'],
        ];
        foreach ($countyAdmins as $ca) {
            $existing = $db->fetchOne("SELECT id FROM users WHERE username = ?", [$ca[0]]);
            if (!$existing) {
                $countyId = $db->fetchOne("SELECT id FROM counties WHERE code = ?", [$ca[2]])['id'];
                $db->insert(
                    "INSERT INTO users (username, password, email, full_name, role, county_id, status) VALUES (?, ?, ?, ?, ?, ?, 'active')",
                    [$ca[0], $adminPass, $ca[0] . '@sportsmeet.gov.lr', $ca[1], 'county_coordinator', $countyId]
                );
            }
        }

        // Association Admins
        foreach ($sportIds as $code => $sportId) {
            $existingAssoc = $db->fetchOne("SELECT id FROM users WHERE username = ?", [strtolower($code) . '_admin']);
            if (!$existingAssoc) {
                $db->insert(
                    "INSERT INTO users (username, password, email, full_name, role, association_id, status) VALUES (?, ?, ?, ?, ?, ?, 'active')",
                    [strtolower($code) . '_admin', $adminPass, strtolower($code) . '@' . strtolower($code) . '.org', "$code Administrator", 'association_admin', $sportId]
                );
            }
        }

        // Match Commissioner
        $existingComm = $db->fetchOne("SELECT id FROM users WHERE username = 'commissioner'");
        if (!$existingComm) {
            $db->insert(
                "INSERT INTO users (username, password, email, full_name, role, status) VALUES (?, ?, ?, ?, ?, 'active')",
                ['commissioner', $adminPass, 'commissioner@sportsmeet.gov.lr', 'Match Commissioner', 'match_commissioner']
            );
        }

        // Sports Bureau Coordinator
        $existingSports = $db->fetchOne("SELECT id FROM users WHERE username = 'sports_coord'");
        if (!$existingSports) {
            $db->insert(
                "INSERT INTO users (username, password, email, full_name, role, status) VALUES (?, ?, ?, ?, ?, 'active')",
                ['sports_coord', $adminPass, 'sports_coord@sportsmeet.gov.lr', 'Sports Bureau Coordinator', 'super_admin']
            );
        }

        $message = 'Database seeded successfully!';
        if (!empty($_SESSION['user_id'])) {
            logActivity('seed_data', 'Database seeded with initial data');
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

$stats = [
    'counties' => $db->fetchOne("SELECT COUNT(*) as c FROM counties")['c'],
    'sports' => $db->fetchOne("SELECT COUNT(*) as c FROM sports_disciplines")['c'],
    'users' => $db->fetchOne("SELECT COUNT(*) as c FROM users")['c'],
    'players' => $db->fetchOne("SELECT COUNT(*) as c FROM players")['c'],
];

$pageTitle = 'Seed Database';
?>
<?php include __DIR__ . '/templates/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Database Seeding</h5>
            </div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?= $message ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <div class="row g-3 mb-4">
                    <div class="col-3">
                        <div class="border rounded p-3 text-center">
                            <div class="h3 mb-0"><?= $stats['counties'] ?></div>
                            <small class="text-muted">Counties</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border rounded p-3 text-center">
                            <div class="h3 mb-0"><?= $stats['sports'] ?></div>
                            <small class="text-muted">Sports</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border rounded p-3 text-center">
                            <div class="h3 mb-0"><?= $stats['users'] ?></div>
                            <small class="text-muted">Users</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border rounded p-3 text-center">
                            <div class="h3 mb-0"><?= $stats['players'] ?></div>
                            <small class="text-muted">Players</small>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <strong>What this will do:</strong>
                    <ul class="mb-0">
                        <li>Insert all 15 counties into the database</li>
                        <li>Create 4 sports disciplines (Football, Kickball, Basketball, Athletics) with their associations</li>
                        <li>Create user accounts:<br>
                            <small>
                                - <strong>admin</strong> (Super Admin)<br>
                                - <strong>gedeh_coord</strong> (Grand Gedeh, Group A)<br>
                                - <strong>bong_coord</strong> (Bong, Group B)<br>
                                - <strong>lofa_coord</strong> (Lofa, Group C)<br>
                                - <strong>kru_coord</strong> (Grand Kru, Group D)<br>
                                - <strong>gedeh_admin</strong> (Grand Gedeh, Group A)<br>
                                - <strong>bong_admin</strong> (Bong, Group B)<br>
                                - <strong>lofa_admin</strong> (Lofa, Group C)<br>
                                - <strong>kru_admin</strong> (Grand Kru, Group D)<br>
                                - <strong>lfa_admin</strong> (Football Association)<br>
                                - <strong>lka_admin</strong> (Kickball Association)<br>
                                - <strong>lba_admin</strong> (Basketball Association)<br>
                                - <strong>laa_admin</strong> (Athletics Association)<br>
                                - <strong>commissioner</strong> (Match Commissioner)<br>
                                - <strong>sports_coord</strong> (Sports Bureau)<br>
                                All passwords: <code>admin123</code>
                            </small>
                        </li>
                    </ul>
                </div>

                <form method="POST" onsubmit="return confirm('This will seed the database with initial data. Continue?');">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-database-fill-up me-2"></i> Seed Database
                        </button>
                    </div>
                </form>

                <hr>
                <div class="text-center">
                    <small class="text-muted">
                        <strong>Note:</strong> Already existing records will be skipped (won't be duplicated).
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
