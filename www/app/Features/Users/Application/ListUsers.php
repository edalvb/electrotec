<?php
namespace App\Features\Users\Application;

use App\Features\Users\Domain\UserRepository;

final class ListUsers
{
    public function __construct(private UserRepository $repo) {}

    /** @return array<int, array<string, mixed>> */
    public function __invoke(int $limit = 100, int $offset = 0): array
    {
        return $this->repo->listAll($limit, $offset);
    }
}
