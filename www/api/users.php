<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Users\Presentation\UsersController;
use App\Features\Users\Infrastructure\UserRepository;
use App\Shared\Auth\AuthMiddleware;
use App\Shared\Auth\JwtService;
use App\Shared\Validation\Validator;
use App\Shared\Config\Config;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Http\JsonResponse;

// Configuración y dependencias
$config = new Config();
$pdoFactory = new PdoFactory($config);
$pdo = $pdoFactory->create();

$userRepository = new UserRepository($pdo);
$jwtService = new JwtService();
$authMiddleware = new AuthMiddleware($jwtService);
$validator = new Validator();

$controller = new UsersController($userRepository, $authMiddleware, $validator);

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

try {
    if ($method === 'GET' && $action === 'list') {
        $controller->list();
    } elseif ($method === 'GET' && $action === 'me') {
        $controller->me();
    } elseif ($method === 'POST') {
        $controller->create();
    } elseif ($method === 'PUT' && $action === 'me') {
        $controller->updateMe();
    } elseif ($method === 'PUT') {
        $controller->update();
    } elseif ($method === 'DELETE') {
        $controller->delete();
    } else {
        JsonResponse::error('Acción no válida', 404);
    }
} catch (Throwable $e) {
    JsonResponse::error('Error inesperado', 500, ['error' => $e->getMessage()]);
}
