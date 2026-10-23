<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
admin_require_login();

require_once __DIR__ . '/../includes/content.php';

$basePath = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/'))), '/');
if ($basePath === '/') {
    $basePath = '';
}

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

$saveNotice = '';
$pdo = admin_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($pdo === null) {
        $saveNotice = 'Could not save changes because the database is unavailable.';
    } else {
        try {
            $pdo->beginTransaction();
            switch ((string) ($_POST['save'] ?? '')) {
                case 'hero':
                    foreach (['kicker', 'title', 'text', 'cta', 'note'] as $key) {
                        $value = trim((string) ($_POST[$key] ?? ''));
                        $pdo->prepare('INSERT INTO site_content (section, key, value) VALUES (?, ?, ?) ON CONFLICT (section, key) DO UPDATE SET value = EXCLUDED.value, updated_at = NOW()')->execute(['hero', $key, $value]);
                    }
                    break;
                case 'site':
                    foreach (['tagline', 'phone', 'email', 'address', 'serving'] as $key) {
                        $value = trim((string) ($_POST[$key] ?? ''));
                        $pdo->prepare('INSERT INTO site_content (section, key, value) VALUES (?, ?, ?) ON CONFLICT (section, key) DO UPDATE SET value = EXCLUDED.value, updated_at = NOW()')->execute(['site', $key, $value]);
                    }
                    foreach (['instagram', 'facebook', 'tiktok'] as $key) {
                        $value = trim((string) ($_POST['social_' . $key] ?? ''));
                        $pdo->prepare('INSERT INTO site_content (section, key, value) VALUES (?, ?, ?) ON CONFLICT (section, key) DO UPDATE SET value = EXCLUDED.value, updated_at = NOW()')->execute(['social', $key, $value]);
                    }
                    break;
                case 'services':
                    $rows = isset($_POST['services']) && is_array($_POST['services']) ? $_POST['services'] : [];
                    $stmt = $pdo->prepare('UPDATE services SET number = ?, title = ?, tagline = ?, description = ?, image = ?, image_alt = ?, sort_order = ?, active = ? WHERE id = ?');
                    foreach ($rows as $id => $row) {
                        if (!is_array($row) || !ctype_digit((string) $id)) continue;
                        $title = trim((string) ($row['title'] ?? ''));
                        if ($title === '') continue;
                        $stmt->execute([
                            trim((string) ($row['number'] ?? '')),
                            $title,
                            trim((string) ($row['tagline'] ?? '')),
                            trim((string) ($row['description'] ?? '')),
                            trim((string) ($row['image'] ?? '')),
                            trim((string) ($row['image_alt'] ?? '')),
                            (int) ($row['sort_order'] ?? 0),
                            isset($row['active']) && $row['active'] === '1',
                            (int) $id,
                        ]);
                    }
                    break;
                case 'reviews':
                    $rows = isset($_POST['reviews']) && is_array($_POST['reviews']) ? $_POST['reviews'] : [];
                    $stmt = $pdo->prepare('UPDATE reviews SET author = ?, quote = ?, rating = ?, active = ?, sort_order = ? WHERE id = ?');
                    foreach ($rows as $id => $row) {
                        if (!is_array($row) || !ctype_digit((string) $id)) continue;
                        $quote = trim((string) ($row['quote'] ?? ''));
                        if ($quote === '') continue;
                        $stmt->execute([
                            trim((string) ($row['author'] ?? '')),
                            $quote,
                            max(1, min(5, (int) ($row['rating'] ?? 5))),
                            isset($row['active']) && $row['active'] === '1',
                            (int) ($row['sort_order'] ?? 0),
                            (int) $id,
                        ]);
                    }
                    break;
                case 'how':
                    $rows = isset($_POST['how_steps']) && is_array($_POST['how_steps']) ? $_POST['how_steps'] : [];
                    $stmt = $pdo->prepare('UPDATE how_steps SET step_number = ?, title = ?, text = ?, image = ?, image_alt = ?, sort_order = ?, active = ? WHERE id = ?');
                    foreach ($rows as $id => $row) {
                        if (!is_array($row) || !ctype_digit((string) $id)) continue;
                        $title = trim((string) ($row['title'] ?? ''));
                        if ($title === '') continue;
                        $stmt->execute([
                            trim((string) ($row['step_number'] ?? '')),
                            $title,
                            trim((string) ($row['text'] ?? '')),
                            trim((string) ($row['image'] ?? '')),
                            trim((string) ($row['image_alt'] ?? '')),
                            (int) ($row['sort_order'] ?? 0),
                            isset($row['active']) && $row['active'] === '1',
                            (int) $id,
                        ]);
                    }
                    break;
                default:
                    throw new RuntimeException('Unknown content save action.');
            }
            $pdo->commit();
            $saveNotice = 'Changes saved.';
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log('Admin content save failed: ' . $e->getMessage());
            $saveNotice = 'Could not save changes. Please check the values and try again.';
        }
    }
}

