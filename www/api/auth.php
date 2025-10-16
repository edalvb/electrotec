<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Auth\Presentation\AuthController;
use App\Features\Users\Infrastructure\UserRepository;
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
$validator = new Validator();

$controller = new AuthController($userRepository, $jwtService, $validator);

// Método HTTP
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        $controller->login();
    } elseif ($method === 'GET') {
        $controller->me();
    } else {
        JsonResponse::error('Método no permitido', 405);
    }
} catch (Throwable $e) {
    JsonResponse::error('Error inesperado', 500, ['error' => $e->getMessage()]);
}
