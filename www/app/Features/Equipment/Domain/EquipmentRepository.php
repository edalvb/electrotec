<?php
namespace App\Features\Equipment\Domain;

interface EquipmentRepository
{
    /** @return array<int, array<string, mixed>> */
    public function listByClientId(string $clientId, int $limit = 100, int $offset = 0): array;
}
