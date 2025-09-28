<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Equipment\Presentation\EquipmentController;
use App\Shared\Http\JsonResponse;

$controller = new EquipmentController();
$action = $_GET['action'] ?? 'list';

try {
    switch ($action) {
        case 'list':
            $controller->listAll();
            break;
        case 'listByClientId':
            $controller->listByClientId();
            break;
        case 'listTypes':
            $controller->listTypes();
            break;
        case 'create':
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
                JsonResponse::error('Método no permitido', 405);
                break;
            }
            $controller->create();
            break;
        default:
            JsonResponse::error('Acción no válida', 404);
    }
} catch (Throwable $e) {
    JsonResponse::error('Error inesperado', 500, ['error' => $e->getMessage()]);
}
