<?php
namespace App\Features\Equipment\Infrastructure;

use App\Features\Equipment\Domain\EquipmentRepository;
use PDO;
use Throwable;

final class PdoEquipmentRepository implements EquipmentRepository
{
    public function __construct(private PDO $pdo) {}

    public function listAll(int $limit = 100, int $offset = 0): array
    {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        $sql = "SELECT e.*, t.name AS equipment_type_name\n            FROM equipment e\n            LEFT JOIN equipment_types t ON t.id = e.equipment_type_id\n            ORDER BY e.created_at DESC\n            LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        return $this->attachClients($rows);
    }

    public function listByClientId(string $clientId, int $limit = 100, int $offset = 0): array
    {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        $sql = "SELECT e.*, t.name AS equipment_type_name\n            FROM client_equipment ce\n            INNER JOIN equipment e ON e.id = ce.equipment_id\n            LEFT JOIN equipment_types t ON t.id = e.equipment_type_id\n            WHERE ce.client_id = :cid\n            ORDER BY e.created_at DESC\n            LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':cid', $clientId);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->attachClients($rows);
    }

    public function create(string $id, string $serialNumber, string $brand, string $model, int $equipmentTypeId, array $clientIds = []): array
    {
        $this->pdo->beginTransaction();
        try {
            $insert = $this->pdo->prepare("INSERT INTO equipment (id, serial_number, brand, model, equipment_type_id, created_at) VALUES (:id, :sn, :brand, :model, :type_id, NOW())");
            $insert->bindValue(':id', $id);
            $insert->bindValue(':sn', $serialNumber);
            $insert->bindValue(':brand', $brand);
            $insert->bindValue(':model', $model);
            $insert->bindValue(':type_id', $equipmentTypeId, PDO::PARAM_INT);
            $insert->execute();

            $this->syncAssignments($id, $clientIds);

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        return $this->findById($id);
    }

    public function listTypes(): array
    {
        $stmt = $this->pdo->query("SELECT id, name FROM equipment_types ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    /** @param array<int, array<string, mixed>> $rows */
    private function attachClients(array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        $ids = [];
        foreach ($rows as $row) {
            if (isset($row['id'])) {
                $ids[] = (string)$row['id'];
            }
        }

        if ($ids === []) {
            return array_map(fn(array $item) => $item + ['clients' => []], $rows);
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare("SELECT ce.equipment_id, c.id AS client_id, c.name FROM client_equipment ce INNER JOIN clients c ON c.id = ce.client_id WHERE ce.equipment_id IN ({$placeholders})");
        $stmt->execute($ids);

        $mapping = [];
        while ($link = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $equipmentId = (string)$link['equipment_id'];
            $mapping[$equipmentId][] = ['id' => (string)$link['client_id'], 'name' => (string)$link['name']];
        }

        $result = [];
        foreach ($rows as $row) {
            $equipmentId = isset($row['id']) ? (string)$row['id'] : '';
            $row['clients'] = $mapping[$equipmentId] ?? [];
            $result[] = $row;
        }

        return $result;
    }

    private function findById(string $id): array
    {
        $stmt = $this->pdo->prepare("SELECT e.*, t.name AS equipment_type_name FROM equipment e LEFT JOIN equipment_types t ON t.id = e.equipment_type_id WHERE e.id = :id LIMIT 1");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return [];
        }

        $withClients = $this->attachClients([$row]);
        return $withClients[0] ?? [];
    }

    private function syncAssignments(string $equipmentId, array $clientIds): void
    {
        $normalized = [];
        foreach ($clientIds as $clientId) {
            $value = is_string($clientId) ? trim($clientId) : '';
            if ($value !== '') {
                $normalized[$value] = true;
            }
        }

        $this->pdo->prepare('DELETE FROM client_equipment WHERE equipment_id = :eid')->execute([':eid' => $equipmentId]);

        if ($normalized === []) {
            return;
        }

        $insert = $this->pdo->prepare('INSERT INTO client_equipment (client_id, equipment_id, assigned_at) VALUES (:cid, :eid, NOW())');
        foreach (array_keys($normalized) as $clientId) {
            $insert->execute([
                ':cid' => $clientId,
                ':eid' => $equipmentId,
            ]);
        }
    }
}
