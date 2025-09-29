<?php
namespace App\Features\Equipment\Application;

use App\Features\Equipment\Domain\EquipmentRepository;

final class DeleteEquipment
{
    public function __construct(private EquipmentRepository $repo) {}

    /** @return 'deleted'|'in_use'|'not_found' */
    public function __invoke(string $id): string
    {
        return $this->repo->delete($id);
    }
}
