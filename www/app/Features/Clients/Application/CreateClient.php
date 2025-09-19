<?php
namespace App\Features\Clients\Application;

use App\Features\Clients\Domain\ClientRepository;

final class CreateClient
{
    public function __construct(private ClientRepository $repo) {}

    /**
     * @param string $id
     * @param string $name
     * @param array<string,mixed>|null $contactDetails
     * @return array<string,mixed>
     */
    public function __invoke(string $id, string $name, ?array $contactDetails): array
    {
        return $this->repo->create($id, $name, $contactDetails);
    }
}
