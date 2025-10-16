<?php
namespace App\Features\Clients\Infrastructure;

use App\Features\Clients\Domain\ClientRepository;
use PDO;

final class PdoClientRepository implements ClientRepository
{
    public function __construct(private PDO $pdo) {}

    public function listAll(int $limit = 100, int $offset = 0): array
    {
        $limit = max(1, (int) $limit);
        $offset = max(0, (int) $offset);
        $sql = "SELECT id, user_id, nombre, ruc, dni, email, celular, direccion, created_at, updated_at FROM clients ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll();
        return $rows;
    }

    public function create(string $id, int $userId, string $nombre, string $ruc, ?string $dni = null, ?string $email = null, ?string $celular = null, ?string $direccion = null): array
    {
        $sql = "INSERT INTO clients (id, user_id, nombre, ruc, dni, email, celular, direccion, created_at) 
                VALUES (:id, :user_id, :nombre, :ruc, :dni, :email, :celular, :direccion, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':ruc', $ruc);
        $stmt->bindValue(':dni', $dni, $dni === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, $email === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':celular', $celular, $celular === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':direccion', $direccion, $direccion === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->execute();
        $client = $this->findById($id);
        if ($client === null) {
            throw new \RuntimeException('No se pudo recuperar el cliente recién creado');
        }
        return $client;
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, user_id, nombre, ruc, dni, email, celular, direccion, created_at, updated_at FROM clients WHERE id = :id');
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function update(string $id, int $userId, string $nombre, string $ruc, ?string $dni = null, ?string $email = null, ?string $celular = null, ?string $direccion = null): array
    {
        $stmt = $this->pdo->prepare('UPDATE clients SET user_id = :user_id, nombre = :nombre, ruc = :ruc, dni = :dni, email = :email, celular = :celular, direccion = :direccion WHERE id = :id');
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':ruc', $ruc);
        $stmt->bindValue(':dni', $dni, $dni === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, $email === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':celular', $celular, $celular === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':direccion', $direccion, $direccion === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->execute();
        $client = $this->findById($id);
        if ($client === null) {
            throw new \RuntimeException('No se pudo actualizar el cliente solicitado');
        }
        return $client;
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM clients WHERE id = :id');
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    }

    public function rucExists(string $ruc, ?string $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = $this->pdo->prepare('SELECT COUNT(1) FROM clients WHERE ruc = :ruc AND id <> :id');
            $stmt->bindValue(':ruc', $ruc);
            $stmt->bindValue(':id', $excludeId);
            $stmt->execute();
            return (int) $stmt->fetchColumn() > 0;
        }
        $stmt = $this->pdo->prepare('SELECT COUNT(1) FROM clients WHERE ruc = :ruc');
        $stmt->bindValue(':ruc', $ruc);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    public function hasCertificates(string $id): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(1) FROM certificates WHERE client_id = :id AND (deleted_at IS NULL)');
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }
}
