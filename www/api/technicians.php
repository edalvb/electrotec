<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Technicians\Presentation\TechniciansController;
use App\Shared\Auth\AuthMiddleware;
use App\Shared\Auth\JwtService;
use App\Shared\Http\JsonResponse;

$jwt = new JwtService();
$auth = new AuthMiddleware($jwt);

// Requiere admin para todas las operaciones del mantenedor
$auth->requireAuth();
$auth->requireAdmin();

$controller = new TechniciansController($auth);
$action = $_GET['action'] ?? 'list';

try {
    switch ($action) {
        case 'list':
            $controller->list();
            break;
        case 'get':
            $controller->get();
            break;
        case 'create':
            $controller->create();
            break;
        case 'update':
            $controller->update();
            break;
        case 'delete':
            $controller->delete();
            break;
        default:
            JsonResponse::error('Acción no válida', 404);
    }
} catch (Throwable $e) {
    JsonResponse::error('Error inesperado', 500, ['error' => $e->getMessage()]);
}
