<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
admin_require_login();

$basePath = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/'))), '/');
if ($basePath === '/') {
    $basePath = '';
}

function admin_inquiries_pdo(): ?PDO
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

$statuses = ['new', 'contacted', 'booked', 'declined'];

$inquiries = [];
if (($_SERVER['REQUEST_METHOD'] === 'POST') && (($_POST['action'] ?? '') === 'status') && isset($_POST['id'])) {
    $pdo = admin_inquiries_pdo();
    if ($pdo !== null) {
        try {
            $pdo->prepare('UPDATE inquiries SET status = ? WHERE id = ?')->execute([trim((string) ($_POST['status'] ?? 'new')), (int) $_POST['id']]);
        } catch (Throwable) {
        }
    }
}
if (($_SERVER['REQUEST_METHOD'] === 'POST') && (($_POST['action'] ?? '') === 'delete') && isset($_POST['id'])) {
    $pdo = admin_inquiries_pdo();
    if ($pdo !== null) {
        try {
            $pdo->prepare('DELETE FROM inquiries WHERE id = ?')->execute([(int) $_POST['id']]);
        } catch (Throwable) {
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array(($_POST['action'] ?? ''), ['status', 'delete'], true)) {
    header('Location: ' . $basePath . '/admin/inquiries.php');
    exit;
}

$pdo = admin_inquiries_pdo();
if ($pdo !== null) {
    try {
        if (isset($_GET['status']) && in_array($_GET['status'], $statuses, true)) {
            $stmt = $pdo->prepare('SELECT id, name, email, phone, event_date, guest_count, location, details, status, created_at FROM inquiries WHERE status = ? ORDER BY created_at DESC');
            $stmt->execute([$_GET['status']]);
            $inquiries = $stmt->fetchAll();
        } else {
            $inquiries = $pdo->query('SELECT id, name, email, phone, event_date, guest_count, location, details, status, created_at FROM inquiries ORDER BY created_at DESC')->fetchAll();
        }
    } catch (Throwable) {
        $inquiries = [];
    }
}

$adminContent = '';
ob_start();
?>
<h2 style="margin-top:0">Inquiries</h2>
<p style="color:#8a7f6d;margin-top:-6px">Guest inquiries submitted through the "Start planning" form.</p>

<div class="adminCard">
    <div class="adminRowActions" style="margin-bottom:14px">
        <a class="adminBtn ghost sm" href="<?= htmlspecialchars($basePath . '/admin/inquiries.php', ENT_QUOTES) ?>">All</a>
        <?php foreach ($statuses as $status): ?>
            <a class="adminBtn ghost sm" href="<?= htmlspecialchars($basePath . '/admin/inquiries.php?status=' . $status, ENT_QUOTES) ?>" style="<?= (isset($_GET['status']) && $_GET['status'] === $status) ? 'background:#b99a62;color:#fff;border-color:#b99a62' : '' ?>"><?= htmlspecialchars(ucfirst($status), ENT_QUOTES) ?></a>
        <?php endforeach; ?>
    </div>
    <?php if (count($inquiries) === 0): ?>
        <p style="color:#8a7f6d">No inquiries found.</p>
    <?php else: ?>
        <div class="inquiryList">
            <?php foreach ($inquiries as $inquiry): ?>
                <article class="inquiryCard">
                    <div class="inquiryCardHead">
                        <div>
                            <span class="inquiryId">Inquiry #<?= (int) $inquiry['id'] ?></span>
                            <h3><?= htmlspecialchars((string) $inquiry['name'], ENT_QUOTES) ?></h3>
                        </div>
                        <span class="inquirySubmitted">Submitted <?= htmlspecialchars((string) $inquiry['created_at'], ENT_QUOTES) ?></span>
                    </div>
                    <dl class="inquiryDetails">
                        <div><dt>Email</dt><dd><?= htmlspecialchars((string) $inquiry['email'], ENT_QUOTES) ?></dd></div>
                        <div><dt>Phone</dt><dd><?= htmlspecialchars((string) ($inquiry['phone'] ?? ''), ENT_QUOTES) ?: '—' ?></dd></div>
                        <div><dt>Event date</dt><dd><?= htmlspecialchars((string) ($inquiry['event_date'] ?? ''), ENT_QUOTES) ?: '—' ?></dd></div>
                        <div><dt>Guests</dt><dd><?= htmlspecialchars((string) ($inquiry['guest_count'] ?? ''), ENT_QUOTES) ?: '—' ?></dd></div>
                        <div class="inquiryLocation"><dt>Gathering location</dt><dd><?= htmlspecialchars((string) ($inquiry['location'] ?? ''), ENT_QUOTES) ?: '—' ?></dd></div>
                        <?php if (!empty($inquiry['details'])): ?>
                            <div class="inquiryMessage"><dt>Message</dt><dd><?= nl2br(htmlspecialchars((string) $inquiry['details'], ENT_QUOTES)) ?></dd></div>
                        <?php endif; ?>
                    </dl>
                    <div class="inquiryCardActions">
                        <form class="inquiryStatusForm" method="post" action="<?= htmlspecialchars($basePath . '/admin/inquiries.php', ENT_QUOTES) ?>">
                            <input type="hidden" name="action" value="status">
                            <input type="hidden" name="id" value="<?= (int) $inquiry['id'] ?>">
                            <label class="inquiryStatusLabel">Status
                                <select class="adminInput" name="status">
                                    <?php foreach ($statuses as $status): ?>
                                        <option value="<?= htmlspecialchars($status, ENT_QUOTES) ?>" <?= ((string) $inquiry['status']) === $status ? 'selected' : '' ?>><?= htmlspecialchars(ucfirst($status), ENT_QUOTES) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <button class="adminBtn sm" type="submit">Save status</button>
                        </form>
                        <form class="inquiryDeleteForm" method="post" action="<?= htmlspecialchars($basePath . '/admin/inquiries.php', ENT_QUOTES) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $inquiry['id'] ?>">
                            <button class="adminBtn danger sm" type="submit">Delete inquiry</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php
$adminContent = ob_get_clean();
include __DIR__ . '/layout.php';
