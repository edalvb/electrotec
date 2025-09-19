<?php
namespace App\Features\Clients\Presentation;

use App\Features\Clients\Application\ListClients;
use App\Features\Clients\Application\CreateClient;
use App\Features\Clients\Infrastructure\PdoClientRepository;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;
use App\Shared\Http\JsonResponse;

final class ClientsController
{
    public function list(): void
    {
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 100;
        $offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;

        $repo = new PdoClientRepository((new PdoFactory(new Config()))->create());
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

        $contact = $payload['contact_details'] ?? null;
        if ($contact !== null && !is_array($contact)) {
            JsonResponse::error('contact_details debe ser un objeto', 422);
            return;
        }

        $repo = new PdoClientRepository((new PdoFactory(new Config()))->create());
        $useCase = new CreateClient($repo);
        $id = self::uuidv4();
        $created = $useCase($id, $name, $contact);
        JsonResponse::ok($created, 201);
    }

    private static function uuidv4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
