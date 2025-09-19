<?php
namespace App\Features\Clients\Domain;

interface ClientRepository
{
    /** @return array<int, array<string, mixed>> */
    public function listAll(int $limit = 100, int $offset = 0): array;
}
