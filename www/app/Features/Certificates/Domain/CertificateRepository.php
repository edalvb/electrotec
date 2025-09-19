<?php
namespace App\Features\Certificates\Domain;

interface CertificateRepository
{
    /** @return array<int, array<string, mixed>> */
    public function listByClientId(string $clientId, int $limit = 50, int $offset = 0): array;

    /** @return array<int, array<string, mixed>> */
    public function listForClientUser(string $userProfileId, int $limit = 50, int $offset = 0): array;

    /** @return array<string, mixed>|null */
    public function findByCertificateNumber(string $certificateNumber): ?array;
}
