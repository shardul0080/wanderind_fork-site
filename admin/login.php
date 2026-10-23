<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

$basePath = admin_base_path();
$allowedRedirects = ['index.php', 'content.php', 'gallery.php', 'inquiries.php'];
$redirect = (string) ($_GET['redirect'] ?? 'index.php');
if (!in_array($redirect, $allowedRedirects, true)) {
    $redirect = 'index.php';
}

if (admin_is_logged_in()) {
    header('Location: ' . $basePath . '/admin/' . $redirect);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if ($username !== '' && $password !== '' && admin_login($username, $password)) {
        header('Location: ' . $basePath . '/admin/' . $redirect);
        exit;
    }
    $error = 'Invalid username or password.';
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login | The Wandering Fork</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;1,9..144,300&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= htmlspecialchars($basePath . '/assets/css/site.css?v=' . (string) filemtime(__DIR__ . '/../assets/css/site.css'), ENT_QUOTES) ?>">
  <link rel="stylesheet" href="<?= htmlspecialchars($basePath . '/admin/admin.css?v=' . (string) filemtime(__DIR__ . '/admin.css'), ENT_QUOTES) ?>">
</head>
<body class="admin-body">
<div class="admin" style="min-height:100vh">
  <main style="flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px">
    <div class="adminCard" style="width:100%;max-width:420px">
      <p class="adminTopKicker">The Wandering Fork</p>
      <h2 style="margin:6px 0 4px">Admin login</h2>
      <p style="color:#8a7f6d;font-size:14px;margin-top:0">Sign in to manage the website.</p>
      <?php if ($error !== ''): ?>
        <div class="adminNote"><?= htmlspecialchars($error, ENT_QUOTES) ?></div>
      <?php endif; ?>
      <form method="post" action="<?= htmlspecialchars($basePath . '/admin/login.php', ENT_QUOTES) ?>">
        <label class="adminField">
          <span>Username</span>
          <input class="adminInput" name="username" type="text" autocomplete="username" required>
        </label>
        <label class="adminField">
          <span>Password</span>
          <div class="adminPasswordControl">
            <input class="adminInput" id="adminPassword" name="password" type="password" autocomplete="current-password" required>
            <button class="adminPasswordToggle" type="button" data-toggle-password="adminPassword" aria-controls="adminPassword" aria-pressed="false">Show</button>
          </div>
        </label>
        <button class="adminBtn" type="submit" style="width:100%">Sign in</button>
      </form>
      <p style="margin-top:18px;font-size:13px"><a href="<?= htmlspecialchars($basePath . '/', ENT_QUOTES) ?>">Back to website</a></p>
    </div>
  </main>
</div>
<script src="<?= htmlspecialchars($basePath . '/admin/admin.js', ENT_QUOTES) ?>" defer></script>
</body>
</html>
