<?php
namespace App\Features\Users\Infrastructure;

use App\Features\Users\Domain\UserRepository;
use PDO;

final class PdoUserRepository implements UserRepository
{
    public function __construct(private PDO $pdo) {}

    public function listAll(int $limit = 100, int $offset = 0): array
    {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        $sql = "SELECT id, full_name, email, role, is_active, deleted_at, created_at, updated_at
                FROM user_profiles
                ORDER BY created_at DESC
                LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(string $id, string $fullName, string $email, string $passwordHash, string $role, bool $isActive): array
    {
        $stmt = $this->pdo->prepare('INSERT INTO user_profiles (id, full_name, email, password_hash, signature_image_url, role, is_active, deleted_at) VALUES (:id, :full_name, :email, :password_hash, NULL, :role, :is_active, NULL)');
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':full_name', $fullName);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password_hash', $passwordHash);
        $stmt->bindValue(':role', $role);
        $stmt->bindValue(':is_active', $isActive ? 1 : 0, PDO::PARAM_INT);
        $stmt->execute();
        $user = $this->findById($id);
        if ($user === null) {
            throw new \RuntimeException('No se pudo recuperar el usuario recién creado');
        }
        return $user;
    }

    public function update(string $id, string $fullName, string $email): array
    {
        $stmt = $this->pdo->prepare('UPDATE user_profiles SET full_name = :full_name, email = :email WHERE id = :id');
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':full_name', $fullName);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $this->findById($id);
        if ($user === null) {
            throw new \RuntimeException('No se pudo actualizar el usuario solicitado');
        }
        return $user;
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM user_profiles WHERE id = :id');
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, full_name, email, role, is_active, deleted_at, created_at, updated_at FROM user_profiles WHERE id = :id');
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function emailExists(string $email, ?string $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = $this->pdo->prepare('SELECT COUNT(1) FROM user_profiles WHERE email = :email AND id <> :id');
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':id', $excludeId);
            $stmt->execute();
            return (int) $stmt->fetchColumn() > 0;
        }
        $stmt = $this->pdo->prepare('SELECT COUNT(1) FROM user_profiles WHERE email = :email');
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }
}
