<?php
declare(strict_types=1);

$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
if ($basePath === '/') $basePath = '';
function url(string $path = ''): string {
    global $basePath;
    $route = trim($path, '/');
    if (in_array($route, ['about', 'services', 'gallery', 'how', 'contact', 'start-planning'], true)) {
        return $basePath . '/index.php?page=' . rawurlencode($route);
    }
    return $basePath . '/' . ltrim($path, '/');
}
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/content.php';

$page = trim((string)($_GET['page'] ?? 'home'), '/');
$routes = ['home', 'about', 'services', 'gallery', 'how', 'contact', 'start-planning'];
if (!in_array($page, $routes, true)) { http_response_code(404); $page = '404'; }
$titles = [
    'home' => 'The Wandering Fork | Private Chef on the Oregon Coast',
    'about' => 'About Kindle | The Wandering Fork',
    'services' => 'Services | The Wandering Fork',
    'gallery' => 'Gallery | The Wandering Fork',
    'how' => 'How It Works | The Wandering Fork',
    'contact' => 'Contact & Press | The Wandering Fork',
    'start-planning' => 'Start Planning | The Wandering Fork',
    '404' => 'Page not found | The Wandering Fork',
];
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($titles[$page]) ?></title>
  <meta name="description" content="Thoughtful private chef dining for weddings, retreats, family gatherings, and vacation stays along the Oregon Coast.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;1,9..144,300&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(url('assets/css/site.css')) ?>">
  <link rel="stylesheet" href="<?= e(url('assets/css/php-overrides.css')) ?>">
</head>
<body class="php-site page-<?= e($page) ?>">
<?php require __DIR__ . '/includes/header.php'; ?>
<?php require __DIR__ . '/views/' . $page . '.php'; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
<script>window.SITE_BASE = <?= json_encode($basePath, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
<script src="<?= e(url('assets/site.js')) ?>" defer></script>
<?php if ($page === 'home'): ?><script src="<?= e(url('assets/home.js')) ?>" defer></script><?php endif; ?>
</body>
</html>
