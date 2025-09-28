<?php
namespace App\Features\Clients\Domain;

interface ClientRepository
{
    /** @return array<int, array<string, mixed>> */
    public function listAll(int $limit = 100, int $offset = 0): array;

    /**
     * Crea un cliente y devuelve el registro creado.
     * @param string $id
     * @param string $name
     * @param string $email
     * @param array<string,mixed>|null $contactDetails
     * @return array<string, mixed>
     */
    public function create(string $id, string $name, string $email, ?array $contactDetails): array;

    public function findById(string $id): ?array;

    public function update(string $id, string $name, string $email, ?array $contactDetails): array;

    public function delete(string $id): void;

    public function emailExists(string $email, ?string $excludeId = null): bool;

    public function hasCertificates(string $id): bool;
}
