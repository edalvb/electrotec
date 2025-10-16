<?php
namespace App\Features\Clients\Domain;

interface ClientRepository
{
    /** @return array<int, array<string, mixed>> */
    public function listAll(int $limit = 100, int $offset = 0): array;

    /**
     * Crea un cliente y devuelve el registro creado.
     * @param string $id
     * @param int $userId
     * @param string $nombre
     * @param string $ruc
     * @param string|null $dni
     * @param string|null $email
     * @param string|null $celular
     * @param string|null $direccion
     * @return array<string, mixed>
     */
    public function create(string $id, int $userId, string $nombre, string $ruc, ?string $dni = null, ?string $email = null, ?string $celular = null, ?string $direccion = null): array;

    public function findById(string $id): ?array;

    public function update(string $id, int $userId, string $nombre, string $ruc, ?string $dni = null, ?string $email = null, ?string $celular = null, ?string $direccion = null): array;

    public function delete(string $id): void;

    public function rucExists(string $ruc, ?string $excludeId = null): bool;

    public function hasCertificates(string $id): bool;
}
