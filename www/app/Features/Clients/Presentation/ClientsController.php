<?php
namespace App\Features\Clients\Presentation;

use App\Features\Clients\Application\CreateClient;
use App\Features\Clients\Application\DeleteClient;
use App\Features\Clients\Application\ListClients;
use App\Features\Clients\Application\UpdateClient;
use App\Features\Clients\Infrastructure\PdoClientRepository;
use App\Features\Clients\Infrastructure\PdoClientUserRepository;
use App\Features\Users\Infrastructure\PdoUserRepository;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;
use App\Shared\Http\JsonResponse;
use App\Shared\Utils\Uuid;
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

        $name = trim((string)($payload['name'] ?? ''));
        if ($name === '') {
            JsonResponse::error('El nombre es obligatorio', 422);
            return;
        }

        $email = trim((string)($payload['email'] ?? ''));
        if ($email === '') {
            JsonResponse::error('El correo es obligatorio', 422);
            return;
        }

        $contact = $payload['contact_details'] ?? null;
        if ($contact !== null && !is_array($contact)) {
            JsonResponse::error('contact_details debe ser un objeto', 422);
            return;
        }

        if (is_array($contact)) {
            unset($contact['email']);
        }

        $pdo = $this->pdo();
        $clients = new PdoClientRepository($pdo);
        $users = new PdoUserRepository($pdo);
        $clientUsers = new PdoClientUserRepository($pdo);
        $useCase = new CreateClient($pdo, $clients, $users, $clientUsers);

        $clientId = Uuid::v4();
        $userProfileId = Uuid::v4();

        try {
            $result = $useCase($clientId, $userProfileId, $name, $email, $contact);
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

        $name = trim((string)($payload['name'] ?? ''));
        $email = trim((string)($payload['email'] ?? ''));
        if ($name === '' || $email === '') {
            JsonResponse::error('Nombre y correo son obligatorios', 422);
            return;
        }

        $contact = $payload['contact_details'] ?? null;
        if ($contact !== null && !is_array($contact)) {
            JsonResponse::error('contact_details debe ser un objeto', 422);
            return;
        }
        if (is_array($contact)) {
            unset($contact['email']);
        }

        $pdo = $this->pdo();
        $clients = new PdoClientRepository($pdo);
        $users = new PdoUserRepository($pdo);
        $clientUsers = new PdoClientUserRepository($pdo);
        $useCase = new UpdateClient($pdo, $clients, $users, $clientUsers);

        try {
            $client = $useCase($id, $name, $email, $contact);
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
        $users = new PdoUserRepository($pdo);
        $clientUsers = new PdoClientUserRepository($pdo);
        $useCase = new DeleteClient($pdo, $clients, $users, $clientUsers);

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
