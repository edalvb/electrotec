<?php
require __DIR__ . '/../bootstrap.php';

use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;
use App\Shared\Http\JsonResponse;

/**
 * @OA\Get(
 *   path="/api/setup.php",
 *   summary="Inicializar base de datos (admin)",
 *   description="Requiere query params `token` y `action=init`. Solo para uso administrativo.",
 *   @OA\Parameter(name="token", in="query", required=true, @OA\Schema(type="string")),
 *   @OA\Parameter(name="action", in="query", required=true, @OA\Schema(type="string", enum={"init"})),
 *   @OA\Response(response=200, description="OK"),
 *   @OA\Response(response=403, description="No autorizado")
 * )
 */
// Protección con token simple: pasar ?token= que coincida con SETUP_TOKEN (env)
$provided = (string)($_GET['token'] ?? '');
$expected = (string)($_ENV['SETUP_TOKEN'] ?? getenv('SETUP_TOKEN') ?: '');
if ($expected === '') {
    JsonResponse::error('SETUP_TOKEN no está configurado en el entorno. Defínelo y vuelve a intentar.', 403);
    exit;
}
if (!hash_equals($expected, $provided)) {
    JsonResponse::error('Token inválido', 403);
    exit;
}

$action = $_GET['action'] ?? 'init';
if ($action !== 'init') {
    JsonResponse::error('Acción no válida', 404);
    exit;
}

$pdo = (new PdoFactory(new Config()))->create();

// Utilidad para ejecutar sentencias y recopilar resultados
/**
 * @param PDO    $pdo
 * @param string $sql
 * @param string $label
 * @param array<string,mixed> $out
 */
function runStmt(PDO $pdo, string $sql, string $label, array &$out): void {
    try {
        $pdo->exec($sql);
        $out[] = ['step' => $label, 'status' => 'ok'];
    } catch (PDOException $e) {
        $code = (int)$e->errorInfo[1] ?? 0; // MySQL error code
        $sqlstate = (string)($e->errorInfo[0] ?? '');
        // 1061 = duplicate key name (índice ya existe)
        // 1050 = table already exists (por si algún IF NOT EXISTS fallara)
        if ($code === 1061 || $code === 1050) {
            $out[] = ['step' => $label, 'status' => 'exists', 'error' => $e->getMessage(), 'sqlstate' => $sqlstate, 'code' => $code];
        } else {
            $out[] = ['step' => $label, 'status' => 'error', 'error' => $e->getMessage(), 'sqlstate' => $sqlstate, 'code' => $code];
        }
    }
}

$results = [];

// Tablas principales (en orden para claves foráneas)
runStmt($pdo, <<<SQL
CREATE TABLE IF NOT EXISTS user_profiles (
    id CHAR(36) PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    signature_image_url VARCHAR(2048),
    role ENUM('SUPERADMIN','ADMIN','TECHNICIAN','CLIENT') NOT NULL DEFAULT 'TECHNICIAN',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    deleted_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL, 'create:user_profiles', $results);

runStmt($pdo, <<<SQL
CREATE TABLE IF NOT EXISTS clients (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact_details JSON,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL, 'create:clients', $results);

runStmt($pdo, <<<SQL
CREATE TABLE IF NOT EXISTS equipment_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL, 'create:equipment_types', $results);

runStmt($pdo, <<<SQL
CREATE TABLE IF NOT EXISTS equipment (
    id CHAR(36) PRIMARY KEY,
    serial_number VARCHAR(255) NOT NULL UNIQUE,
    brand VARCHAR(255) NOT NULL,
    model VARCHAR(255) NOT NULL,
    owner_client_id CHAR(36) NULL,
    equipment_type_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_equipment_owner_client FOREIGN KEY (owner_client_id) REFERENCES clients(id) ON DELETE SET NULL,
    CONSTRAINT fk_equipment_type FOREIGN KEY (equipment_type_id) REFERENCES equipment_types(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL, 'create:equipment', $results);

runStmt($pdo, <<<SQL
CREATE TABLE IF NOT EXISTS certificates (
    id CHAR(36) PRIMARY KEY,
    certificate_number VARCHAR(255) NOT NULL UNIQUE,
    equipment_id CHAR(36) NOT NULL,
    technician_id CHAR(36) NOT NULL,
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
    CONSTRAINT fk_cert_technician FOREIGN KEY (technician_id) REFERENCES user_profiles(id) ON DELETE RESTRICT,
    CONSTRAINT fk_cert_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL, 'create:certificates', $results);

runStmt($pdo, <<<SQL
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
SQL, 'create:client_users', $results);

// Índices adicionales
runStmt($pdo, "CREATE INDEX idx_certificates_client_id ON certificates(client_id)", 'index:certificates_client_id', $results);
runStmt($pdo, "CREATE INDEX idx_certificates_equipment_id ON certificates(equipment_id)", 'index:certificates_equipment_id', $results);
runStmt($pdo, "CREATE INDEX idx_certificates_number ON certificates(certificate_number)", 'index:certificates_number', $results);
runStmt($pdo, "CREATE INDEX idx_user_profiles_deleted_at ON user_profiles(deleted_at)", 'index:user_profiles_deleted_at', $results);

JsonResponse::ok([
    'executed' => $results,
]);

?>
