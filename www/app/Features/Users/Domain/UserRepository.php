<?php
namespace App\Features\Users\Domain;

interface UserRepository
{
    /** @return array<int, array<string, mixed>> */
    public function listAll(int $limit = 100, int $offset = 0): array;

    public function create(string $id, string $fullName, string $email, string $passwordHash, string $role, bool $isActive): array;

    public function update(string $id, string $fullName, string $email): array;

    public function delete(string $id): void;

    public function findById(string $id): ?array;

    public function emailExists(string $email, ?string $excludeId = null): bool;
}
