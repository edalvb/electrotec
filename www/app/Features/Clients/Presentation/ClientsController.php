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
use App\Shared\Auth\JwtService;
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

    public function get(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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
        $client = $clients->findById($id);
        if (!$client) {
            JsonResponse::error('Cliente no encontrado', 404);
            return;
        }
        JsonResponse::ok($client);
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
            $result = $useCase($nombre, $ruc, $dni, $email, $celular, $direccion);
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

        // Obtener el cliente existente para mantener el user_id
        $existing = $clients->findById($id);
        if (!$existing) {
            JsonResponse::error('Cliente no encontrado', 404);
            return;
        }

        $useCase = new UpdateClient($pdo, $clients);

        try {
            $client = $useCase($id, $existing['user_id'], $nombre, $ruc, $dni, $email, $celular, $direccion);
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

    /**
     * GET /api/clients.php?action=me
     * Devuelve el cliente asociado al usuario autenticado
     */
    public function me(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
            JsonResponse::error('Método no permitido', 405);
            return;
        }

        $jwt = new JwtService();
        $user = $jwt->getCurrentUser();
        if (!$user) {
            JsonResponse::error('No autorizado', 401);
            return;
        }

        $pdo = $this->pdo();
        $clients = new PdoClientRepository($pdo);
        $client = $clients->findByUserId((int)$user->id);
        if (!$client) {
            JsonResponse::error('Cliente no encontrado para el usuario', 404);
            return;
        }
        JsonResponse::ok($client);
    }

    /**
     * PUT /api/clients.php?action=updateMe
     * Actualiza datos del cliente asociado al usuario autenticado
     */
    public function updateMe(): void
    {
        if (!in_array(($_SERVER['REQUEST_METHOD'] ?? 'GET'), ['PUT','PATCH'], true)) {
            JsonResponse::error('Método no permitido', 405);
            return;
        }

        $jwt = new JwtService();
        $user = $jwt->getCurrentUser();
        if (!$user) {
            JsonResponse::error('No autorizado', 401);
            return;
        }

        $raw = file_get_contents('php://input') ?: '{}';
        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            JsonResponse::error('JSON inválido', 400);
            return;
        }

        $pdo = $this->pdo();
        $clients = new PdoClientRepository($pdo);
        $client = $clients->findByUserId((int)$user->id);
        if (!$client) {
            JsonResponse::error('Cliente no encontrado', 404);
            return;
        }

        $nombre = trim((string)($payload['nombre'] ?? ''));
        if ($nombre === '') {
            JsonResponse::error('El nombre es obligatorio', 422);
            return;
        }

        $ruc = trim((string)($payload['ruc'] ?? ($client['ruc'] ?? '')));
        if ($ruc === '') {
            JsonResponse::error('El RUC es obligatorio', 422);
            return;
        }
        if (!preg_match('/^\d{11}$/', $ruc)) {
            JsonResponse::error('El RUC debe tener 11 dígitos', 422);
            return;
        }

        $dni = trim((string)($payload['dni'] ?? '')) ?: null;
        $email = trim((string)($payload['email'] ?? '')) ?: null;
        $celular = trim((string)($payload['celular'] ?? '')) ?: null;
        $direccion = trim((string)($payload['direccion'] ?? '')) ?: null;

        $updated = $clients->update(
            (string)$client['id'],
            (int)$user->id,
            $nombre,
            $ruc,
            $dni,
            $email,
            $celular,
            $direccion
        );

        JsonResponse::ok($updated);
    }
}
