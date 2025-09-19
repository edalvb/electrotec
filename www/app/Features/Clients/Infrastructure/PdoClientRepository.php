<?php
namespace App\Features\Clients\Infrastructure;

use App\Features\Clients\Domain\ClientRepository;
use PDO;

final class PdoClientRepository implements ClientRepository
{
    public function __construct(private PDO $pdo) {}

    public function listAll(int $limit = 100, int $offset = 0): array
    {
        $sql = "SELECT id, name, contact_details, created_at FROM clients ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
