<?php
namespace App\Infrastructure\Database;

use App\Shared\Config\Config;
use PDO;
use PDOException;

final class PdoFactory
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function create(): PDO
    {
        try {
            $pdo = new PDO(
                $this->config->dbDsn(),
                $this->config->dbUser(),
                $this->config->dbPassword(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
            return $pdo;
        } catch (PDOException $e) {
            // Re-throw a generic exception to avoid leaking credentials
            throw new \RuntimeException('No se pudo conectar a la base de datos. ' . $e->getMessage(), 0, $e);
        }
    }
}
