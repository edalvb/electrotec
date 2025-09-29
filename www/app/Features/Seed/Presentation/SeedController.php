<?php
namespace App\Features\Seed\Presentation;

use App\Features\Seed\Application\ResetDatabase;
use App\Features\Seed\Application\SeedSampleData;
use App\Features\Seed\Application\SetupDatabaseSchema;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;
use App\Shared\Http\JsonResponse;
use Throwable;

final class SeedController
{
    public function run(): void
    {
        $method = $this->serverValue('REQUEST_METHOD');
        if ($method === null && PHP_SAPI === 'cli') {
            $method = 'POST';
        }

        if ($method !== 'POST') {
            JsonResponse::error('Método no permitido', 405);
            return;
        }

        $expected = (string)($this->envValue('SEED_TOKEN') ?? '');
        if ($expected === '') {
            JsonResponse::error('SEED_TOKEN no está configurado en el entorno.', 403);
            return;
        }

        $provided = $this->resolveToken();
        if ($provided === null || $provided === '') {
            JsonResponse::error('Token no proporcionado.', 403);
            return;
        }

        if (!hash_equals($expected, $provided)) {
            JsonResponse::error('Token inválido.', 403);
            return;
        }

        try {
            $startedAt = microtime(true);
            $pdo = (new PdoFactory(new Config()))->create();

            // 1) Reset DB (DROP TABLES)
            $resetDatabase = new ResetDatabase();
            $resetSteps = $resetDatabase($pdo);

            // 2) Re-crear esquema
            $schemaSetup = new SetupDatabaseSchema();
            $schemaSteps = $schemaSetup($pdo);

            // 3) Sembrar datos
            $seed = new SeedSampleData($pdo);
            $summary = $seed();
            JsonResponse::ok([
                'schema_steps' => $schemaSteps,
                'reset_steps' => $resetSteps,
                'summary' => $summary,
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);
        } catch (Throwable $e) {
            JsonResponse::error('No se pudo ejecutar la semilla.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function resolveToken(): ?string
    {
        $queryRaw = $_GET['token'] ?? null;
        if (is_string($queryRaw)) {
            $queryToken = trim($queryRaw);
            if ($queryToken !== '') {
                return $queryToken;
            }
        }

        if (is_array($queryRaw) && count($queryRaw) > 0) {
            $first = reset($queryRaw);
            if (is_string($first)) {
                $trimmed = trim($first);
                if ($trimmed !== '') {
                    return $trimmed;
                }
            }
        }

        $headerToken = $this->resolveHeaderToken();
        if ($headerToken !== null && $headerToken !== '') {
            return $headerToken;
        }

        $bodyToken = $this->resolveBodyToken();
        if ($bodyToken !== null && $bodyToken !== '') {
            return $bodyToken;
        }

        return null;
    }

    private function resolveHeaderToken(): ?string
    {
        $authorization = $this->serverValue('HTTP_AUTHORIZATION')
            ?? $this->serverValue('REDIRECT_HTTP_AUTHORIZATION')
            ?? $this->serverValue('Authorization');

        if (is_string($authorization) && $authorization !== '') {
            if (preg_match('/^Bearer\s+(\S+)/i', $authorization, $matches) === 1) {
                return trim($matches[1]);
            }

            $trimmed = trim($authorization);
            if ($trimmed !== '') {
                return $trimmed;
            }
        }

        $directHeader = $this->serverValue('HTTP_X_SEED_TOKEN');
        if (is_string($directHeader) && trim($directHeader) !== '') {
            return trim($directHeader);
        }

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (is_array($headers)) {
                foreach ($headers as $name => $value) {
                    if (is_string($name) && strcasecmp($name, 'X-Seed-Token') === 0 && is_string($value)) {
                        $trimmed = trim($value);
                        if ($trimmed !== '') {
                            return $trimmed;
                        }
                    }
                }
            }
        }

        return null;
    }

    private function resolveBodyToken(): ?string
    {
        $contentType = (string)($this->serverValue('CONTENT_TYPE') ?? $this->serverValue('HTTP_CONTENT_TYPE') ?? '');
        if (stripos($contentType, 'application/json') === false) {
            return null;
        }

        static $parsed = false;
        static $payload = null;

        if (!$parsed) {
            $raw = file_get_contents('php://input');
            if (is_string($raw) && $raw !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $payload = $decoded;
                }
            }
            $parsed = true;
        }

        if (!is_array($payload)) {
            return null;
        }

        $token = $payload['token'] ?? null;
        return is_string($token) ? trim($token) : null;
    }

    private function serverValue(string $key): mixed
    {
        return $_SERVER[$key] ?? null;
    }

    private function envValue(string $key): mixed
    {
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        $value = getenv($key);
        return $value === false ? null : $value;
    }
}
