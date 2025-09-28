<?php
namespace App\Features\Clients\Application;

use App\Features\Clients\Domain\ClientRepository;
use App\Features\Clients\Domain\ClientUserRepository;
use App\Features\Users\Domain\UserRepository;
use DomainException;
use PDO;

final class DeleteClient
{
    public function __construct(
        private PDO $pdo,
        private ClientRepository $clients,
        private UserRepository $users,
        private ClientUserRepository $clientUsers
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
            $userIds = $this->clientUsers->findUserIdsByClient($clientId);
            $this->clientUsers->detachByClient($clientId);
            $this->clients->delete($clientId);
            foreach ($userIds as $userId) {
                $this->users->delete($userId);
            }
            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
