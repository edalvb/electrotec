<?php
namespace App\Features\Clients\Application;

use App\Features\Clients\Domain\ClientRepository;

final class ListClients
{
    public function __construct(private ClientRepository $repo) {}

    /** @return array<int, array<string, mixed>> */
    public function __invoke(int $limit = 100, int $offset = 0): array
    {
        return $this->repo->listAll($limit, $offset);
    }
}
