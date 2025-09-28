<?php
namespace App\Features\Clients\Domain;

interface ClientUserRepository
{
    public function attachPrimaryUser(string $id, string $clientId, string $userProfileId, array $permissions): void;

    public function findPrimaryUserId(string $clientId): ?string;

    public function findUserIdsByClient(string $clientId): array;

    public function detachByClient(string $clientId): void;
}
