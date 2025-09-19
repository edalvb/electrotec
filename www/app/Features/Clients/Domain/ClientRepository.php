<?php
namespace App\Features\Clients\Domain;

interface ClientRepository
{
    /** @return array<int, array<string, mixed>> */
    public function listAll(int $limit = 100, int $offset = 0): array;

    /**
     * Crea un cliente y devuelve el registro creado.
     * @param string $id UUID v4
     * @param string $name Nombre del cliente
     * @param array<string,mixed>|null $contactDetails Datos de contacto (se serializan a JSON)
     * @return array<string, mixed>
     */
    public function create(string $id, string $name, ?array $contactDetails): array;
}
