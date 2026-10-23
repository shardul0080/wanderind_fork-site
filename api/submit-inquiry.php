<?php
declare(strict_types=1);

/**
 * Public endpoint that stores "Start planning" inquiries into PostgreSQL.
 * Expects JSON: { "name": "...", "email": "...", "phone": "...", "event_date": "...", "guest_count": "..., "location": "...", "details": "..." }
 */
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$body = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($body)) {
    $body = $_POST;
}

$name = trim((string) ($body['name'] ?? ''));
$email = trim((string) ($body['email'] ?? ''));
$phone = trim((string) ($body['phone'] ?? ''));
$eventDate = trim((string) ($body['event_date'] ?? ''));
$guestCount = isset($body['guest_count']) && $body['guest_count'] !== '' ? (int) $body['guest_count'] : null;
$location = trim((string) ($body['location'] ?? ''));
$details = trim((string) ($body['details'] ?? ''));

if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Name and a valid email are required.']);
    exit;
}

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

    $stmt = $pdo->prepare('INSERT INTO inquiries (name, email, phone, event_date, guest_count, location, details, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$name, $email, $phone !== '' ? $phone : null, $eventDate !== '' ? $eventDate : null, $guestCount, $location !== '' ? $location : null, $details !== '' ? $details : null, 'new']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Database error.']);
    exit;
}

echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId()]);
