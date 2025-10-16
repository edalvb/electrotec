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

    public function __invoke(string $nombre, string $ruc, ?string $dni = null, ?string $email = null, ?string $celular = null, ?string $direccion = null): array
    {
        // Verificar que el RUC no exista
        if ($this->clients->rucExists($ruc)) {
            throw new DomainException('El RUC ya está registrado para otro cliente');
        }

        $this->pdo->beginTransaction();
        try {
            // Crear un usuario con username y password igual al RUC
            $stmt = $this->pdo->prepare('
                INSERT INTO users (username, password_hash, tipo)
                VALUES (:username, :password_hash, :tipo)
            ');
            $stmt->execute([
                'username' => $ruc,
                'password_hash' => password_hash($ruc, PASSWORD_DEFAULT),
                'tipo' => 'client'
            ]);
            $userId = (int) $this->pdo->lastInsertId();

            // Generar un ID único para el cliente
            $clientId = bin2hex(random_bytes(16));
            
            $client = $this->clients->create($clientId, $userId, $nombre, $ruc, $dni, $email, $celular, $direccion);
            
            $this->pdo->commit();
            return [
                'client' => $client,
                'user_created' => true
            ];
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
