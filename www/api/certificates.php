<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Certificates\Presentation\CertificatesController;
use App\Shared\Http\JsonResponse;

$controller = new CertificatesController();

$action = $_GET['action'] ?? 'listAll';

try {
    switch ($action) {
        case 'listAll':
            $controller->listAll();
            break;
        case 'listByClientId':
            $controller->listByClientId();
            break;
        case 'listForClientUser':
            $controller->listForClientUser();
            break;
        case 'create':
            $controller->create();
            break;
        default:
            JsonResponse::error('Acción no válida', 404);
    }
} catch (Throwable $e) {
    JsonResponse::error('Error inesperado', 500, ['error' => $e->getMessage()]);
}
