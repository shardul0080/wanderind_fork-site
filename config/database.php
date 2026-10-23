<?php
declare(strict_types=1);

/**
 * PostgreSQL connection for The Wandering Fork.
 *
 * Connection settings are read from environment variables so the same code
 * works on any host. Defaults are provided for local development.
 *
 *   DB_HOST     - PostgreSQL host (default: 127.0.0.1)
 *   DB_PORT     - PostgreSQL port (default: 5432)
 *   DB_NAME     - Database name (default: wandering-fork-site)
 *   DB_USER     - Database user (default: wandering-fork-site)
 *   DB_PASSWORD - Database password (default: 1234)
 */
$host = (string) (getenv('DB_HOST') ?: '127.0.0.1');
$port = (int) (getenv('DB_PORT') ?: 5432);
$dbname = (string) (getenv('DB_NAME') ?: 'wandering-fork-site');
$user = (string) (getenv('DB_USER') ?: 'wandering-fork-site');
$password = (string) (getenv('DB_PASSWORD') ?: '1234');

$dsn = sprintf('pgsql:host=%s;port=%d;dbname=%s', $host, $port, $dbname);

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_TIMEOUT => 5,
];

return new PDO($dsn, $user, $password, $options);
