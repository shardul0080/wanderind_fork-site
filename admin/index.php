<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
admin_require_login();

function admin_pdo(): ?PDO
{
    try {
        return new PDO(
            sprintf('pgsql:host=%s;port=%d;dbname=%s', (string) (getenv('DB_HOST') ?: '127.0.0.1'), (int) (getenv('DB_PORT') ?: 5432), (string) (getenv('DB_NAME') ?: 'wandering-fork-site')),
            (string) (getenv('DB_USER') ?: 'wandering-fork-site'),
            (string) (getenv('DB_PASSWORD') ?: '1234'),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    } catch (Throwable) {
        return null;
    }
}

function admin_stat(int $number, string $label, string $href = ''): string
{
    return '<div class="adminStat"><div class="num">' . htmlspecialchars((string) $number, ENT_QUOTES) . '</div><div class="lbl">' . htmlspecialchars($label, ENT_QUOTES) . '</div>'
        . ($href !== '' ? '<div style="margin-top:8px"><a href="' . htmlspecialchars($GLOBALS['basePath'] . $href, ENT_QUOTES) . '" class="adminBtn ghost sm">Manage</a></div>' : '') . '</div>';
}

$pdo = admin_pdo();
$galleryCount = 0;
$serviceCount = 0;
$reviewCount = 0;
$inquiryTotal = 0;
$inquiryNew = 0;
$inquiries = [];
$upcoming = [];
$today = date('Y-m-d');

if ($pdo !== null) {
    try {
        $galleryCount = (int) $pdo->query('SELECT COUNT(*) FROM gallery_items')->fetchColumn();
        $serviceCount = (int) $pdo->query('SELECT COUNT(*) FROM services WHERE active = TRUE')->fetchColumn();
        $reviewCount = (int) $pdo->query('SELECT COUNT(*) FROM reviews WHERE active = TRUE')->fetchColumn();
        $inquiryTotal = (int) $pdo->query('SELECT COUNT(*) FROM inquiries')->fetchColumn();
        $inquiryNew = (int) $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'new'")->fetchColumn();
        $inquiries = $pdo->query('SELECT id, name, email, phone, event_date, guest_count, location, details, status, created_at FROM inquiries ORDER BY created_at DESC LIMIT 8')->fetchAll();
        $upcoming = $pdo->query("SELECT id, name, email, event_date, guest_count, location FROM inquiries WHERE event_date >= '" . $today . "' ORDER BY event_date ASC LIMIT 5")->fetchAll();
    } catch (Throwable) {
        // leave counts at zero on failure
    }
}

$adminContent = '';
ob_start();
?>
<?php if ($pdo === null): ?>
  <div class="adminNote">Database is not reachable. Start PostgreSQL and check your DB_* environment variables.</div>
<?php endif; ?>

<h2 style="margin-top:0">Dashboard</h2>
<p style="color:#8a7f6d;margin-top:-6px">Welcome back. Here is what is happening around the table.</p>

<div class="adminGrid">
    <?= admin_stat($galleryCount, 'Gallery photos') ?>
    <?= admin_stat($serviceCount, 'Active services') ?>
    <?= admin_stat($reviewCount, 'Published reviews') ?>
    <?= admin_stat($inquiryNew, 'New inquiries') ?>
    <?= admin_stat($inquiryTotal, 'Total inquiries') ?>
</div>

<div class="adminTwoCol">
    <div class="adminCard">
        <h3>Recent inquiries</h3>
        <?php if (count($inquiries) === 0): ?>
            <p style="color:#8a7f6d">No inquiries yet.</p>
        <?php else: ?>
            <table class="adminTable">
                <thead>
                    <tr><th>Name</th><th>Event</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($inquiries as $inquiry): ?>
                        <tr>
                            <td>
                                <a href="<?= htmlspecialchars($GLOBALS['basePath'] . '/admin/inquiries.php', ENT_QUOTES) ?>" style="color:#6d5a33"><?= htmlspecialchars((string) $inquiry['name'], ENT_QUOTES) ?></a>
                                <div style="font-size:12px;color:#8a7f6d"><?= htmlspecialchars((string) $inquiry['email'], ENT_QUOTES) ?></div>
                            </td>
                            <td><?= htmlspecialchars((string) ($inquiry['event_date'] ?? ''), ENT_QUOTES) ?></td>
                            <td><span class="adminBadge"><?= htmlspecialchars((string) $inquiry['status'], ENT_QUOTES) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <div class="adminCard">
        <h3>Upcoming gatherings</h3>
        <?php if (count($upcoming) === 0): ?>
            <p style="color:#8a7f6d">No upcoming inquiries with a date.</p>
        <?php else: ?>
            <table class="adminTable">
                <thead>
                    <tr><th>Name</th><th>Date</th><th>Guests</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($upcoming as $inquiry): ?>
                        <tr>
                            <td><?= htmlspecialchars((string) $inquiry['name'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars((string) $inquiry['event_date'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars((string) ($inquiry['guest_count'] ?? ''), ENT_QUOTES) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<style>
.adminBadge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 999px;
    background: #f3ead9;
    color: #6d5f4a;
    font-size: 11px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
</style>
<?php
$adminContent = ob_get_clean();
include __DIR__ . '/layout.php';
?>
