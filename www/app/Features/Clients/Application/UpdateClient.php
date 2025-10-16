<?php
namespace App\Features\Clients\Application;

use App\Features\Clients\Domain\ClientRepository;
use DomainException;
use PDO;

final class UpdateClient
{
    public function __construct(
        private PDO $pdo,
        private ClientRepository $clients
    ) {}

    public function __invoke(string $clientId, int $userId, string $nombre, string $ruc, ?string $dni = null, ?string $email = null, ?string $celular = null, ?string $direccion = null): array
    {
        $existing = $this->clients->findById($clientId);
        if ($existing === null) {
            throw new DomainException('El cliente no existe');
        }
        
        // Verificar que el RUC no exista (excluyendo el cliente actual)
        if ($this->clients->rucExists($ruc, $clientId)) {
            throw new DomainException('El RUC ya está registrado para otro cliente');
        }

        $this->pdo->beginTransaction();
        try {
            $client = $this->clients->update($clientId, $userId, $nombre, $ruc, $dni, $email, $celular, $direccion);
            $this->pdo->commit();
            return $client;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
