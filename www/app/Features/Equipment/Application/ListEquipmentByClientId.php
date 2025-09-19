<?php
namespace App\Features\Equipment\Application;

use App\Features\Equipment\Domain\EquipmentRepository;

final class ListEquipmentByClientId
{
    public function __construct(private EquipmentRepository $repo) {}

    /** @return array<int, array<string, mixed>> */
    public function __invoke(string $clientId, int $limit = 100, int $offset = 0): array
    {
        return $this->repo->listByClientId($clientId, $limit, $offset);
    }
}
