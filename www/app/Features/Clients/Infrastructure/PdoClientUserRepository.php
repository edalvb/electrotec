<?php
namespace App\Features\Clients\Infrastructure;

use App\Features\Clients\Domain\ClientUserRepository;
use PDO;

final class PdoClientUserRepository implements ClientUserRepository
{
    public function __construct(private PDO $pdo) {}

    public function attachPrimaryUser(string $id, string $clientId, string $userProfileId, array $permissions): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO client_users (id, client_id, user_profile_id, permissions, created_at) VALUES (:id, :client_id, :user_profile_id, :permissions, NOW()) ON DUPLICATE KEY UPDATE permissions = VALUES(permissions)');
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':client_id', $clientId);
        $stmt->bindValue(':user_profile_id', $userProfileId);
        $stmt->bindValue(':permissions', json_encode($permissions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $stmt->execute();
    }

    public function findPrimaryUserId(string $clientId): ?string
    {
        $stmt = $this->pdo->prepare('SELECT user_profile_id FROM client_users WHERE client_id = :client_id ORDER BY created_at ASC LIMIT 1');
        $stmt->bindValue(':client_id', $clientId);
        $stmt->execute();
        $value = $stmt->fetchColumn();
        return $value !== false ? (string) $value : null;
    }

    public function findUserIdsByClient(string $clientId): array
    {
        $stmt = $this->pdo->prepare('SELECT user_profile_id FROM client_users WHERE client_id = :client_id');
        $stmt->bindValue(':client_id', $clientId);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_map('strval', $rows ?: []);
    }

    public function detachByClient(string $clientId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM client_users WHERE client_id = :client_id');
        $stmt->bindValue(':client_id', $clientId);
        $stmt->execute();
    }
}
