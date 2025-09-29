<?php
namespace App\Features\Dashboard\Application;

use App\Features\Dashboard\Domain\DashboardRepository;

final class GetExpiringSoon
{
    public function __construct(private DashboardRepository $repository) {}

    public function __invoke(int $days): array
    {
        return $this->repository->getExpiringSoon($days);
    }
}
