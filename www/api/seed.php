<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Seed\Presentation\SeedController;
use App\Shared\Http\JsonResponse;
use Throwable;

$controller = new SeedController();

try {
    $controller->run();
} catch (Throwable $e) {
    JsonResponse::error('Error inesperado', 500, ['error' => $e->getMessage()]);
}
