<?php
declare(strict_types=1);

/**
 * Admin panel layout.
 */
require_once __DIR__ . '/../includes/auth.php';
admin_require_login();

$basePath = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/'))), '/');
if ($basePath === '/') {
    $basePath = '';
}

function admin_e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$adminPage = basename((string) ($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php'));
$adminMap = [
    'index.php' => 'dashboard',
    'content.php' => 'content',
    'gallery.php' => 'gallery',
    'inquiries.php' => 'inquiries',
];
$adminPage = (string) ($adminMap[$adminPage] ?? 'dashboard');
$adminTitles = [
    'dashboard' => 'Dashboard | The Wandering Fork Admin',
    'content' => 'Content | The Wandering Fork Admin',
    'gallery' => 'Gallery | The Wandering Fork Admin',
    'inquiries' => 'Inquiries | The Wandering Fork Admin',
];
$adminContent = (string) ($adminContent ?? '');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= admin_e($adminTitles[$adminPage] ?? $adminTitles['dashboard']) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;1,9..144,300&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= admin_e($basePath . '/assets/css/site.css') ?>">
  <link rel="stylesheet" href="<?= admin_e($basePath . '/assets/css/php-overrides.css') ?>">
  <link rel="stylesheet" href="<?= admin_e($basePath . '/admin/admin.css') ?>">
</head>
<body class="admin-body">
<div class="admin">
  <aside class="adminSide" id="adminSide">
    <a class="adminBrand" href="<?= admin_e($basePath . '/admin/index.php') ?>">
      <span class="adminBrandMark">WF</span>
      <span class="adminBrandText">The Wandering Fork<small>Admin</small></span>
    </a>
    <nav class="adminNav" aria-label="Admin">
      <a href="<?= admin_e($basePath . '/admin/index.php') ?>" class="<?= $adminPage === 'dashboard' ? 'on' : '' ?>">Dashboard</a>
      <a href="<?= admin_e($basePath . '/admin/content.php') ?>" class="<?= $adminPage === 'content' ? 'on' : '' ?>">Site content</a>
      <a href="<?= admin_e($basePath . '/admin/gallery.php') ?>" class="<?= $adminPage === 'gallery' ? 'on' : '' ?>">Gallery</a>
      <a href="<?= admin_e($basePath . '/admin/inquiries.php') ?>" class="<?= $adminPage === 'inquiries' ? 'on' : '' ?>">Inquiries</a>
    </nav>
    <div class="adminSideFoot">
      <a href="<?= admin_e($basePath . '/') ?>">View website</a>
      <a href="<?= admin_e($basePath . '/admin/logout.php') ?>">Log out</a>
    </div>
  </aside>
  <div class="adminMain">
    <header class="adminTop">
      <div>
        <p class="adminTopKicker">The Wandering Fork</p>
        <h1 class="adminTopTitle"><?= admin_e($adminTitles[$adminPage] ?? $adminTitles['dashboard']) ?></h1>
      </div>
      <div class="adminTopUser">
        <span class="adminAvatar"><?= admin_e(substr((string) ($_SESSION['admin_user'] ?? 'A'), 0, 1)) ?></span>
        <span class="adminUserName"><?= admin_e((string) ($_SESSION['admin_user'] ?? 'Admin')) ?></span>
        <a class="adminBtn sm" href="<?= admin_e($basePath . '/admin/logout.php') ?>">Log out</a>
      </div>
    </header>
    <main class="adminContent">
      <?= $adminContent ?>
    </main>
  </div>
</div>
<script src="<?= admin_e($basePath . '/admin/admin.js') ?>" defer></script>
</body>
</html>
