<?php
namespace App\Features\Equipment\Infrastructure;

use App\Features\Equipment\Domain\EquipmentRepository;
use PDO;

final class PdoEquipmentRepository implements EquipmentRepository
{
    public function __construct(private PDO $pdo) {}

    public function listByClientId(string $clientId, int $limit = 100, int $offset = 0): array
    {
        $sql = "SELECT e.* FROM equipment e WHERE e.owner_client_id = :cid ORDER BY e.created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':cid', $clientId);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
