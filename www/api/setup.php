<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Seed\Application\SetupDatabaseSchema;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;
use App\Shared\Http\JsonResponse;

// Protección con token simple: pasar ?token= que coincida con SETUP_TOKEN (env)
$provided = (string)($_GET['token'] ?? '');
$expected = (string)($_ENV['SETUP_TOKEN'] ?? getenv('SETUP_TOKEN') ?: '');
if ($expected === '') {
    JsonResponse::error('SETUP_TOKEN no está configurado en el entorno. Defínelo y vuelve a intentar.', 403);
    exit;
}
if (!hash_equals($expected, $provided)) {
    JsonResponse::error('Token inválido', 403);
    exit;
}

$action = $_GET['action'] ?? 'init';
if ($action !== 'init') {
    JsonResponse::error('Acción no válida', 404);
    exit;
}

$pdo = (new PdoFactory(new Config()))->create();

$schemaSetup = new SetupDatabaseSchema();

try {
    $steps = $schemaSetup($pdo);
    JsonResponse::ok([
        'schema_steps' => $steps,
    ]);
} catch (\Throwable $e) {
    JsonResponse::error('No se pudo preparar el esquema.', 500, [
        'error' => $e->getMessage(),
    ]);
}

?>
