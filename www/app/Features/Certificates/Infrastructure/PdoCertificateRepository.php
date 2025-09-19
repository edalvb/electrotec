<?php
namespace App\Features\Certificates\Infrastructure;

use App\Features\Certificates\Domain\CertificateRepository;
use PDO;

final class PdoCertificateRepository implements CertificateRepository
{
    public function __construct(private PDO $pdo) {}

    public function listByClientId(string $clientId, int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT c.* FROM certificates c WHERE c.client_id = :client_id ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':client_id', $clientId);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function listForClientUser(string $userProfileId, int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT c.*\n                FROM client_users cu\n                JOIN certificates c ON c.client_id = cu.client_id\n                WHERE cu.user_profile_id = :uid\n                  AND JSON_EXTRACT(cu.permissions, '$.view_certificates') = true\n                ORDER BY c.created_at DESC\n                LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':uid', $userProfileId);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
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
