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
        // Orden de eliminación respetando dependencias
        $tables = [
            'resultados_distancia',
            'resultados',
            'condiciones_ambientales',
            'certificates',
            'equipment',
            'equipment_types',
            'clients',
            'tecnico',
            'users',
        ];

        $steps = [];

        $this->setForeignKeyChecks($pdo, false);
        try {
            foreach ($tables as $table) {
                $steps[] = $this->dropTableIfExists($pdo, $table);
            }
        } finally {
            $this->setForeignKeyChecks($pdo, true);
        }

        return $steps;
    }

    /**
     * @return array<string, mixed>
     */
    private function dropTableIfExists(PDO $pdo, string $table): array
    {
        try {
            $exists = $this->tableExists($pdo, $table);
        } catch (PDOException $e) {
            return [
                'table' => $table,
                'status' => 'skipped',
                'error' => $e->getMessage(),
            ];
        }

        if (!$exists) {
            return [
                'table' => $table,
                'status' => 'absent',
            ];
        }

        try {
            $pdo->exec(sprintf('DROP TABLE IF EXISTS %s', $this->quoteIdentifier($table)));

            return [
                'table' => $table,
                'status' => 'dropped',
            ];
        } catch (PDOException $e) {
            return [
                'table' => $table,
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function tableExists(PDO $pdo, string $table): bool
    {
        $sql = 'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :t';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':t' => $table]);
        return (int)$stmt->fetchColumn() > 0;
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
