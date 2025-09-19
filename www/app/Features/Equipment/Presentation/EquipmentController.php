<?php
namespace App\Features\Equipment\Presentation;

use App\Features\Equipment\Application\ListEquipmentByClientId;
use App\Features\Equipment\Infrastructure\PdoEquipmentRepository;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;
use App\Shared\Http\JsonResponse;

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
}