$hero = content_hero();
$site = content_site();
$services = [];
$reviews = [];
$howSteps = [];
if ($pdo !== null) {
    try {
        $services = $pdo->query('SELECT id, number, title, tagline, description, image, image_alt, sort_order, active FROM services ORDER BY sort_order ASC, id ASC')->fetchAll();
        $reviews = $pdo->query('SELECT id, author, quote, rating, sort_order, active FROM reviews ORDER BY sort_order ASC, id ASC')->fetchAll();
        $howSteps = $pdo->query('SELECT id, step_number, title, text, image, image_alt, sort_order, active FROM how_steps ORDER BY sort_order ASC, id ASC')->fetchAll();
    } catch (Throwable $e) {
        error_log('Admin content read failed: ' . $e->getMessage());
        $saveNotice = 'Could not load all content from the database.';
    }
}

$adminContent = '';
ob_start();

?>
<h2 style="margin-top:0">Site content</h2>
<p style="color:#8a7f6d;margin-top:-6px">Edit what visitors see across the website.</p>
<?php if ($saveNotice !== ''): ?>
    <div class="adminNote <?= $saveNotice === 'Changes saved.' ? 'success' : '' ?>" role="status"><?= htmlspecialchars($saveNotice, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
<?php endif; ?>

<div class="adminCard">
    <h3>Hero (homepage)</h3>
    <form method="post" action="<?= htmlspecialchars($basePath . '/admin/content.php', ENT_QUOTES) ?>">
        <input type="hidden" name="save" value="hero">
        <div class="adminMisc">
            <label class="adminField"><span>Hero kicker</span><input class="adminInput" name="kicker" value="<?= htmlspecialchars((string) $hero['kicker'], ENT_QUOTES) ?>"></label>
            <label class="adminField"><span>Hero title</span><textarea class="adminInput" name="title" rows="2"><?= htmlspecialchars((string) $hero['title'], ENT_QUOTES) ?></textarea></label>
            <label class="adminField"><span>Hero text</span><textarea class="adminInput" name="text" rows="3"><?= htmlspecialchars((string) $hero['text'], ENT_QUOTES) ?></textarea></label>
            <label class="adminField"><span>Hero call to action</span><input class="adminInput" name="cta" value="<?= htmlspecialchars((string) $hero['cta'], ENT_QUOTES) ?>"></label>
            <label class="adminField"><span>Hero note</span><input class="adminInput" name="note" value="<?= htmlspecialchars((string) $hero['note'], ENT_QUOTES) ?>"></label>
        </div>
        <div class="adminFormActions">
            <button class="adminBtn" type="submit">Save hero</button>
        </div>
    </form>
</div>

<div class="adminCard">
    <h3>Business information + socials</h3>
    <form method="post" action="<?= htmlspecialchars($basePath . '/admin/content.php', ENT_QUOTES) ?>">
        <input type="hidden" name="save" value="site">
        <div class="adminMisc">
            <label class="adminField"><span>Tagline</span><input class="adminInput" name="tagline" value="<?= htmlspecialchars((string) $site['tagline'], ENT_QUOTES) ?>"></label>
            <label class="adminField"><span>Phone</span><input class="adminInput" name="phone" value="<?= htmlspecialchars((string) $site['phone'], ENT_QUOTES) ?>"></label>
            <label class="adminField"><span>Email</span><input class="adminInput" name="email" value="<?= htmlspecialchars((string) $site['email'], ENT_QUOTES) ?>"></label>
            <label class="adminField"><span>Address</span><textarea class="adminInput" name="address" rows="2"><?= htmlspecialchars((string) $site['address'], ENT_QUOTES) ?></textarea></label>
            <label class="adminField"><span>Serving area</span><input class="adminInput" name="serving" value="<?= htmlspecialchars((string) $site['serving'], ENT_QUOTES) ?>"></label>
            <label class="adminField"><span>Instagram URL</span><input class="adminInput" name="social_instagram" value="<?= htmlspecialchars((string) ($site['socials']['instagram']['url'] ?? ''), ENT_QUOTES) ?>"></label>
            <label class="adminField"><span>Facebook URL</span><input class="adminInput" name="social_facebook" value="<?= htmlspecialchars((string) ($site['socials']['facebook']['url'] ?? ''), ENT_QUOTES) ?>"></label>
            <label class="adminField"><span>TikTok URL</span><input class="adminInput" name="social_tiktok" value="<?= htmlspecialchars((string) ($site['socials']['tiktok']['url'] ?? ''), ENT_QUOTES) ?>"></label>
        </div>
        <div class="adminFormActions">
            <button class="adminBtn" type="submit">Save business info</button>
        </div>
    </form>
</div>

<div class="adminCard">
    <h3>Services</h3>
    <form method="post" action="<?= htmlspecialchars($basePath . '/admin/content.php', ENT_QUOTES) ?>">
        <input type="hidden" name="save" value="services">
        <?php foreach ($services as $service): ?>
            <?php $index = (int) ($service['id'] ?? 0); ?>
            <div style="border:1px solid #eee5d5;border-radius:12px;padding:14px;margin-bottom:12px">
                <div class="adminGrid" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px">
                    <label class="adminField"><span>Number</span><input class="adminInput" name="services[<?= $index ?>][number]" value="<?= htmlspecialchars((string) $service['number'], ENT_QUOTES) ?>"></label>
                    <label class="adminField"><span>Title</span><input class="adminInput" name="services[<?= $index ?>][title]" value="<?= htmlspecialchars((string) $service['title'], ENT_QUOTES) ?>"></label>
                    <label class="adminField"><span>Tagline</span><input class="adminInput" name="services[<?= $index ?>][tagline]" value="<?= htmlspecialchars((string) $service['tagline'], ENT_QUOTES) ?>"></label>
                    <label class="adminField"><span>Sort order</span><input class="adminInput" type="number" name="services[<?= $index ?>][sort_order]" value="<?= (int) $service['sort_order'] ?>"></label>
                </div>
                <label class="adminField"><span>Description</span><textarea class="adminInput" name="services[<?= $index ?>][description]" rows="3"><?= htmlspecialchars((string) $service['description'], ENT_QUOTES) ?></textarea></label>
                <div class="adminGrid" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px">
                    <label class="adminField"><span>Image</span><input class="adminInput" name="services[<?= $index ?>][image]" value="<?= htmlspecialchars((string) $service['image'], ENT_QUOTES) ?>"></label>
                    <label class="adminField"><span>Image alt</span><input class="adminInput" name="services[<?= $index ?>][image_alt]" value="<?= htmlspecialchars((string) $service['image_alt'], ENT_QUOTES) ?>"></label>
                </div>
                <label class="adminField"><span>Active</span><input type="checkbox" name="services[<?= $index ?>][active]" value="1" <?= $service['active'] ? 'checked' : '' ?>></label>
            </div>
        <?php endforeach; ?>
        <div class="adminFormActions">
            <button class="adminBtn" type="submit">Save services</button>
        </div>
    </form>
</div>

<div class="adminCard">
    <h3>Reviews</h3>
    <form method="post" action="<?= htmlspecialchars($basePath . '/admin/content.php', ENT_QUOTES) ?>">
        <input type="hidden" name="save" value="reviews">
        <?php foreach ($reviews as $review): ?>
            <?php $index = (int) $review['id']; ?>
            <?php $author = (string) $review['author']; ?>
            <?php $quote = (string) $review['quote']; ?>
            <div style="border:1px solid #eee5d5;border-radius:12px;padding:14px;margin-bottom:12px">
                <div class="adminGrid" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px">
                    <label class="adminField"><span>Author</span><input class="adminInput" name="reviews[<?= $index ?>][author]" value="<?= htmlspecialchars($author, ENT_QUOTES) ?>"></label>
                    <label class="adminField"><span>Rating</span><input class="adminInput" type="number" min="1" max="5" name="reviews[<?= $index ?>][rating]" value="<?= (int) $review['rating'] ?>"></label>
                    <label class="adminField"><span>Sort order</span><input class="adminInput" type="number" name="reviews[<?= $index ?>][sort_order]" value="<?= (int) $review['sort_order'] ?>"></label>
                </div>
                <label class="adminField"><span>Quote</span><textarea class="adminInput" name="reviews[<?= $index ?>][quote]" rows="2"><?= htmlspecialchars($quote, ENT_QUOTES) ?></textarea></label>
                <label class="adminField"><span>Active</span><input type="checkbox" name="reviews[<?= $index ?>][active]" value="1" <?= $review['active'] ? 'checked' : '' ?>></label>
            </div>
        <?php endforeach; ?>
        <div class="adminFormActions">
            <button class="adminBtn" type="submit">Save reviews</button>
        </div>
    </form>
</div>

<div class="adminCard">
    <h3>How it works steps</h3>
    <form method="post" action="<?= htmlspecialchars($basePath . '/admin/content.php', ENT_QUOTES) ?>">
        <input type="hidden" name="save" value="how">
        <?php foreach ($howSteps as $step): ?>
            <?php $index = (int) $step['id']; ?>
            <?php $stepNumber = (string) $step['step_number']; ?>
            <div style="border:1px solid #eee5d5;border-radius:12px;padding:14px;margin-bottom:12px">
                <div class="adminGrid" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px">
                    <label class="adminField"><span>Step number</span><input class="adminInput" name="how_steps[<?= $index ?>][step_number]" value="<?= htmlspecialchars($stepNumber, ENT_QUOTES) ?>"></label>
                    <label class="adminField"><span>Title</span><input class="adminInput" name="how_steps[<?= $index ?>][title]" value="<?= htmlspecialchars((string) $step['title'], ENT_QUOTES) ?>"></label>
                    <label class="adminField"><span>Sort order</span><input class="adminInput" type="number" name="how_steps[<?= $index ?>][sort_order]" value="<?= (int) $step['sort_order'] ?>"></label>
                </div>
                <label class="adminField"><span>Text</span><textarea class="adminInput" name="how_steps[<?= $index ?>][text]" rows="3"><?= htmlspecialchars((string) $step['text'], ENT_QUOTES) ?></textarea></label>
                <div class="adminGrid" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px">
                    <label class="adminField"><span>Image</span><input class="adminInput" name="how_steps[<?= $index ?>][image]" value="<?= htmlspecialchars((string) $step['image'], ENT_QUOTES) ?>"></label>
                    <label class="adminField"><span>Image alt</span><input class="adminInput" name="how_steps[<?= $index ?>][image_alt]" value="<?= htmlspecialchars((string) $step['image_alt'], ENT_QUOTES) ?>"></label>
                </div>
                <label class="adminField"><span>Active</span><input type="checkbox" name="how_steps[<?= $index ?>][active]" value="1" <?= $step['active'] ? 'checked' : '' ?>></label>
            </div>
        <?php endforeach; ?>
        <div class="adminFormActions">
            <button class="adminBtn" type="submit">Save how it works</button>
        </div>
    </form>
</div>

<?php
$adminContent = ob_get_clean();

include __DIR__ . '/layout.php';
