<?php
namespace App\Features\Certificates\Infrastructure;

use App\Features\Certificates\Domain\CertificateRepository;
use PDO;

final class PdoCertificateRepository implements CertificateRepository
{
    public function __construct(private PDO $pdo) {}

    public function listAll(int $limit = 50, int $offset = 0): array
    {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);

        $sql = "SELECT c.*, 
                       cl.nombre AS client_name,
                       e.serial_number AS equipment_serial_number,
                       e.brand AS equipment_brand,
                       e.model AS equipment_model
                FROM certificates c
                LEFT JOIN clients cl ON cl.id = c.client_id
                LEFT JOIN equipment e ON e.id = c.equipment_id
                ORDER BY c.created_at DESC
                LIMIT {$limit} OFFSET {$offset}";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function countAll(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM certificates');
        return (int)$stmt->fetchColumn();
    }

    public function listByClientId(string $clientId, int $limit = 50, int $offset = 0): array
    {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        $sql = "SELECT c.* FROM certificates c WHERE c.client_id = :client_id ORDER BY c.created_at DESC LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':client_id', $clientId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function listForClientUser(string $userProfileId, int $limit = 50, int $offset = 0): array
    {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        $sql = "SELECT c.*
                FROM client_users cu
                JOIN certificates c ON c.client_id = cu.client_id
                WHERE cu.user_profile_id = :uid
                  AND JSON_EXTRACT(cu.permissions, '$.view_certificates') = true
                ORDER BY c.created_at DESC
                LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':uid', $userProfileId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByCertificateNumber(string $certificateNumber): ?array
    {
        $sql = "SELECT c.* FROM certificates c WHERE c.certificate_number = :n LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':n', $certificateNumber);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        $nowYear = (int)date('Y');

        $this->pdo->beginTransaction();
        try {
            // Obtener y actualizar secuencia de forma segura por año
            // Usamos INSERT ... ON DUPLICATE KEY UPDATE para crear fila si no existe
            $initStmt = $this->pdo->prepare(
                'INSERT INTO certificate_sequences (year, last_number) VALUES (:y, 0)
                 ON DUPLICATE KEY UPDATE last_number = last_number'
            );
            $initStmt->execute([':y' => $nowYear]);

            // Bloqueo de la fila del año para incrementar de forma atómica
            $selStmt = $this->pdo->prepare('SELECT last_number FROM certificate_sequences WHERE year = :y FOR UPDATE');
            $selStmt->execute([':y' => $nowYear]);
            $row = $selStmt->fetch();
            $last = $row && isset($row['last_number']) ? (int)$row['last_number'] : 0;
            $next = $last + 1;

            $updStmt = $this->pdo->prepare('UPDATE certificate_sequences SET last_number = :n WHERE year = :y');
            $updStmt->execute([':n' => $next, ':y' => $nowYear]);

            // Construir número con padding (por ejemplo 4 dígitos): 2025-0001
            $certNumber = sprintf('%d-%04d', $nowYear, $next);

            // Insertar certificado
            $insert = $this->pdo->prepare(
                'INSERT INTO certificates (
                    id, certificate_number, equipment_id, calibrator_id,
                    calibration_date, next_calibration_date, results, lab_conditions,
                    pdf_url, client_id, created_at, updated_at, deleted_at
                 ) VALUES (
                    :id, :certificate_number, :equipment_id, :calibrator_id,
                    :calibration_date, :next_calibration_date, :results, :lab_conditions,
                    :pdf_url, :client_id, NOW(), NOW(), NULL
                 )'
            );

            $insert->execute([
                ':id' => (string)$data['id'],
                ':certificate_number' => $certNumber,
                ':equipment_id' => (string)$data['equipment_id'],
                ':calibrator_id' => (string)$data['calibrator_id'],
                ':calibration_date' => (string)$data['calibration_date'],
                ':next_calibration_date' => (string)($data['next_calibration_date'] ?? $data['calibration_date']),
                ':results' => json_encode($data['results'] ?? new \stdClass(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                ':lab_conditions' => isset($data['lab_conditions']) ? json_encode($data['lab_conditions'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
                ':pdf_url' => $data['pdf_url'] ?? null,
                ':client_id' => $data['client_id'] ?? null,
            ]);

            $this->pdo->commit();

            // Devolver el registro creado
            $stmt = $this->pdo->prepare('SELECT * FROM certificates WHERE id = :id');
            $stmt->execute([':id' => (string)$data['id']]);
            $created = $stmt->fetch();
            return $created ?: [
                'id' => (string)$data['id'],
                'certificate_number' => $certNumber,
            ];
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
