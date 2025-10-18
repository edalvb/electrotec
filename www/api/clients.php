<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Clients\Presentation\ClientsController;
use App\Shared\Auth\AuthMiddleware;
use App\Shared\Auth\JwtService;
use App\Shared\Http\JsonResponse;

// Proteger todas las rutas con autenticación
$jwtService = new JwtService();
$authMiddleware = new AuthMiddleware($jwtService);

// Requerir autenticación para todos los endpoints
$user = $authMiddleware->requireAuth();

$controller = new ClientsController();
$action = $_GET['action'] ?? 'list';

try {
    switch ($action) {
        case 'list':
            // Solo admin
            $authMiddleware->requireAdmin();
            $controller->list();
            break;
        case 'get':
            // Solo admin
            $authMiddleware->requireAdmin();
            $controller->get();
            break;
        case 'create':
            // Solo admin
            $authMiddleware->requireAdmin();
            $controller->create();
            break;
        case 'update':
            // Solo admin
            $authMiddleware->requireAdmin();
            $controller->update();
            break;
        case 'delete':
            // Solo admin
            $authMiddleware->requireAdmin();
            $controller->delete();
            break;
        case 'me':
            // Cliente o admin, devuelve su cliente asociado si existe
            $controller->me();
            break;
        case 'updateMe':
            // Cliente puede actualizar sus propios datos básicos
            $controller->updateMe();
            break;
        default:
            JsonResponse::error('Acción no válida', 404);
    }
} catch (Throwable $e) {
    JsonResponse::error('Error inesperado', 500, ['error' => $e->getMessage()]);
}
