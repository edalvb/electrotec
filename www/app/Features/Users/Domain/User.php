<?php
namespace App\Features\Users\Domain;

final class User
{
    public int $id;
    public string $username;
    public string $passwordHash;
    public string $tipo; // 'admin' o 'client'
    public string $createdAt;
    public string $updatedAt;

    public function __construct(
        int $id,
        string $username,
        string $passwordHash,
        string $tipo,
        string $createdAt = '',
        string $updatedAt = ''
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->tipo = $tipo;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'tipo' => $this->tipo,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->tipo === 'admin';
    }

    public function isClient(): bool
    {
        return $this->tipo === 'client';
    }
    
    /**
     * @deprecated Use isClient() instead
     */
    public function isCliente(): bool
    {
        return $this->isClient();
    }
}
