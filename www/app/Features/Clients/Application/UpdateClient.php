<?php
namespace App\Features\Clients\Application;

use App\Features\Clients\Domain\ClientRepository;
use App\Features\Clients\Domain\ClientUserRepository;
use App\Features\Users\Domain\UserRepository;
use DomainException;
use PDO;

final class UpdateClient
{
    public function __construct(
        private PDO $pdo,
        private ClientRepository $clients,
        private UserRepository $users,
        private ClientUserRepository $clientUsers
    ) {}

    public function __invoke(string $clientId, string $name, string $email, ?array $contactDetails): array
    {
        $existing = $this->clients->findById($clientId);
        if ($existing === null) {
            throw new DomainException('El cliente no existe');
        }
        if ($this->clients->emailExists($email, $clientId)) {
            throw new DomainException('El correo ya está registrado para otro cliente');
        }
        $primaryUserId = $this->clientUsers->findPrimaryUserId($clientId);
        if ($this->users->emailExists($email, $primaryUserId)) {
            throw new DomainException('El correo ya está registrado para otro usuario');
        }

        $this->pdo->beginTransaction();
        try {
            $client = $this->clients->update($clientId, $name, $email, $contactDetails);
            if ($primaryUserId !== null) {
                $this->users->update($primaryUserId, $name, $email);
            }
            $this->pdo->commit();
            return $client;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
