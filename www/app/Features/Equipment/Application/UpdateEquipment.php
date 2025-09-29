<?php
namespace App\Features\Equipment\Application;

use App\Features\Equipment\Domain\EquipmentRepository;
use DomainException;

final class UpdateEquipment
{
    public function __construct(private EquipmentRepository $repo) {}

    /** @return array<string, mixed> */
    public function __invoke(
        string $id,
        string $serialNumber,
        string $brand,
        string $model,
        int $equipmentTypeId,
        ?array $clientIds = null
    ): array {
        $existing = $this->repo->findById($id);
        if ($existing === null) {
            throw new DomainException('El equipo no existe.');
        }

        $normalized = $clientIds;
        if ($normalized === null) {
            $normalized = is_array($existing['client_ids'] ?? null) ? $existing['client_ids'] : [];
        }

        $sanitized = [];
        if (is_array($normalized)) {
            foreach ($normalized as $candidate) {
                if (!is_string($candidate)) {
                    continue;
                }
                $value = trim($candidate);
                if ($value !== '') {
                    $sanitized[$value] = true;
                }
            }
        }

        $updated = $this->repo->update($id, $serialNumber, $brand, $model, $equipmentTypeId, array_keys($sanitized));
        if ($updated === null) {
            throw new DomainException('No se pudo actualizar el equipo.');
        }

        return $updated;
    }
}
