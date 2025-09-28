<?php
namespace App\Features\Equipment\Domain;

interface EquipmentRepository
{
    /** @return array<int, array<string, mixed>> */
    public function listAll(int $limit = 100, int $offset = 0): array;

    /** @return array<int, array<string, mixed>> */
    public function listByClientId(string $clientId, int $limit = 100, int $offset = 0): array;

    /** Crea un equipo y devuelve el registro insertado */
    public function create(
        string $id,
        string $serialNumber,
        string $brand,
        string $model,
        int $equipmentTypeId,
        array $clientIds = []
    ): array;

    /** @return array<int, array{id:int,name:string,equipment_count:int}> */
    public function listTypes(): array;

    /** @return array{id:int,name:string,equipment_count:int} */
    public function createType(string $name): array;

    /** @return array{id:int,name:string,equipment_count:int}|null */
    public function updateType(int $id, string $name): ?array;

    /** @return 'deleted'|'in_use'|'not_found' */
    public function deleteType(int $id): string;
}
