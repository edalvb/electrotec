<?php
namespace App\Features\Equipment\Presentation;

use App\Features\Equipment\Application\CreateEquipment;
use App\Features\Equipment\Application\DeleteEquipment;
use App\Features\Equipment\Application\ListEquipment;
use App\Features\Equipment\Application\ListEquipmentByClientId;
use App\Features\Equipment\Application\UpdateEquipment;
use App\Features\Equipment\Infrastructure\PdoEquipmentRepository;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;
use App\Shared\Http\JsonResponse;
use App\Shared\Utils\Uuid;
use DomainException;
use PDOException;

final class EquipmentController
{
    public function listByClientId(): void
    {
        $clientId = (string)($_GET['client_id'] ?? '');
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 100;
        $offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;

        if ($clientId === '') {
            JsonResponse::error('client_id es requerido', 422);
            return;
        }

        $repo = new PdoEquipmentRepository((new PdoFactory(new Config()))->create());
        $useCase = new ListEquipmentByClientId($repo);
        $data = $useCase($clientId, $limit, $offset);
        JsonResponse::ok($data);
    }

    public function listAll(): void
    {
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 100;
        $offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;
        $repo = new PdoEquipmentRepository((new PdoFactory(new Config()))->create());
        $useCase = new ListEquipment($repo);
        $data = $useCase($limit, $offset);
        JsonResponse::ok($data);
    }

    public function listTypes(): void
    {
        $repo = new PdoEquipmentRepository((new PdoFactory(new Config()))->create());
        $data = $repo->listTypes();
        JsonResponse::ok($data);
    }

    public function createType(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            JsonResponse::error('Método no permitido', 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input') ?: '[]', true);
        if (!is_array($input)) {
            JsonResponse::error('JSON inválido', 400);
            return;
        }

        $name = trim((string)($input['name'] ?? ''));
        if ($name === '') {
            JsonResponse::error('El nombre es obligatorio.', 422);
            return;
        }

        $repo = new PdoEquipmentRepository((new PdoFactory(new Config()))->create());
        try {
            $created = $repo->createType($name);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                JsonResponse::error('Ya existe un tipo con ese nombre.', 409);
                return;
            }
            JsonResponse::error('No se pudo crear el tipo de equipo.', 500, ['error' => $e->getMessage()]);
            return;
        }

        JsonResponse::ok($created, 201);
    }

    public function updateType(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (!in_array($method, ['PUT', 'PATCH', 'POST'], true)) {
            JsonResponse::error('Método no permitido', 405);
            return;
        }

        $raw = file_get_contents('php://input') ?: '[]';
        $input = json_decode($raw, true);
        if (!is_array($input)) {
            JsonResponse::error('JSON inválido', 400);
            return;
        }

        $id = (int)($_GET['id'] ?? ($input['id'] ?? 0));
        $name = trim((string)($input['name'] ?? ''));

        if ($id <= 0 || $name === '') {
            JsonResponse::error('Campos requeridos: id, name', 422);
            return;
        }

        $repo = new PdoEquipmentRepository((new PdoFactory(new Config()))->create());
        try {
            $updated = $repo->updateType($id, $name);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                JsonResponse::error('Ya existe un tipo con ese nombre.', 409);
                return;
            }
            JsonResponse::error('No se pudo actualizar el tipo de equipo.', 500, ['error' => $e->getMessage()]);
            return;
        }

        if ($updated === null) {
            JsonResponse::error('Tipo de equipo no encontrado.', 404);
            return;
        }

