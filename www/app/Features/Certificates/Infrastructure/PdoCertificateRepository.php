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
                       cl.name AS client_name,
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
}
