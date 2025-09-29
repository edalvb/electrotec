<?php
namespace App\Features\Dashboard\Application;

use App\Features\Dashboard\Domain\DashboardRepository;

final class GetEquipmentWithoutCertificates
{
    public function __construct(private DashboardRepository $repository) {}

    public function __invoke(): array
    {
        return $this->repository->getEquipmentWithoutCertificates();
    }
}
