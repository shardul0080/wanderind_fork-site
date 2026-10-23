<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
admin_require_login();

require_once __DIR__ . '/../includes/content.php';

$basePath = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/'))), '/');
if ($basePath === '/') {
    $basePath = '';
}

function admin_gallery_pdo(): ?PDO
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

$galleryItems = [];
$galleryCategories = [];
$pdo = admin_gallery_pdo();
if ($pdo !== null) {
    try {
        $galleryItems = $pdo->query('SELECT id, src, alt, caption, category, shape, width, height, sort_order, active FROM gallery_items ORDER BY sort_order ASC, id ASC')->fetchAll();
        $galleryCategories = $pdo->query('SELECT DISTINCT category FROM gallery_items WHERE active = TRUE ORDER BY category ASC')->fetchAll();
    } catch (Throwable) {
        $galleryItems = [];
    }
}

$adminContent = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'save' && isset($_POST['id'])) {
        $pdo = admin_gallery_pdo();
        if ($pdo !== null) {
            try {
                $stmt = $pdo->prepare('UPDATE gallery_items SET src = ?, caption = ?, category = ?, shape = ?, alt = ?, width = ?, height = ?, sort_order = ?, active = ? WHERE id = ?');
                $stmt->execute([
                    trim((string) ($_POST['src'] ?? '')),
                    trim((string) ($_POST['caption'] ?? '')),
                    trim((string) ($_POST['category'] ?? 'At the table')),
                    trim((string) ($_POST['shape'] ?? 'square')),
                    trim((string) ($_POST['alt'] ?? '')),
                    (int) ($_POST['width'] ?? 0),
                    (int) ($_POST['height'] ?? 0),
                    (int) ($_POST['sort_order'] ?? 0),
                    (isset($_POST['active']) && $_POST['active'] === '1') ? 1 : 0,
                    (int) $_POST['id'],
                ]);
            } catch (Throwable) {
            }
        }
    }
    if (($_POST['action'] ?? '') === 'delete' && isset($_POST['id'])) {
        $pdo = admin_gallery_pdo();
        if ($pdo !== null) {
            try {
                $pdo->prepare('DELETE FROM gallery_items WHERE id = ?')->execute([(int) $_POST['id']]);
            } catch (Throwable) {
            }
        }
    }
    if (($_POST['action'] ?? '') === 'add') {
        $pdo = admin_gallery_pdo();
        if ($pdo !== null) {
            try {
                $pdo->prepare('INSERT INTO gallery_items (src, alt, caption, category, shape, width, height, sort_order, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)')->execute([
                    trim((string) ($_POST['src'] ?? '')),
                    trim((string) ($_POST['alt'] ?? '')),
                    trim((string) ($_POST['caption'] ?? '')),
                    trim((string) ($_POST['category'] ?? 'At the table')),
                    trim((string) ($_POST['shape'] ?? 'square')),
                    (int) ($_POST['width'] ?? 0),
                    (int) ($_POST['height'] ?? 0),
                    (int) ($_POST['sort_order'] ?? 0),
                    (isset($_POST['active']) && $_POST['active'] === '1') ? 1 : 0,
                ]);
            } catch (Throwable) {
            }
        }
    }
}

ob_start();
?>
<h2 style="margin-top:0">Gallery</h2>
<p style="color:#8a7f6d;margin-top:-6px">Manage the photos that appear on the website gallery.</p>

<div class="adminCard">
    <h3>Add a new photo</h3>
    <form method="post" action="<?= htmlspecialchars($basePath . '/admin/gallery.php', ENT_QUOTES) ?>">
        <input type="hidden" name="action" value="add">
        <div class="adminMisc">
            <label class="adminField"><span>Image path (e.g. /Hero Food Pics/IMG_0055.jpg)</span><input class="adminInput" name="src" required></label>
            <label class="adminField"><span>Caption</span><input class="adminInput" name="caption" required></label>
            <label class="adminField"><span>Alt text</span><input class="adminInput" name="alt"></label>
            <label class="adminField"><span>Category</span><select class="adminInput" name="category"><?php foreach ($galleryCategories as $cat): ?><option value="<?= htmlspecialchars((string) $cat['category'], ENT_QUOTES) ?>"><?= htmlspecialchars((string) $cat['category'], ENT_QUOTES) ?></option><?php endforeach; ?></select></label>
            <label class="adminField"><span>Shape</span><select class="adminInput" name="shape"><option value="square">Square</option><option value="portrait">Portrait</option><option value="landscape">Landscape</option></select></label>
            <label class="adminField"><span>Sort order</span><input class="adminInput" type="number" name="sort_order" value="0"></label>
            <label class="adminField"><span>Width</span><input class="adminInput" type="number" name="width" value="0"></label>
            <label class="adminField"><span>Height</span><input class="adminInput" type="number" name="height" value="0"></label>
        </div>
        <label class="adminField"><span>Show on site</span><input type="checkbox" name="active" value="1" checked></label>
        <div class="adminFormActions">
            <button class="adminBtn" type="submit">Add photo</button>
        </div>
    </form>
</div>

<div class="adminCard">
    <h3>Photos (<?= count($galleryItems) ?>)</h3>
    <table class="adminTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Photo</th>
                <th>Caption</th>
                <th>Category</th>
                <th>Shape</th>
                <th>Order</th>
                <th>Active</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($galleryItems as $item): ?>
                <tr>
                    <td><?= (int) $item['id'] ?></td>
                    <td style="max-width:180px"><img src="<?= htmlspecialchars($basePath . '/' . ltrim((string) $item['src'], '/'), ENT_QUOTES) ?>" alt="" style="width:100%;height:44px;object-fit:cover;border-radius:6px"></td>
                    <td><?= htmlspecialchars((string) $item['caption'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars((string) $item['category'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars((string) $item['shape'], ENT_QUOTES) ?></td>
                    <td><?= (int) $item['sort_order'] ?></td>
                    <td><?= ((int) $item['active']) === 1 ? 'Yes' : 'No' ?></td>
                    <td>
                        <div class="adminRowActions">
                            <form method="post" action="<?= htmlspecialchars($basePath . '/admin/gallery.php', ENT_QUOTES) ?>" style="display:inline" onsubmit="return confirm('Delete this photo?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                <button class="adminBtn danger sm" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$adminContent = ob_get_clean();
include __DIR__ . '/layout.php';
