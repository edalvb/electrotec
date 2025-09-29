<?php
namespace App\Features\Dashboard\Domain;

interface DashboardRepository
{
    public function getOverview(): array;

    public function getCoverageByClient(): array;

    public function getExpiringSoon(int $days): array;

    public function getRiskRanking(int $limit): array;

    public function getProductivityByTechnician(): array;

    public function getCertificatesByMonth(int $months): array;

    public function getDistributionByEquipmentType(): array;

    public function getEquipmentWithoutCertificates(): array;

    public function getFailRates(int $months): array;

    public function getMissingPdfCertificates(int $limit): array;
}