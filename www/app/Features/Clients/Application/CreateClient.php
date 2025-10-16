<?php
namespace App\Features\Clients\Application;

use App\Features\Clients\Domain\ClientRepository;
use DomainException;
use PDO;

final class CreateClient
{
    public function __construct(
        private PDO $pdo,
        private ClientRepository $clients
    ) {}

    public function __invoke(int $userId, string $nombre, string $ruc, ?string $dni = null, ?string $email = null, ?string $celular = null, ?string $direccion = null): array
    {
        // Verificar que el RUC no exista
        if ($this->clients->rucExists($ruc)) {
            throw new DomainException('El RUC ya está registrado para otro cliente');
        }

        $this->pdo->beginTransaction();
        try {
            // Generar un ID único para el cliente
            $clientId = bin2hex(random_bytes(16));
            
            $client = $this->clients->create($clientId, $userId, $nombre, $ruc, $dni, $email, $celular, $direccion);
            
            $this->pdo->commit();
            return [
                'client' => $client
            ];
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
