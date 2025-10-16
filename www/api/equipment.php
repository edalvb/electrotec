<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Equipment\Presentation\EquipmentController;
use App\Shared\Auth\AuthMiddleware;
use App\Shared\Auth\JwtService;
use App\Shared\Http\JsonResponse;

// Proteger todas las rutas con autenticación
$jwtService = new JwtService();
$authMiddleware = new AuthMiddleware($jwtService);

// Requerir autenticación para todos los endpoints
$user = $authMiddleware->requireAuth();

$controller = new EquipmentController();
$action = $_GET['action'] ?? 'list';

try {
    switch ($action) {
        case 'list':
            // Solo administradores pueden listar todos los equipos
            $authMiddleware->requireAdmin();
            $controller->listAll();
            break;
        case 'listTypes':
            // Cualquier usuario autenticado puede ver tipos de equipos
            $controller->listTypes();
            break;
        case 'create':
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
                JsonResponse::error('Método no permitido', 405);
                break;
            }
            // Solo administradores pueden crear equipos
            $authMiddleware->requireAdmin();
            $controller->create();
            break;
        case 'update':
            if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['PUT', 'PATCH', 'POST'], true)) {
                JsonResponse::error('Método no permitido', 405);
                break;
            }
            // Solo administradores pueden actualizar equipos
            $authMiddleware->requireAdmin();
            $controller->update();
            break;
        case 'delete':
            if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['DELETE', 'POST'], true)) {
                JsonResponse::error('Método no permitido', 405);
                break;
            }
            // Solo administradores pueden eliminar equipos
            $authMiddleware->requireAdmin();
            $controller->delete();
            break;
        case 'createType':
            // Solo administradores pueden crear tipos de equipos
            $authMiddleware->requireAdmin();
            $controller->createType();
            break;
        case 'updateType':
            // Solo administradores pueden actualizar tipos de equipos
            $authMiddleware->requireAdmin();
            $controller->updateType();
            break;
        case 'deleteType':
            // Solo administradores pueden eliminar tipos de equipos
            $authMiddleware->requireAdmin();
            $controller->deleteType();
            break;
        case 'find':
            // Cualquier usuario autenticado puede buscar un equipo
            $controller->find();
            break;
        default:
            JsonResponse::error('Acción no válida', 404);
    }
} catch (Throwable $e) {
    JsonResponse::error('Error inesperado', 500, ['error' => $e->getMessage()]);
}
