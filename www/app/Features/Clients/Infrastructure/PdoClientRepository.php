<?php
namespace App\Features\Clients\Infrastructure;

use App\Features\Clients\Domain\ClientRepository;
use PDO;

final class PdoClientRepository implements ClientRepository
{
    public function __construct(private PDO $pdo) {}

    public function listAll(int $limit = 100, int $offset = 0): array
    {
        // MySQL no permite bind de LIMIT/OFFSET con prepared statements nativos.
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        $sql = "SELECT id, name, contact_details, created_at FROM clients ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function create(string $id, string $name, ?array $contactDetails): array
    {
        $sql = "INSERT INTO clients (id, name, contact_details, created_at) VALUES (:id, :name, :contact_details, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':name', $name);
        // Para MySQL JSON, pasamos string JSON o NULL
        $json = $contactDetails !== null ? json_encode($contactDetails, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;
        $stmt->bindValue(':contact_details', $json, $json === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->execute();

        $stmt2 = $this->pdo->prepare("SELECT id, name, contact_details, created_at FROM clients WHERE id = :id");
        $stmt2->bindValue(':id', $id);
        $stmt2->execute();
        $row = $stmt2->fetch();
        if (!$row) {
            throw new \RuntimeException('No se pudo recuperar el cliente recién creado');
        }
        return $row;
    }
}
