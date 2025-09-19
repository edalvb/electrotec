<?php
namespace App\Features\Equipment\Domain;

interface EquipmentRepository
{
    /** @return array<int, array<string, mixed>> */
    public function listByClientId(string $clientId, int $limit = 100, int $offset = 0): array;

    /** Crea un equipo y devuelve el registro insertado */
    public function create(
        string $id,
        string $serialNumber,
        string $brand,
        string $model,
        int $equipmentTypeId,
        ?string $ownerClientId
    ): array;

    /** @return array<int, array{id:int,name:string}> */
    public function listTypes(): array;
}
