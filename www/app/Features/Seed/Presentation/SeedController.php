<?php
namespace App\Features\Seed\Presentation;

use App\Features\Seed\Application\SeedSampleData;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;
use App\Shared\Http\JsonResponse;
use Throwable;

final class SeedController
{
    public function run(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            JsonResponse::error('Método no permitido', 405);
            return;
        }

        $expected = (string)($_ENV['SEED_TOKEN'] ?? getenv('SEED_TOKEN') ?: '');
        if ($expected === '') {
            JsonResponse::error('SEED_TOKEN no está configurado en el entorno.', 403);
            return;
        }

        $provided = (string)($_GET['token'] ?? '');
        if (!hash_equals($expected, $provided)) {
            JsonResponse::error('Token inválido.', 403);
            return;
        }

        try {
            $pdo = (new PdoFactory(new Config()))->create();
            $seed = new SeedSampleData($pdo);
            $summary = $seed();
            JsonResponse::ok([
                'summary' => $summary,
            ]);
        } catch (Throwable $e) {
            JsonResponse::error('No se pudo ejecutar la semilla.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
