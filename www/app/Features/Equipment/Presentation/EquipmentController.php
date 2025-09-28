<?php
namespace App\Features\Equipment\Presentation;

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

    public function listTypes(): void
    {
        $repo = new PdoEquipmentRepository((new PdoFactory(new Config()))->create());
        $data = $repo->listTypes();
        JsonResponse::ok($data);
    }

    public function create(): void
    {
        // Espera JSON body: { serial_number, brand, model, equipment_type_id, owner_client_id }
        $input = json_decode(file_get_contents('php://input') ?: '[]', true);
        if (!is_array($input)) {
            JsonResponse::error('JSON inválido', 400);
            return;
        }

        $serial = trim((string)($input['serial_number'] ?? ''));
        $brand  = trim((string)($input['brand'] ?? ''));
        $model  = trim((string)($input['model'] ?? ''));
        $typeId = (int)($input['equipment_type_id'] ?? 0);
        $owner  = isset($input['owner_client_id']) && $input['owner_client_id'] !== '' ? (string)$input['owner_client_id'] : null;

        if ($serial === '' || $brand === '' || $model === '' || $typeId <= 0) {
            JsonResponse::error('Campos requeridos: serial_number, brand, model, equipment_type_id', 422);
            return;
        }

        // UUID simple (versión sin dependencia externa)
    $id = Uuid::v4();

        $repo = new PdoEquipmentRepository((new PdoFactory(new Config()))->create());
        $useCase = new CreateEquipment($repo);
        $created = $useCase($id, $serial, $brand, $model, $typeId, $owner);
        JsonResponse::ok($created, 201);
    }

}
