<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Equipment\Presentation\EquipmentController;
use App\Shared\Http\JsonResponse;

$controller = new EquipmentController();

/**
 * @OA\Get(
 *   path="/api/equipment.php",
 *   summary="Listar equipos o tipos",
 *   @OA\Parameter(name="action", in="query", required=true, @OA\Schema(type="string", enum={"listByClientId","listTypes"})),
 *   @OA\Parameter(name="client_id", in="query", required=false, @OA\Schema(type="string", format="uuid")),
 *   @OA\Parameter(name="limit", in="query", required=false, @OA\Schema(type="integer", minimum=1, default=100)),
 *   @OA\Parameter(name="offset", in="query", required=false, @OA\Schema(type="integer", minimum=0, default=0)),
 *   @OA\Response(response=200, description="OK",
 *     @OA\MediaType(mediaType="application/json",
 *       @OA\Schema(oneOf={
 *         @OA\Schema(ref="#/components/schemas/EnvelopeEquipmentList"),
 *         @OA\Schema(ref="#/components/schemas/EnvelopeEquipmentTypes")
 *       })
 *     )
 *   ),
 *   @OA\Response(response=422, description="Parámetros inválidos",
 *     @OA\MediaType(mediaType="application/json",
 *       @OA\Schema(ref="#/components/schemas/EnvelopeError")
 *     )
 *   )
 * )
 *
 * @OA\Post(
 *   path="/api/equipment.php",
 *   summary="Crear equipo",
 *   @OA\Parameter(name="action", in="query", required=true, @OA\Schema(type="string", enum={"create"})),
 *   @OA\RequestBody(required=true,
 *     @OA\MediaType(mediaType="application/json",
 *       @OA\Schema(type="object", required={"serial_number","brand","model","equipment_type_id"},
 *         @OA\Property(property="serial_number", type="string"),
 *         @OA\Property(property="brand", type="string"),
 *         @OA\Property(property="model", type="string"),
 *         @OA\Property(property="equipment_type_id", type="integer", minimum=1),
 *         @OA\Property(property="owner_client_id", type="string", format="uuid", nullable=true)
 *       )
 *     )
 *   ),
 *   @OA\Response(response=201, description="Creado",
 *     @OA\MediaType(mediaType="application/json",
 *       @OA\Schema(ref="#/components/schemas/EnvelopeEquipment")
 *     )
 *   ),
 *   @OA\Response(response=422, description="Validación",
 *     @OA\MediaType(mediaType="application/json",
 *       @OA\Schema(ref="#/components/schemas/EnvelopeError")
 *     )
 *   )
 * )
 */
$action = $_GET['action'] ?? 'listByClientId';

try {
    switch ($action) {
        case 'listByClientId':
            $controller->listByClientId();
            break;
        case 'listTypes':
            $controller->listTypes();
            break;
        case 'create':
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
                JsonResponse::error('Método no permitido', 405);
                break;
            }
            $controller->create();
            break;
        default:
            JsonResponse::error('Acción no válida', 404);
    }
} catch (Throwable $e) {
    JsonResponse::error('Error inesperado', 500, ['error' => $e->getMessage()]);
}
