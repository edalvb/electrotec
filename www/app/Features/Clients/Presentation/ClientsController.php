<?php
namespace App\Features\Clients\Presentation;

use App\Features\Clients\Application\CreateClient;
use App\Features\Clients\Application\DeleteClient;
use App\Features\Clients\Application\ListClients;
use App\Features\Clients\Application\UpdateClient;
use App\Features\Clients\Infrastructure\PdoClientRepository;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;
use App\Shared\Http\JsonResponse;
use DomainException;
use PDO;

final class ClientsController
{
    public function list(): void
    {
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 100;
        $offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;

        $pdo = $this->pdo();
        $repo = new PdoClientRepository($pdo);
        $useCase = new ListClients($repo);
        $data = $useCase($limit, $offset);
        JsonResponse::ok($data);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            JsonResponse::error('Método no permitido', 405);
            return;
        }

        $raw = file_get_contents('php://input');
        $payload = json_decode($raw ?: 'null', true);
        if (!is_array($payload)) {
            JsonResponse::error('JSON inválido', 400);
            return;
        }

        $userId = isset($payload['user_id']) ? (int)$payload['user_id'] : 0;
        if ($userId <= 0) {
            JsonResponse::error('El user_id es obligatorio', 422);
            return;
        }

        $nombre = trim((string)($payload['nombre'] ?? ''));
        if ($nombre === '') {
            JsonResponse::error('El nombre es obligatorio', 422);
            return;
        }

        $ruc = trim((string)($payload['ruc'] ?? ''));
        if ($ruc === '') {
            JsonResponse::error('El RUC es obligatorio', 422);
            return;
        }

        // Validar formato de RUC (11 dígitos)
        if (!preg_match('/^\d{11}$/', $ruc)) {
            JsonResponse::error('El RUC debe tener 11 dígitos', 422);
            return;
        }

        $dni = trim((string)($payload['dni'] ?? '')) ?: null;
        $email = trim((string)($payload['email'] ?? '')) ?: null;
        $celular = trim((string)($payload['celular'] ?? '')) ?: null;
        $direccion = trim((string)($payload['direccion'] ?? '')) ?: null;

        $pdo = $this->pdo();
        $clients = new PdoClientRepository($pdo);
        $useCase = new CreateClient($pdo, $clients);

        try {
            $result = $useCase($userId, $nombre, $ruc, $dni, $email, $celular, $direccion);
            JsonResponse::ok($result, 201);
        } catch (DomainException $e) {
            JsonResponse::error($e->getMessage(), 409);
        }
    }

    public function update(): void
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'PATCH'], true)) {
            JsonResponse::error('Método no permitido', 405);
            return;
        }

        $id = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
        if ($id === '') {
            JsonResponse::error('El id es obligatorio', 400);
            return;
        }

        $raw = file_get_contents('php://input');
        $payload = json_decode($raw ?: 'null', true);
        if (!is_array($payload)) {
            JsonResponse::error('JSON inválido', 400);
            return;
        }

        $userId = isset($payload['user_id']) ? (int)$payload['user_id'] : 0;
        if ($userId <= 0) {
            JsonResponse::error('El user_id es obligatorio', 422);
            return;
        }

        $nombre = trim((string)($payload['nombre'] ?? ''));
        if ($nombre === '') {
            JsonResponse::error('El nombre es obligatorio', 422);
            return;
        }

        $ruc = trim((string)($payload['ruc'] ?? ''));
        if ($ruc === '') {
            JsonResponse::error('El RUC es obligatorio', 422);
            return;
        }

        // Validar formato de RUC (11 dígitos)
        if (!preg_match('/^\d{11}$/', $ruc)) {
            JsonResponse::error('El RUC debe tener 11 dígitos', 422);
            return;
        }

        $dni = trim((string)($payload['dni'] ?? '')) ?: null;
        $email = trim((string)($payload['email'] ?? '')) ?: null;
        $celular = trim((string)($payload['celular'] ?? '')) ?: null;
        $direccion = trim((string)($payload['direccion'] ?? '')) ?: null;

        $pdo = $this->pdo();
        $clients = new PdoClientRepository($pdo);
        $useCase = new UpdateClient($pdo, $clients);

        try {
            $client = $useCase($id, $userId, $nombre, $ruc, $dni, $email, $celular, $direccion);
            JsonResponse::ok(['client' => $client]);
        } catch (DomainException $e) {
            JsonResponse::error($e->getMessage(), 409);
        }
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            JsonResponse::error('Método no permitido', 405);
            return;
        }

        $id = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
        if ($id === '') {
            JsonResponse::error('El id es obligatorio', 400);
            return;
        }

        $pdo = $this->pdo();
        $clients = new PdoClientRepository($pdo);
        $useCase = new DeleteClient($pdo, $clients);

        try {
            $useCase($id);
            JsonResponse::ok(['deleted' => true]);
        } catch (DomainException $e) {
            JsonResponse::error($e->getMessage(), 409);
        }
    }

    private function pdo(): PDO
    {
        return (new PdoFactory(new Config()))->create();
    }
}
