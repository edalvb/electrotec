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
            // Nueva tabla de técnicos
            ['label' => 'create:tecnico', 'sql' => <<<SQL
CREATE TABLE IF NOT EXISTS tecnico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(150) NOT NULL,
    cargo VARCHAR(100) NULL,
    path_firma VARCHAR(255) NULL,
    firma_base64 MEDIUMTEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            ],
            ['label' => 'create:users', 'sql' => <<<SQL
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'client') NOT NULL DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            ],
            ['label' => 'create:clients', 'sql' => <<<SQL
CREATE TABLE IF NOT EXISTS clients (
    id CHAR(36) PRIMARY KEY,
    user_id INT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    ruc VARCHAR(11) NOT NULL UNIQUE,
    dni VARCHAR(8) NULL,
    email VARCHAR(255) NULL,
    celular VARCHAR(20) NULL,
    direccion TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_clients_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    UNIQUE KEY idx_clients_ruc (ruc),
    KEY idx_clients_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            ],
            ['label' => 'create:equipment_types', 'sql' => <<<SQL
CREATE TABLE IF NOT EXISTS equipment_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    resultado_precision ENUM('segundos','lineal') NOT NULL DEFAULT 'segundos',
    resultado_conprisma TINYINT(1) NOT NULL DEFAULT 0
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
    calibrator_id INT NOT NULL,
    calibration_date DATE NOT NULL,
    next_calibration_date DATE NOT NULL,
    results JSON NOT NULL,
    pdf_url VARCHAR(2048),
    client_id CHAR(36) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_cert_equipment FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE RESTRICT,
    CONSTRAINT fk_cert_calibrator FOREIGN KEY (calibrator_id) REFERENCES tecnico(id) ON DELETE RESTRICT,
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
            // Ajustes/migraciones idempotentes
            ['label' => 'alter:equipment_types.add_resultado_precision', 'sql' => <<<SQL
ALTER TABLE equipment_types
    ADD COLUMN resultado_precision ENUM('segundos','lineal') NOT NULL DEFAULT 'segundos'
SQL
            ],
            ['label' => 'alter:equipment_types.add_resultado_conprisma', 'sql' => <<<SQL
ALTER TABLE equipment_types
    ADD COLUMN resultado_conprisma TINYINT(1) NOT NULL DEFAULT 0
SQL
            ],
            ['label' => 'alter:equipment.drop_resultado_precision', 'sql' => <<<SQL
ALTER TABLE equipment
    DROP COLUMN resultado_precision
SQL
            ],
            ['label' => 'alter:equipment.drop_resultado_conprisma', 'sql' => <<<SQL
ALTER TABLE equipment
    DROP COLUMN resultado_conprisma
SQL
            ],
            ['label' => 'create:condiciones_ambientales', 'sql' => <<<SQL
CREATE TABLE IF NOT EXISTS condiciones_ambientales (
    id_certificado CHAR(36) PRIMARY KEY,
    temperatura_celsius DECIMAL(5,2) NULL,
    humedad_relativa_porc DECIMAL(5,2) NULL,
    presion_atm_mmhg INT NULL,
    CONSTRAINT fk_cond_amb_cert FOREIGN KEY (id_certificado) REFERENCES certificates(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            ],
            ['label' => 'create:resultados', 'sql' => <<<SQL
CREATE TABLE IF NOT EXISTS resultados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_certificado CHAR(36) NOT NULL,
    tipo_resultado ENUM('segundos','lineal') NOT NULL,
    valor_patron_grados SMALLINT NOT NULL,
    valor_patron_minutos TINYINT UNSIGNED NOT NULL,
    valor_patron_segundos TINYINT UNSIGNED NOT NULL,
    valor_obtenido_grados SMALLINT NOT NULL,
    valor_obtenido_minutos TINYINT UNSIGNED NOT NULL,
    valor_obtenido_segundos TINYINT UNSIGNED NOT NULL,
    precision_val DECIMAL(8,4) NOT NULL,
    error_segundos TINYINT UNSIGNED NOT NULL,
    CONSTRAINT fk_resultados_cert FOREIGN KEY (id_certificado) REFERENCES certificates(id) ON DELETE CASCADE,
    KEY idx_resultados_cert (id_certificado),
    KEY idx_resultados_tipo (tipo_resultado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            ],
            ['label' => 'create:resultados_distancia', 'sql' => <<<SQL
CREATE TABLE IF NOT EXISTS resultados_distancia (
    id_resultado INT AUTO_INCREMENT PRIMARY KEY,
    id_certificado CHAR(36) NOT NULL,
    punto_control_metros DECIMAL(10,3) NOT NULL,
    distancia_obtenida_metros DECIMAL(10,3) NOT NULL,
    variacion_metros DECIMAL(10,3) NOT NULL,
    precision_base_mm INT NOT NULL,
    precision_ppm INT NOT NULL,
    con_prisma BOOLEAN NOT NULL,
    CONSTRAINT fk_resultados_distancia_cert FOREIGN KEY (id_certificado) REFERENCES certificates(id) ON DELETE CASCADE,
    KEY idx_resdist_cert (id_certificado),
    KEY idx_resdist_conprisma (con_prisma)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            ],
            // índices ya definidos en CREATE TABLE
            // Las siguientes operaciones ALTER se removieron para evitar duplicados y
            // porque los CREATE TABLE ya incluyen las columnas e índices necesarios.
            // Migración idempotente para mover FK de users -> tecnico si existía previamente
            ['label' => 'alter:certificates.drop_fk_calibrator_users', 'sql' => <<<SQL
ALTER TABLE certificates
    DROP FOREIGN KEY fk_cert_calibrator
SQL
            ],
            ['label' => 'alter:certificates.add_fk_calibrator_tecnico', 'sql' => <<<SQL
ALTER TABLE certificates
    ADD CONSTRAINT fk_cert_calibrator FOREIGN KEY (calibrator_id) REFERENCES tecnico(id) ON DELETE RESTRICT
SQL
            ],
            // Asegurar columna firma_base64 en tecnico (si tabla ya existía sin ella)
            ['label' => 'alter:tecnico.add_firma_base64', 'sql' => <<<SQL
ALTER TABLE tecnico
    ADD COLUMN firma_base64 MEDIUMTEXT NULL
SQL
            ],
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

            // Códigos MySQL idempotentes:
            // 1050: Table already exists
            // 1060: Duplicate column name
            // 1061: Duplicate key name
            // 1091: Can't DROP; check that column/key exists (usado para DROP COLUMN/INDEX idempotentes)
            if (in_array($code, [1050, 1060, 1061, 1091], true)) {
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
