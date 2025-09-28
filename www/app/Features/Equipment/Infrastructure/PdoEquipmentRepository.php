<?php
namespace App\Features\Equipment\Infrastructure;

use App\Features\Equipment\Domain\EquipmentRepository;
use PDO;

final class PdoEquipmentRepository implements EquipmentRepository
{
    public function __construct(private PDO $pdo) {}

    public function listByClientId(string $clientId, int $limit = 100, int $offset = 0): array
    {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
    $sql = "SELECT e.*, t.name AS equipment_type_name
        FROM equipment e
        LEFT JOIN equipment_types t ON t.id = e.equipment_type_id
        WHERE e.owner_client_id = :cid
        ORDER BY e.created_at DESC
        LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':cid', $clientId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(string $id, string $serialNumber, string $brand, string $model, int $equipmentTypeId, ?string $ownerClientId): array
    {
        $sql = "INSERT INTO equipment (id, serial_number, brand, model, equipment_type_id, owner_client_id) 
                VALUES (:id, :sn, :brand, :model, :type_id, :owner)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':sn', $serialNumber);
        $stmt->bindValue(':brand', $brand);
        $stmt->bindValue(':model', $model);
        $stmt->bindValue(':type_id', $equipmentTypeId, PDO::PARAM_INT);
        if ($ownerClientId === null) {
            $stmt->bindValue(':owner', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':owner', $ownerClientId);
        }
        $stmt->execute();

        $select = $this->pdo->prepare("SELECT * FROM equipment WHERE id = :id");
        $select->bindValue(':id', $id);
        $select->execute();
        return (array)$select->fetch();
    }

    public function listTypes(): array
    {
        $stmt = $this->pdo->query("SELECT id, name FROM equipment_types ORDER BY name ASC");
        return $stmt->fetchAll();
    }
}
