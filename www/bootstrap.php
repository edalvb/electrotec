<?php
declare(strict_types=1);

// Error reporting sensible defaults
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

date_default_timezone_set('UTC');

// CORS for API endpoints
if (php_sapi_name() !== 'cli') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

require __DIR__ . '/app/autoload.php';

// Load env from Docker env vars (already passed via docker-compose)
// Provide defaults to avoid notices when running locally without compose
$_ENV['DB_HOST'] = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1';
$_ENV['DB_PORT'] = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '3306';
$_ENV['MYSQL_DATABASE'] = $_ENV['MYSQL_DATABASE'] ?? getenv('MYSQL_DATABASE') ?: 'electrotec';
$_ENV['MYSQL_USER'] = $_ENV['MYSQL_USER'] ?? getenv('MYSQL_USER') ?: 'root';
$_ENV['MYSQL_PASSWORD'] = $_ENV['MYSQL_PASSWORD'] ?? getenv('MYSQL_PASSWORD') ?: '';
