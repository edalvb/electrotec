<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Equipment\Presentation\EquipmentController;
use App\Shared\Http\JsonResponse;

$action = $_GET['action'] ?? 'listByClientId';
$controller = new EquipmentController();

try {
    switch ($action) {
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
