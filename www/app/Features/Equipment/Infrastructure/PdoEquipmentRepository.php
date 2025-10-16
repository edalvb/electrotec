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
        $limit = max(1, (int) $limit);
        $offset = max(0, (int) $offset);
        $sql = "SELECT e.id, e.serial_number, e.brand, e.model, e.equipment_type_id, e.created_at,
                t.name AS equipment_type_name,
                (SELECT COUNT(*) FROM certificates c WHERE c.equipment_id = e.id AND (c.deleted_at IS NULL)) AS certificate_count
            FROM equipment e
            LEFT JOIN equipment_types t ON t.id = e.equipment_type_id
            ORDER BY e.created_at DESC
            LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

        return $this->mapEquipmentRows($rows);
    }

    public function create(string $id, string $serialNumber, string $brand, string $model, int $equipmentTypeId): array
    {
        $insert = $this->pdo->prepare('INSERT INTO equipment (id, serial_number, brand, model, equipment_type_id, created_at) VALUES (:id, :sn, :brand, :model, :type_id, NOW())');
        $insert->bindValue(':id', $id);
        $insert->bindValue(':sn', $serialNumber);
        $insert->bindValue(':brand', $brand);
        $insert->bindValue(':model', $model);
        $insert->bindValue(':type_id', $equipmentTypeId, PDO::PARAM_INT);
        $insert->execute();

        return $this->findById($id) ?? [];
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT e.id, e.serial_number, e.brand, e.model, e.equipment_type_id, e.created_at,
                t.name AS equipment_type_name,
                (SELECT COUNT(*) FROM certificates c WHERE c.equipment_id = e.id AND (c.deleted_at IS NULL)) AS certificate_count
            FROM equipment e
            LEFT JOIN equipment_types t ON t.id = e.equipment_type_id
            WHERE e.id = :id
            LIMIT 1");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }

        return $this->mapEquipmentRow($row);
    }

    public function update(string $id, string $serialNumber, string $brand, string $model, int $equipmentTypeId): ?array
    {
        if ($this->findById($id) === null) {
            return null;
        }

        $update = $this->pdo->prepare('UPDATE equipment SET serial_number = :sn, brand = :brand, model = :model, equipment_type_id = :type_id WHERE id = :id');
        $update->bindValue(':id', $id);
        $update->bindValue(':sn', $serialNumber);
        $update->bindValue(':brand', $brand);
        $update->bindValue(':model', $model);
        $update->bindValue(':type_id', $equipmentTypeId, PDO::PARAM_INT);
        $update->execute();

        return $this->findById($id);
    }

    public function delete(string $id): string
    {
        $this->pdo->beginTransaction();
        try {
            $countStmt = $this->pdo->prepare('SELECT COUNT(*) FROM certificates WHERE equipment_id = :id AND (deleted_at IS NULL)');
            $countStmt->bindValue(':id', $id);
            $countStmt->execute();
            $certificateCount = (int) $countStmt->fetchColumn();
            if ($certificateCount > 0) {
                $this->pdo->rollBack();
                return 'has_certificates';
            }

            $delete = $this->pdo->prepare('DELETE FROM equipment WHERE id = :id');
            $delete->bindValue(':id', $id);
            $delete->execute();
            $result = $delete->rowCount() > 0 ? 'deleted' : 'not_found';

            $this->pdo->commit();

            return $result;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function listTypes(): array
    {
        $sql = "SELECT t.id, t.name, COUNT(e.id) AS equipment_count
                FROM equipment_types t
                LEFT JOIN equipment e ON e.equipment_type_id = t.id
                GROUP BY t.id, t.name
                ORDER BY t.name ASC";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

        return array_map(fn(array $row) => $this->mapTypeRow($row), $rows);
    }

    public function createType(string $name): array
    {
        $stmt = $this->pdo->prepare('INSERT INTO equipment_types (name) VALUES (:name)');
        $stmt->bindValue(':name', $name);
        $stmt->execute();

        $id = (int) $this->pdo->lastInsertId();

        return $this->findTypeById($id) ?? [
            'id' => $id,
            'name' => $name,
            'equipment_count' => 0,
        ];
    }

    public function updateType(int $id, string $name): ?array
    {
        $stmt = $this->pdo->prepare('UPDATE equipment_types SET name = :name WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $name);
        $stmt->execute();

        return $this->findTypeById($id);
    }

    public function deleteType(int $id): string
    {
        $check = $this->pdo->prepare('SELECT COUNT(*) FROM equipment WHERE equipment_type_id = :id');
        $check->bindValue(':id', $id, PDO::PARAM_INT);
        $check->execute();
        $inUse = (int) $check->fetchColumn();
        if ($inUse > 0) {
            return 'in_use';
        }

        $delete = $this->pdo->prepare('DELETE FROM equipment_types WHERE id = :id');
        $delete->bindValue(':id', $id, PDO::PARAM_INT);
        $delete->execute();

        return $delete->rowCount() > 0 ? 'deleted' : 'not_found';
    }

    /** @param array<int, array<string, mixed>> $rows */
    private function mapEquipmentRows(array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        return array_map(fn(array $row) => $this->mapEquipmentRow($row), $rows);
    }

    /** @param array<string, mixed> $row */
    private function mapEquipmentRow(array $row): array
    {
        return [
            'id' => (string) ($row['id'] ?? ''),
            'serial_number' => (string) ($row['serial_number'] ?? ''),
            'brand' => (string) ($row['brand'] ?? ''),
            'model' => (string) ($row['model'] ?? ''),
            'equipment_type_id' => (int) ($row['equipment_type_id'] ?? 0),
            'equipment_type_name' => (string) ($row['equipment_type_name'] ?? ''),
            'created_at' => $row['created_at'] ?? null,
            'certificate_count' => (int) ($row['certificate_count'] ?? 0),
        ];
    }

    private function findTypeById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT t.id, t.name,
                (SELECT COUNT(*) FROM equipment e WHERE e.equipment_type_id = t.id) AS equipment_count
            FROM equipment_types t
            WHERE t.id = :id
            LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }

        return $this->mapTypeRow($row);
    }

    /** @param array<string, mixed> $row */
    private function mapTypeRow(array $row): array
    {
        return [
            'id' => (int) ($row['id'] ?? 0),
            'name' => (string) ($row['name'] ?? ''),
            'equipment_count' => (int) ($row['equipment_count'] ?? 0),
        ];
    }
}

