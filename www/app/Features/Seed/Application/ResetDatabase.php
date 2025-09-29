<?php
namespace App\Features\Seed\Application;

use PDO;
use PDOException;

final class ResetDatabase
{
    /**
     * Elimina el contenido de las tablas principales antes de ejecutar la semilla.
     *
     * @return array<int, array<string, mixed>>
     */
    public function __invoke(PDO $pdo): array
    {
        $tables = [
            'client_users',
            'certificates',
            'client_equipment',
            'equipment',
            'equipment_types',
            'clients',
            'user_profiles',
        ];

        $steps = [];

        $this->setForeignKeyChecks($pdo, false);
        try {
            foreach ($tables as $table) {
                $steps[] = $this->truncateTable($pdo, $table);
            }
        } finally {
            $this->setForeignKeyChecks($pdo, true);
        }

        return $steps;
    }

    /**
     * @return array<string, mixed>
     */
    private function truncateTable(PDO $pdo, string $table): array
    {
        try {
            $rowCount = (int) $pdo->query(sprintf('SELECT COUNT(*) FROM %s', $this->quoteIdentifier($table)))->fetchColumn();
        } catch (PDOException $e) {
            return [
                'table' => $table,
                'status' => 'skipped',
                'error' => $e->getMessage(),
            ];
        }

        try {
            $pdo->exec(sprintf('TRUNCATE TABLE %s', $this->quoteIdentifier($table)));

            return [
                'table' => $table,
                'status' => 'ok',
                'deleted' => $rowCount,
            ];
        } catch (PDOException $e) {
            return [
                'table' => $table,
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function setForeignKeyChecks(PDO $pdo, bool $enabled): void
    {
        $value = $enabled ? '1' : '0';
        $pdo->exec('SET FOREIGN_KEY_CHECKS = ' . $value);
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}
