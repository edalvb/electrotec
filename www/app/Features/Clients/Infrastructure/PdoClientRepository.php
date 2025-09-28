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
        $sql = "SELECT id, name, email, contact_details, created_at FROM clients ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll();
        return array_map(fn (array $row) => $this->hydrate($row), $rows);
    }

    public function create(string $id, string $name, string $email, ?array $contactDetails): array
    {
        $sql = "INSERT INTO clients (id, name, email, contact_details, created_at) VALUES (:id, :name, :email, :contact_details, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':contact_details', $this->encodeContact($contactDetails), $contactDetails === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->execute();
        $client = $this->findById($id);
        if ($client === null) {
            throw new \RuntimeException('No se pudo recuperar el cliente recién creado');
        }
        return $client;
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, email, contact_details, created_at FROM clients WHERE id = :id');
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function update(string $id, string $name, string $email, ?array $contactDetails): array
    {
        $stmt = $this->pdo->prepare('UPDATE clients SET name = :name, email = :email, contact_details = :contact_details WHERE id = :id');
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':contact_details', $this->encodeContact($contactDetails), $contactDetails === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
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

    public function emailExists(string $email, ?string $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = $this->pdo->prepare('SELECT COUNT(1) FROM clients WHERE email = :email AND id <> :id');
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':id', $excludeId);
            $stmt->execute();
            return (int) $stmt->fetchColumn() > 0;
        }
        $stmt = $this->pdo->prepare('SELECT COUNT(1) FROM clients WHERE email = :email');
        $stmt->bindValue(':email', $email);
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

    private function hydrate(array $row): array
    {
        if (array_key_exists('contact_details', $row) && is_string($row['contact_details'])) {
            $decoded = json_decode($row['contact_details'], true);
            $row['contact_details'] = is_array($decoded) ? $decoded : null;
        }
        return $row;
    }

    private function encodeContact(?array $contactDetails): ?string
    {
        if ($contactDetails === null) {
            return null;
        }
        return json_encode($contactDetails, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
