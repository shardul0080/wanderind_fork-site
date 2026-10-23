<?php
declare(strict_types=1);

/**
 * Session based admin authentication.
 */
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    $cookie = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => $cookie['lifetime'],
        'path' => $cookie['path'],
        'domain' => $cookie['domain'],
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function admin_base_path(): string
{
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php'));
    $directory = str_replace('\\', '/', dirname(dirname($script)));
    return $directory === '/' || $directory === '.' ? '' : rtrim($directory, '/');
}

function admin_is_logged_in(): bool
{
    return isset($_SESSION['admin_user_id']);
}

function admin_authenticate(string $username, string $password): ?int
{
    try {
        $pdo = new PDO(
            sprintf('pgsql:host=%s;port=%d;dbname=%s', (string) (getenv('DB_HOST') ?: '127.0.0.1'), (int) (getenv('DB_PORT') ?: 5432), (string) (getenv('DB_NAME') ?: 'wandering-fork-site')),
            (string) (getenv('DB_USER') ?: 'wandering-fork-site'),
            (string) (getenv('DB_PASSWORD') ?: '1234'),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admin_users WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if (!$user) {
            return null;
        }

        if (!password_verify($password, (string) $user['password_hash'])) {
            return null;
        }

        return (int) $user['id'];
    } catch (Throwable $e) {
        error_log('Admin authentication failed: ' . $e->getMessage());
        return null;
    }
}

function admin_login(string $username, string $password): bool
{
    $userId = admin_authenticate($username, $password);
    if ($userId === null) {
        return false;
    }

    $_SESSION['admin_user_id'] = $userId;
    $_SESSION['admin_user'] = $username;
    session_regenerate_id(true);

    return true;
}

function admin_logout(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        return;
    }
    $_SESSION = [];
    session_destroy();
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
}

function admin_require_login(): void
{
    if (admin_is_logged_in()) {
        return;
    }
    $redirect = basename((string) ($_SERVER['SCRIPT_NAME'] ?? 'index.php'));
    $allowedRedirects = ['index.php', 'content.php', 'gallery.php', 'inquiries.php'];
    if (!in_array($redirect, $allowedRedirects, true)) {
        $redirect = 'index.php';
    }
    header('Location: ' . admin_base_path() . '/admin/login.php?redirect=' . rawurlencode($redirect));
    exit;
}
