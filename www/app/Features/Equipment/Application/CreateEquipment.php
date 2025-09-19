<?php
namespace App\Features\Equipment\Application;

use App\Features\Equipment\Domain\EquipmentRepository;

final class CreateEquipment
{
    public function __construct(private EquipmentRepository $repo) {}

    /** @return array<string,mixed> */
    public function __invoke(
        string $id,
        string $serialNumber,
        string $brand,
        string $model,
        int $equipmentTypeId,
        ?string $ownerClientId
    ): array {
        return $this->repo->create($id, $serialNumber, $brand, $model, $equipmentTypeId, $ownerClientId);
    }
}
