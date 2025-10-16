<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Clients\Presentation\ClientsController;
use App\Shared\Auth\AuthMiddleware;
use App\Shared\Auth\JwtService;
use App\Shared\Http\JsonResponse;

// Proteger todas las rutas con autenticación
$jwtService = new JwtService();
$authMiddleware = new AuthMiddleware($jwtService);

// Requerir autenticación y rol de administrador para todos los endpoints de clientes
$user = $authMiddleware->requireAuth();
$authMiddleware->requireAdmin();

$controller = new ClientsController();
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
