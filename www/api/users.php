<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Users\Presentation\UsersController;
use App\Shared\Http\JsonResponse;

$controller = new UsersController();
$action = $_GET['action'] ?? 'list';

try {
    switch ($action) {
        case 'list':
            $controller->list();
            break;
        default:
            JsonResponse::error('Acción no válida', 404);
    }
} catch (Throwable $e) {
    JsonResponse::error('Error inesperado', 500, ['error' => $e->getMessage()]);
}
