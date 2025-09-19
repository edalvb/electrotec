<?php
namespace App\Features\Users\Presentation;

use App\Features\Users\Application\ListUsers;
use App\Features\Users\Infrastructure\PdoUserRepository;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;
use App\Shared\Http\JsonResponse;

final class UsersController
{
    public function list(): void
    {
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 100;
        $offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;

        $repo = new PdoUserRepository((new PdoFactory(new Config()))->create());
        $useCase = new ListUsers($repo);
        $data = $useCase($limit, $offset);
        JsonResponse::ok($data);
    }
}
