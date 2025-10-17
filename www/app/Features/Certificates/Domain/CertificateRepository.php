<?php
namespace App\Features\Certificates\Domain;

interface CertificateRepository
{
    /** @return array<int, array<string, mixed>> */
    public function listAll(int $limit = 50, int $offset = 0): array;

    public function countAll(): int;

    /** @return array<int, array<string, mixed>> */
    public function listByClientId(string $clientId, int $limit = 50, int $offset = 0): array;

    /** @return array<int, array<string, mixed>> */
    public function listForClientUser(string $userProfileId, int $limit = 50, int $offset = 0): array;

    /** @return array<string, mixed>|null */
    public function findByCertificateNumber(string $certificateNumber): ?array;

    /** @return array<string, mixed>|null */
    public function findByIdWithDetails(string $id): ?array;

    /**
     * Crea un nuevo certificado con los datos provistos y devuelve el registro insertado.
     *
     * Debe generar el certificate_number de forma correlativa: {YYYY}-{secuencia}
     * donde la secuencia es incremental por año.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function create(array $data): array;

    /**
     * Actualiza campos editables de un certificado y devuelve el registro actualizado (con detalles cuando sea posible).
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data): array;
}
