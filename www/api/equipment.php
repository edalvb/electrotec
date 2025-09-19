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
        default:
            JsonResponse::error('Acción no válida', 404);
    }
} catch (Throwable $e) {
    JsonResponse::error('Error inesperado', 500, ['error' => $e->getMessage()]);
}
