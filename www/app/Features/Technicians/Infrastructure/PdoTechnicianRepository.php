<?php
namespace App\Features\Technicians\Infrastructure;

use App\Features\Technicians\Domain\Technician;
use PDO;

final class PdoTechnicianRepository
{
    public function __construct(private PDO $pdo) {}

    /** @return array<int, array<string,mixed>> */
    public function findAll(int $limit = 200, int $offset = 0): array
    {
        $limit = max(1, (int)$limit); $offset = max(0, (int)$offset);
        $stmt = $this->pdo->query("SELECT id, nombre_completo, cargo, path_firma, firma_base64 FROM tecnico ORDER BY nombre_completo ASC LIMIT {$limit} OFFSET {$offset}");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, nombre_completo, cargo, path_firma, firma_base64 FROM tecnico WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** @param array{nombre_completo:string,cargo?:?string,path_firma?:?string,firma_base64?:?string} $data */
    public function create(array $data): array
    {
        $stmt = $this->pdo->prepare('INSERT INTO tecnico (nombre_completo, cargo, path_firma, firma_base64) VALUES (:n, :c, :p, :b64)');
        $stmt->execute([
            ':n' => $data['nombre_completo'],
            ':c' => $data['cargo'] ?? null,
            ':p' => $data['path_firma'] ?? null,
            ':b64' => $data['firma_base64'] ?? null,
        ]);
        $id = (int)$this->pdo->lastInsertId();
        return $this->findById($id) ?? ['id' => $id] + $data;
    }

    /** @param array<string,mixed> $data */
    public function update(int $id, array $data): ?array
    {
        $fields = []; $params = [':id' => $id];
        if (array_key_exists('nombre_completo', $data)) { $fields[] = 'nombre_completo = :n'; $params[':n'] = (string)$data['nombre_completo']; }
        if (array_key_exists('cargo', $data)) { $fields[] = 'cargo = :c'; $params[':c'] = $data['cargo'] !== '' ? (string)$data['cargo'] : null; }
        if (array_key_exists('path_firma', $data)) { $fields[] = 'path_firma = :p'; $params[':p'] = $data['path_firma'] !== '' ? (string)$data['path_firma'] : null; }
        if (array_key_exists('firma_base64', $data)) {
            $fields[] = 'firma_base64 = :b64';
            $val = $data['firma_base64'];
            if ($val !== null && $val !== '' && !is_string($val)) { $val = (string)$val; }
            // Permitir data URL completa o base64 simple; no forzamos formato pero preservamos el valor
            $params[':b64'] = ($val === '' ? null : $val);
        }
        if (!$fields) { return $this->findById($id); }
        $sql = 'UPDATE tecnico SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM tecnico WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
