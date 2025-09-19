<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Clients\Presentation\ClientsController;
use App\Shared\Http\JsonResponse;

$action = $_GET['action'] ?? 'list';
$controller = new ClientsController();

try {
    switch ($action) {
        case 'list':
            $controller->list();
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
