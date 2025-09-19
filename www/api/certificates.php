<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Certificates\Presentation\CertificatesController;
use App\Shared\Http\JsonResponse;

$action = $_GET['action'] ?? 'listByClientId';
$controller = new CertificatesController();

try {
    switch ($action) {
        case 'listByClientId':
            $controller->listByClientId();
            break;
        case 'listForClientUser':
            $controller->listForClientUser();
            break;
        default:
            JsonResponse::error('Acción no válida', 404);
    }
} catch (Throwable $e) {
    JsonResponse::error('Error inesperado', 500, ['error' => $e->getMessage()]);
}
