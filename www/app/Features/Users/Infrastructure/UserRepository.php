<?php
namespace App\Features\Users\Infrastructure;

use App\Features\Users\Domain\User;
use PDO;

final class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Busca un usuario por username
     */
    public function findByUsername(string $username): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->mapRowToUser($row);
    }
    
    /**
     * @deprecated Use findByUsername instead
     */
    public function findByRuc(string $ruc): ?User
    {
        // Mantenemos por compatibilidad, pero ahora busca por username
        return $this->findByUsername($ruc);
    }

    /**
     * Busca un usuario por ID
     */
    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->mapRowToUser($row);
    }

    /**
     * Obtiene todos los usuarios
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM users ORDER BY created_at DESC');
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => $this->mapRowToUser($row), $rows);
    }

    /**
     * Obtiene todos los usuarios
     */
    public function findAllClientes(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => $this->mapRowToUser($row), $rows);
    }

    /**
     * Crea un nuevo usuario
     */
    public function create(array $data): User
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO users (username, password_hash, tipo)
            VALUES (:username, :password_hash, :tipo)
        ');

        $stmt->execute([
            'username' => $data['username'],
            'password_hash' => $data['password_hash'],
            'tipo' => $data['tipo'] ?? 'client',
        ]);

        $id = (int) $this->pdo->lastInsertId();
        return $this->findById($id);
    }

    /**
     * Actualiza un usuario existente
     */
    public function update(int $id, array $data): ?User
    {
        $fields = [];
        $params = ['id' => $id];

        if (isset($data['username'])) {
            $fields[] = 'username = :username';
            $params['username'] = $data['username'];
        }

        if (isset($data['tipo'])) {
            $fields[] = 'tipo = :tipo';
            $params['tipo'] = $data['tipo'];
        }

        if (isset($data['password_hash'])) {
            $fields[] = 'password_hash = :password_hash';
            $params['password_hash'] = $data['password_hash'];
        }

        if (empty($fields)) {
            return $this->findById($id);
        }

        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $this->findById($id);
    }

    /**
     * Elimina un usuario
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Verifica si un username ya existe
     */
    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :username AND id != :id');
            $stmt->execute(['username' => $username, 'id' => $excludeId]);
        } else {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
            $stmt->execute(['username' => $username]);
        }

        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * @deprecated Use usernameExists instead
     */
    public function rucExists(string $ruc, ?int $excludeId = null): bool
    {
        return $this->usernameExists($ruc, $excludeId);
    }

    /**
     * Mapea una fila de la base de datos a un objeto User
     */
    private function mapRowToUser(array $row): User
    {
        return new User(
            id: (int) $row['id'],
            username: $row['username'],
            passwordHash: $row['password_hash'],
            tipo: $row['tipo'],
            createdAt: $row['created_at'],
            updatedAt: $row['updated_at']
        );
    }
}
