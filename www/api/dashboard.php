<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Dashboard\Presentation\DashboardController;
use App\Shared\Http\JsonResponse;

$controller = new DashboardController();
$action = $_GET['action'] ?? 'overview';

try {
    switch ($action) {
        case 'overview':
            $controller->overview();
            break;
        case 'coverageByClient':
            $controller->coverageByClient();
            break;
        case 'expiringSoon':
            $controller->expiringSoon();
            break;
        case 'riskRanking':
            $controller->riskRanking();
            break;
        case 'certificatesByMonth':
            $controller->certificatesByMonth();
            break;
        case 'distributionByEquipmentType':
            $controller->distributionByEquipmentType();
            break;
        case 'equipmentWithoutCertificates':
            $controller->equipmentWithoutCertificates();
            break;
        case 'failRates':
            $controller->failRates();
            break;
        case 'missingPdfCertificates':
            $controller->missingPdfCertificates();
            break;
        default:
            JsonResponse::error('Acción no válida', 404);
    }
} catch (Throwable $e) {
    JsonResponse::error('Error inesperado', 500, ['error' => $e->getMessage()]);
}
