<?php
namespace App\Features\Equipment\Presentation;

use App\Features\Equipment\Application\ListEquipment;
use App\Features\Equipment\Application\ListEquipmentByClientId;
use App\Features\Equipment\Application\CreateEquipment;
use App\Features\Equipment\Infrastructure\PdoEquipmentRepository;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;
use App\Shared\Http\JsonResponse;
use App\Shared\Utils\Uuid;

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
        $created = $useCase($id, $serial, $brand, $model, $typeId, $clientIds);
        JsonResponse::ok($created, 201);
    }

}
