<?php
namespace App\Features\Users\Domain;

interface UserRepository
{
    /** @return array<int, array<string, mixed>> */
    public function listAll(int $limit = 100, int $offset = 0): array;
}
