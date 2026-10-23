<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

admin_logout();

$basePath = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/'))), '/');
if ($basePath === '/') {
    $basePath = '';
}
header('Location: ' . $basePath . '/admin/login.php');
exit;
