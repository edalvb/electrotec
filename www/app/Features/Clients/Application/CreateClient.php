<?php
namespace App\Features\Clients\Application;

use App\Features\Clients\Domain\ClientRepository;
use App\Features\Clients\Domain\ClientUserRepository;
use App\Features\Users\Domain\UserRepository;
use App\Shared\Utils\Uuid;
use DomainException;
use PDO;

final class CreateClient
{
    private const DEFAULT_PASSWORD = 'abc123';

    public function __construct(
        private PDO $pdo,
        private ClientRepository $clients,
        private UserRepository $users,
        private ClientUserRepository $clientUsers
    ) {}

    public function __invoke(string $clientId, string $userProfileId, string $name, string $email, ?array $contactDetails): array
    {
        if ($this->clients->emailExists($email)) {
            throw new DomainException('El correo ya está registrado para otro cliente');
        }
        if ($this->users->emailExists($email)) {
            throw new DomainException('El correo ya está registrado para otro usuario');
        }

        $passwordHash = password_hash(self::DEFAULT_PASSWORD, PASSWORD_DEFAULT);

        $this->pdo->beginTransaction();
        try {
            $client = $this->clients->create($clientId, $name, $email, $contactDetails);
            $user = $this->users->create($userProfileId, $name, $email, $passwordHash, 'CLIENT', true);
            $this->clientUsers->attachPrimaryUser(Uuid::v4(), $clientId, $userProfileId, [
                'view_certificates' => true,
                'only_own_certificates' => true,
                'primary' => true,
            ]);
            $this->pdo->commit();
            return [
                'client' => $client,
                'user' => $user,
            ];
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
