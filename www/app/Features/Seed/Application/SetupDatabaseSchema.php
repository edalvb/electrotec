<?php
namespace App\Features\Seed\Application;

use PDO;
use PDOException;
use RuntimeException;

final class SetupDatabaseSchema
{
    /**
     * Ejecuta las sentencias DDL necesarias para que la base de datos esté lista.
     *
     * @return array<int, array<string, mixed>> Lista de pasos ejecutados.
     */
    public function __invoke(PDO $pdo): array
    {
        $steps = [];
        foreach ($this->statements() as $statement) {
            $result = $this->run($pdo, $statement['sql'], $statement['label']);
            $steps[] = $result;
            if ($result['status'] === 'error') {
                throw new RuntimeException(sprintf(
                    'No se pudo completar el paso "%s": %s',
                    $statement['label'],
                    $result['error'] ?? 'Error desconocido'
                ));
            }
        }

        return $steps;
    }

    /**
     * @return array<int, array{label: string, sql: string}>
     */
    private function statements(): array
    {
        $defaultPasswordHash = addslashes(password_hash('abc123', PASSWORD_DEFAULT));

        return [
            ['label' => 'create:user_profiles', 'sql' => <<<SQL
CREATE TABLE IF NOT EXISTS user_profiles (
    id CHAR(36) PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    signature_image_url VARCHAR(2048),
    role ENUM('ADMIN','CLIENT') NOT NULL DEFAULT 'CLIENT',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    deleted_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_user_profiles_email (email),
    KEY idx_user_profiles_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            ],
            ['label' => 'create:clients', 'sql' => <<<SQL
CREATE TABLE IF NOT EXISTS clients (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    contact_details JSON,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY idx_clients_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            ],
            ['label' => 'create:equipment_types', 'sql' => <<<SQL
CREATE TABLE IF NOT EXISTS equipment_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            ],
            ['label' => 'create:equipment', 'sql' => <<<SQL
CREATE TABLE IF NOT EXISTS equipment (
    id CHAR(36) PRIMARY KEY,
    serial_number VARCHAR(255) NOT NULL UNIQUE,
    brand VARCHAR(255) NOT NULL,
    model VARCHAR(255) NOT NULL,
    equipment_type_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_equipment_type FOREIGN KEY (equipment_type_id) REFERENCES equipment_types(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            ],
            ['label' => 'create:certificates', 'sql' => <<<SQL
CREATE TABLE IF NOT EXISTS certificates (
    id CHAR(36) PRIMARY KEY,
    certificate_number VARCHAR(255) NOT NULL,
    equipment_id CHAR(36) NOT NULL,
    calibrator_id CHAR(36) NOT NULL,
    calibration_date DATE NOT NULL,
    next_calibration_date DATE NOT NULL,
    results JSON NOT NULL,
    lab_conditions JSON,
    pdf_url VARCHAR(2048),
    client_id CHAR(36) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_cert_equipment FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE RESTRICT,
    CONSTRAINT fk_cert_calibrator FOREIGN KEY (calibrator_id) REFERENCES user_profiles(id) ON DELETE RESTRICT,
    CONSTRAINT fk_cert_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
    KEY idx_certificates_client_id (client_id),
    KEY idx_certificates_equipment_id (equipment_id),
    UNIQUE KEY idx_certificates_number (certificate_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            ],
            ['label' => 'create:certificate_sequences', 'sql' => <<<SQL
CREATE TABLE IF NOT EXISTS certificate_sequences (
    year INT PRIMARY KEY,
    last_number INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            ],
            ['label' => 'create:client_users', 'sql' => <<<SQL
CREATE TABLE IF NOT EXISTS client_users (
    id CHAR(36) PRIMARY KEY,
    client_id CHAR(36) NOT NULL,
    user_profile_id CHAR(36) NOT NULL,
    permissions JSON NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cu_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    CONSTRAINT fk_cu_user FOREIGN KEY (user_profile_id) REFERENCES user_profiles(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_cu_user_client (user_profile_id, client_id),
    KEY idx_cu_client (client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            ],
            // índices ya definidos en CREATE TABLE
            // Las siguientes operaciones ALTER se removieron para evitar duplicados y
            // porque los CREATE TABLE ya incluyen las columnas e índices necesarios.
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function run(PDO $pdo, string $sql, string $label): array
    {
        try {
            $pdo->exec($sql);
            return [
                'step' => $label,
                'status' => 'ok',
            ];
        } catch (PDOException $e) {
            $errorInfo = $e->errorInfo;
            $code = isset($errorInfo[1]) ? (int) $errorInfo[1] : null;
            $sqlState = isset($errorInfo[0]) ? (string) $errorInfo[0] : null;

            if (in_array($code, [1050, 1060, 1061], true)) {
                return [
                    'step' => $label,
                    'status' => 'exists',
                    'error' => $e->getMessage(),
                    'code' => $code,
                    'sqlstate' => $sqlState,
                ];
            }

            return [
                'step' => $label,
                'status' => 'error',
                'error' => $e->getMessage(),
                'code' => $code,
                'sqlstate' => $sqlState,
            ];
        }
    }
}
