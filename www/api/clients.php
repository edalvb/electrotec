<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Clients\Presentation\ClientsController;
use App\Shared\Http\JsonResponse;

$controller = new ClientsController();

/**
 * @OA\Get(
 *   path="/api/clients.php",
 *   summary="Listar clientes",
 *   @OA\Parameter(name="action", in="query", required=false, @OA\Schema(type="string", enum={"list"})),
 *   @OA\Parameter(name="limit", in="query", required=false, @OA\Schema(type="integer", minimum=1, default=100)),
 *   @OA\Parameter(name="offset", in="query", required=false, @OA\Schema(type="integer", minimum=0, default=0)),
 *   @OA\Response(response=200, description="OK",
 *     @OA\MediaType(mediaType="application/json",
 *       @OA\Schema(ref="#/components/schemas/EnvelopeClients")
 *     )
 *   )
 * )
 *
 * @OA\Post(
 *   path="/api/clients.php",
 *   summary="Crear cliente",
 *   @OA\Parameter(name="action", in="query", required=true, @OA\Schema(type="string", enum={"create"})),
 *   @OA\RequestBody(required=true,
 *     @OA\MediaType(mediaType="application/json",
 *       @OA\Schema(type="object", required={"name"},
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="contact_details", type="object", nullable=true)
 *       )
 *     )
 *   ),
 *   @OA\Response(response=201, description="Creado",
 *     @OA\MediaType(mediaType="application/json",
 *       @OA\Schema(ref="#/components/schemas/EnvelopeClient")
 *     )
 *   ),
 *   @OA\Response(response=422, description="Validación",
 *     @OA\MediaType(mediaType="application/json",
 *       @OA\Schema(ref="#/components/schemas/EnvelopeError")
 *     )
 *   )
 * )
 */
$action = $_GET['action'] ?? 'list';

try {
    switch ($action) {
        case 'list':
            $controller->list();
            break;
        case 'create':
            $controller->create();
            break;
        default:
            JsonResponse::error('Acción no válida', 404);
    }
} catch (Throwable $e) {
    JsonResponse::error('Error inesperado', 500, ['error' => $e->getMessage()]);
}
