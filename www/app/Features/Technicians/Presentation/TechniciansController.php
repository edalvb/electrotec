<?php
namespace App\Features\Technicians\Presentation;

use App\Features\Technicians\Infrastructure\PdoTechnicianRepository;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Auth\AuthMiddleware;
use App\Shared\Config\Config;
use App\Shared\Http\JsonResponse;
use PDO;

final class TechniciansController
{
    public function __construct(private AuthMiddleware $auth) {}

    private function pdo(): PDO { return (new PdoFactory(new Config()))->create(); }

    public function list(): void
    {
        $this->auth->requireAdmin();
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 200;
        $offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;
        $repo = new PdoTechnicianRepository($this->pdo());
        JsonResponse::ok($repo->findAll($limit, $offset));
    }

    public function get(): void
    {
        $this->auth->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { JsonResponse::error('ID inválido', 422); return; }
        $repo = new PdoTechnicianRepository($this->pdo());
        $row = $repo->findById($id);
        if (!$row) { JsonResponse::error('Técnico no encontrado', 404); return; }
        JsonResponse::ok($row);
    }

    public function create(): void
    {
        $this->auth->requireAdmin();
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { JsonResponse::error('Método no permitido', 405); return; }
        $payload = json_decode(file_get_contents('php://input') ?: 'null', true);
        if (!is_array($payload)) { JsonResponse::error('JSON inválido', 400); return; }
        $nombre = trim((string)($payload['nombre_completo'] ?? ''));
        if ($nombre === '') { JsonResponse::error('nombre_completo es requerido', 422); return; }
        $repo = new PdoTechnicianRepository($this->pdo());
        $firma = null;
        if (array_key_exists('firma_base64', $payload)) {
            if ($payload['firma_base64'] !== null && !is_string($payload['firma_base64'])) { JsonResponse::error('firma_base64 debe ser string data URL/base64 o null', 422); return; }
            $firma = $payload['firma_base64'] === null ? null : trim((string)$payload['firma_base64']);
        }
        $created = $repo->create([
            'nombre_completo' => $nombre,
            'cargo' => isset($payload['cargo']) ? (string)$payload['cargo'] : null,
            'path_firma' => isset($payload['path_firma']) ? (string)$payload['path_firma'] : null,
            'firma_base64' => $firma,
        ]);
        JsonResponse::ok($created, 201);
    }

    public function update(): void
    {
        $this->auth->requireAdmin();
        if (!in_array(($_SERVER['REQUEST_METHOD'] ?? 'GET'), ['PUT','PATCH'], true)) { JsonResponse::error('Método no permitido', 405); return; }
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { JsonResponse::error('ID inválido', 422); return; }
        $payload = json_decode(file_get_contents('php://input') ?: 'null', true);
        if (!is_array($payload)) { JsonResponse::error('JSON inválido', 400); return; }
        $repo = new PdoTechnicianRepository($this->pdo());
        $row = $repo->findById($id);
        if (!$row) { JsonResponse::error('Técnico no encontrado', 404); return; }
        $data = [];
        if (array_key_exists('nombre_completo', $payload)) { $v = trim((string)$payload['nombre_completo']); if ($v===''){JsonResponse::error('nombre_completo no puede ser vacío',422);return;} $data['nombre_completo']=$v; }
        if (array_key_exists('cargo', $payload)) { $data['cargo'] = ($payload['cargo'] === null) ? null : trim((string)$payload['cargo']); }
        if (array_key_exists('path_firma', $payload)) { $data['path_firma'] = ($payload['path_firma'] === null) ? null : trim((string)$payload['path_firma']); }
        if (array_key_exists('firma_base64', $payload)) {
            $b64 = $payload['firma_base64'];
            if ($b64 !== null && !is_string($b64)) { JsonResponse::error('firma_base64 debe ser string data URL/base64 o null', 422); return; }
            $data['firma_base64'] = $b64 === null ? null : trim((string)$b64);
        }
        if (!$data) { JsonResponse::error('Sin cambios', 400); return; }
        $updated = $repo->update($id, $data);
        JsonResponse::ok(['technician' => $updated]);
    }

    public function delete(): void
    {
        $this->auth->requireAdmin();
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'DELETE') { JsonResponse::error('Método no permitido', 405); return; }
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { JsonResponse::error('ID inválido', 422); return; }
        $repo = new PdoTechnicianRepository($this->pdo());
        $row = $repo->findById($id);
        if (!$row) { JsonResponse::error('Técnico no encontrado', 404); return; }
        // Validar referencial: evitar borrar si hay certificados
        $stmt = $this->pdo()->prepare('SELECT COUNT(*) FROM certificates WHERE calibrator_id = :id');
        $stmt->execute([':id' => $id]);
        if ((int)$stmt->fetchColumn() > 0) { JsonResponse::error('No se puede eliminar: existen certificados asociados', 409); return; }
        $repo->delete($id);
        JsonResponse::ok(['deleted' => true]);
    }
}
