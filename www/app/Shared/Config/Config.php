<?php
namespace App\Shared\Config;

final class Config
{
    public function dbDsn(): string
    {
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = (string)($_ENV['DB_PORT'] ?? '3306');
        $db   = $_ENV['MYSQL_DATABASE'] ?? 'electrotec';
        $charset = 'utf8mb4';
        return "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";
    }

    public function dbUser(): string
    {
        return (string)($_ENV['MYSQL_USER'] ?? 'root');
    }

    public function dbPassword(): string
    {
        return (string)($_ENV['MYSQL_PASSWORD'] ?? '');
    }
}
