<?php
namespace App\Features\Dashboard\Application;

use App\Features\Dashboard\Domain\DashboardRepository;

final class GetFailRates
{
    public function __construct(private DashboardRepository $repository) {}

    public function __invoke(int $months): array
    {
        return $this->repository->getFailRates($months);
    }
}
