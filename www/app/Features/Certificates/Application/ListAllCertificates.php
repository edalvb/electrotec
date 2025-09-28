<?php
namespace App\Features\Certificates\Application;

use App\Features\Certificates\Domain\CertificateRepository;

final class ListAllCertificates
{
    public function __construct(private CertificateRepository $repo) {}

    /**
     * @return array{items: array<int, array<string, mixed>>, pagination: array{limit: int, offset: int, total: int}}
     */
    public function __invoke(int $limit = 50, int $offset = 0): array
    {
        $limit = max(1, $limit);
        $offset = max(0, $offset);

        $items = $this->repo->listAll($limit, $offset);
        $total = $this->repo->countAll();

        return [
            'items' => $items,
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
                'total' => $total,
            ],
        ];
    }
}