        JsonResponse::ok($updated);
    }

    public function deleteType(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (!in_array($method, ['DELETE', 'POST'], true)) {
            JsonResponse::error('Método no permitido', 405);
            return;
        }

        $raw = file_get_contents('php://input') ?: '';
        $input = $raw !== '' ? json_decode($raw, true) : [];
        if (!is_array($input)) {
            $input = [];
        }

        $id = (int)($_GET['id'] ?? ($input['id'] ?? 0));
        if ($id <= 0) {
            JsonResponse::error('Campo requerido: id', 422);
            return;
        }

        $repo = new PdoEquipmentRepository((new PdoFactory(new Config()))->create());
        $result = $repo->deleteType($id);

        if ($result === 'not_found') {
            JsonResponse::error('Tipo de equipo no encontrado.', 404);
            return;
        }

        if ($result === 'in_use') {
            JsonResponse::error('No se puede eliminar el tipo porque está asociado a uno o más equipos.', 409);
            return;
        }

        JsonResponse::ok(['deleted' => true]);
    }

    public function create(): void
    {
        $input = json_decode(file_get_contents('php://input') ?: '[]', true);
        if (!is_array($input)) {
            JsonResponse::error('JSON inválido', 400);
            return;
        }

        $serial = trim((string)($input['serial_number'] ?? ''));
        $brand  = trim((string)($input['brand'] ?? ''));
        $model  = trim((string)($input['model'] ?? ''));
        $typeId = (int)($input['equipment_type_id'] ?? 0);
        $clientIds = [];
        if (isset($input['client_ids']) && is_array($input['client_ids'])) {
            foreach ($input['client_ids'] as $candidate) {
                if (is_string($candidate) && trim($candidate) !== '') {
                    $clientIds[] = trim($candidate);
                }
            }
        } elseif (isset($input['owner_client_id']) && is_string($input['owner_client_id']) && $input['owner_client_id'] !== '') {
            $clientIds[] = trim($input['owner_client_id']);
        }

        if ($serial === '' || $brand === '' || $model === '' || $typeId <= 0) {
            JsonResponse::error('Campos requeridos: serial_number, brand, model, equipment_type_id', 422);
            return;
        }

        $id = Uuid::v4();

        $repo = new PdoEquipmentRepository((new PdoFactory(new Config()))->create());
        $useCase = new CreateEquipment($repo);
        try {
            $created = $useCase($id, $serial, $brand, $model, $typeId, $clientIds);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                JsonResponse::error('Ya existe un equipo con ese número de serie.', 409);
                return;
            }
            JsonResponse::error('No se pudo crear el equipo.', 500, ['error' => $e->getMessage()]);
            return;
        }

        JsonResponse::ok($created, 201);
    }

    public function update(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (!in_array($method, ['PUT', 'PATCH', 'POST'], true)) {
            JsonResponse::error('Método no permitido', 405);
            return;
        }

        $raw = file_get_contents('php://input') ?: '[]';
        $input = json_decode($raw, true);
        if (!is_array($input)) {
            JsonResponse::error('JSON inválido', 400);
            return;
        }

        $id = trim((string)($_GET['id'] ?? ($input['id'] ?? '')));
        $serial = trim((string)($input['serial_number'] ?? ''));
        $brand = trim((string)($input['brand'] ?? ''));
        $model = trim((string)($input['model'] ?? ''));
        $typeId = (int)($input['equipment_type_id'] ?? 0);

        if ($id === '' || $serial === '' || $brand === '' || $model === '' || $typeId <= 0) {
            JsonResponse::error('Campos requeridos: id, serial_number, brand, model, equipment_type_id', 422);
            return;
        }

        $clientIds = null;
        if (array_key_exists('client_ids', $input)) {
            $rawClientIds = $input['client_ids'];
            if ($rawClientIds === null) {
                $clientIds = [];
            } elseif (is_array($rawClientIds)) {
                $clientIds = [];
                foreach ($rawClientIds as $candidate) {
                    if (is_string($candidate) && trim($candidate) !== '') {
                        $clientIds[] = trim($candidate);
                    }
                }
            } else {
                JsonResponse::error('client_ids debe ser un arreglo de ids o null', 422);
                return;
            }
        } elseif (isset($input['owner_client_id']) && is_string($input['owner_client_id'])) {
            $owner = trim($input['owner_client_id']);
            $clientIds = $owner === '' ? [] : [$owner];
        }

        $repo = new PdoEquipmentRepository((new PdoFactory(new Config()))->create());
        $useCase = new UpdateEquipment($repo);

        try {
            $updated = $useCase($id, $serial, $brand, $model, $typeId, $clientIds);
        } catch (DomainException $e) {
            $message = $e->getMessage();
            $status = str_contains(strtolower($message), 'no existe') ? 404 : 500;
            JsonResponse::error($message, $status);
            return;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                JsonResponse::error('Ya existe un equipo con ese número de serie.', 409);
                return;
            }
            JsonResponse::error('No se pudo actualizar el equipo.', 500, ['error' => $e->getMessage()]);
            return;
        }

        JsonResponse::ok($updated);
    }

    public function delete(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (!in_array($method, ['DELETE', 'POST'], true)) {
            JsonResponse::error('Método no permitido', 405);
            return;
        }

        $raw = file_get_contents('php://input') ?: '';
        $input = $raw !== '' ? json_decode($raw, true) : [];
        if (!is_array($input)) {
            $input = [];
        }

        $id = trim((string)($_GET['id'] ?? ($input['id'] ?? '')));
        if ($id === '') {
            JsonResponse::error('Campo requerido: id', 422);
            return;
        }

        $repo = new PdoEquipmentRepository((new PdoFactory(new Config()))->create());
        $useCase = new DeleteEquipment($repo);
        $result = $useCase($id);

        if ($result === 'not_found') {
            JsonResponse::error('Equipo no encontrado.', 404);
            return;
        }

        if ($result === 'has_certificates') {
            JsonResponse::error('No se puede eliminar el equipo porque tiene certificados asociados.', 409);
            return;
        }

        JsonResponse::ok(['deleted' => true]);
    }

}
