<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Certificates\Presentation\CertificatesController;
use App\Shared\Auth\AuthMiddleware;
use App\Shared\Auth\JwtService;
use App\Shared\Http\JsonResponse;

// Proteger todas las rutas con autenticación
$jwtService = new JwtService();
$authMiddleware = new AuthMiddleware($jwtService);

// Requerir autenticación para todos los endpoints
$user = $authMiddleware->requireAuth();

$controller = new CertificatesController();

$action = $_GET['action'] ?? 'listAll';

try {
    switch ($action) {
        case 'listAll':
            // Solo administradores pueden ver todos los certificados
            $authMiddleware->requireAdmin();
            $controller->listAll();
            break;
        case 'find':
            // Cualquier usuario autenticado puede consultar detalle (ajustar según negocio)
            $controller->find();
            break;
        case 'listByClientId':
            // Solo administradores pueden ver certificados de cualquier cliente
            $authMiddleware->requireAdmin();
            $controller->listByClientId();
            break;
        case 'listForClientUser':
            // Los clientes solo pueden ver sus propios certificados
            $controller->listForClientUser();
            break;
        case 'listForMe':
            // Deducir el client_id a partir del usuario actual y listar por client_id
            $controller->listForMe();
            break;
        case 'get':
            // Alias de find
            $controller->find();
            break;
        case 'getConditions':
            $controller->getConditions();
            break;
        case 'getResults':
            $controller->getResults();
            break;
        case 'getDistanceResults':
            $controller->getDistanceResults();
            break;
        case 'create':
            // Solo administradores pueden crear certificados
            $authMiddleware->requireAdmin();
            $controller->create();
            break;
        case 'update':
            // Solo administradores pueden editar certificados
            $authMiddleware->requireAdmin();
            $controller->update();
            break;
        default:
            JsonResponse::error('Acción no válida', 404);
    }
} catch (Throwable $e) {
    JsonResponse::error('Error inesperado', 500, ['error' => $e->getMessage()]);
}
