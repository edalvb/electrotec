<?php
namespace App\Features\Certificates\Application;

use App\Features\Certificates\Domain\CertificateRepository;

final class ListCertificatesForClientUser
{
    public function __construct(private CertificateRepository $repo) {}

    /** @return array<int, array<string, mixed>> */
    public function __invoke(string $userProfileId, int $limit = 50, int $offset = 0): array
    {
        return $this->repo->listForClientUser($userProfileId, $limit, $offset);
    }
}
