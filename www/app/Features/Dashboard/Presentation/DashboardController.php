<?php
namespace App\Features\Dashboard\Presentation;

use App\Features\Dashboard\Application\GetCertificatesByMonth;
use App\Features\Dashboard\Application\GetCoverageByClient;
use App\Features\Dashboard\Application\GetDistributionByEquipmentType;
use App\Features\Dashboard\Application\GetEquipmentWithoutCertificates;
use App\Features\Dashboard\Application\GetExpiringSoon;
use App\Features\Dashboard\Application\GetFailRates;
use App\Features\Dashboard\Application\GetMissingPdfCertificates;
use App\Features\Dashboard\Application\GetDashboardOverview;
use App\Features\Dashboard\Application\GetProductivityByTechnician;
use App\Features\Dashboard\Application\GetRiskRanking;
use App\Features\Dashboard\Infrastructure\PdoDashboardRepository;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;
use App\Shared\Http\JsonResponse;

final class DashboardController
{
    private function repository(): PdoDashboardRepository
    {
        return new PdoDashboardRepository((new PdoFactory(new Config()))->create());
    }

    public function overview(): void
    {
        $useCase = new GetDashboardOverview($this->repository());
        JsonResponse::ok($useCase());
    }

    public function coverageByClient(): void
    {
        $useCase = new GetCoverageByClient($this->repository());
        JsonResponse::ok($useCase());
    }

    public function expiringSoon(): void
    {
        $days = isset($_GET['days']) ? max(1, (int)$_GET['days']) : 14;
        $useCase = new GetExpiringSoon($this->repository());
        JsonResponse::ok($useCase($days));
    }

    public function riskRanking(): void
    {
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
        $useCase = new GetRiskRanking($this->repository());
        JsonResponse::ok($useCase($limit));
    }

    public function productivityByTechnician(): void
    {
        $useCase = new GetProductivityByTechnician($this->repository());
        JsonResponse::ok($useCase());
    }

    public function certificatesByMonth(): void
    {
        $months = isset($_GET['months']) ? max(1, (int)$_GET['months']) : 12;
        $useCase = new GetCertificatesByMonth($this->repository());
        JsonResponse::ok($useCase($months));
    }

    public function distributionByEquipmentType(): void
    {
        $useCase = new GetDistributionByEquipmentType($this->repository());
        JsonResponse::ok($useCase());
    }

    public function equipmentWithoutCertificates(): void
    {
        $useCase = new GetEquipmentWithoutCertificates($this->repository());
        JsonResponse::ok($useCase());
    }

    public function failRates(): void
    {
        $months = isset($_GET['months']) ? max(1, (int)$_GET['months']) : 12;
        $useCase = new GetFailRates($this->repository());
        JsonResponse::ok($useCase($months));
    }

    public function missingPdfCertificates(): void
    {
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 20;
        $useCase = new GetMissingPdfCertificates($this->repository());
        JsonResponse::ok($useCase($limit));
    }
}
