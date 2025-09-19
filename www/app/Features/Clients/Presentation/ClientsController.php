<?php
namespace App\Features\Clients\Presentation;

use App\Features\Clients\Application\ListClients;
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
}
