<?php
namespace App\Features\Dashboard\Application;

use App\Features\Dashboard\Domain\DashboardRepository;

final class GetMissingPdfCertificates
{
    public function __construct(private DashboardRepository $repository) {}

    public function __invoke(int $limit): array
    {
        return $this->repository->getMissingPdfCertificates($limit);
    }
}
