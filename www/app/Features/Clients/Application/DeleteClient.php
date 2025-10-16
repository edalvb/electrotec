<?php
namespace App\Features\Clients\Application;

use App\Features\Clients\Domain\ClientRepository;
use DomainException;
use PDO;

final class DeleteClient
{
    public function __construct(
        private PDO $pdo,
        private ClientRepository $clients
    ) {}

    public function __invoke(string $clientId): void
    {
        $existing = $this->clients->findById($clientId);
        if ($existing === null) {
            throw new DomainException('El cliente no existe');
        }
        if ($this->clients->hasCertificates($clientId)) {
            throw new DomainException('No se puede eliminar el cliente porque tiene certificados asociados');
        }

        $this->pdo->beginTransaction();
        try {
            $this->clients->delete($clientId);
            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
