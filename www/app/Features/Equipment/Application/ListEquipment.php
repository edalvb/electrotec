<?php
namespace App\Features\Equipment\Application;

use App\Features\Equipment\Domain\EquipmentRepository;

final class ListEquipment
{
    public function __construct(private EquipmentRepository $repo) {}

    /** @return array<int, array<string, mixed>> */
    public function __invoke(int $limit = 100, int $offset = 0): array
    {
        return $this->repo->listAll($limit, $offset);
    }
}
