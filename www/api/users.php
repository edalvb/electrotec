<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Users\Presentation\UsersController;
use App\Shared\Http\JsonResponse;

$controller = new UsersController();

/**
 * @OA\Get(
 *   path="/api/users.php",
 *   summary="Listar usuarios",
 *   @OA\Parameter(name="action", in="query", required=false, @OA\Schema(type="string", enum={"list"})),
 *   @OA\Parameter(name="limit", in="query", required=false, @OA\Schema(type="integer", minimum=1, default=100)),
 *   @OA\Parameter(name="offset", in="query", required=false, @OA\Schema(type="integer", minimum=0, default=0)),
 *   @OA\Response(response=200, description="OK",
 *     @OA\MediaType(mediaType="application/json",
 *       @OA\Schema(ref="#/components/schemas/EnvelopeUsers")
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
        default:
            JsonResponse::error('Acción no válida', 404);
    }
} catch (Throwable $e) {
    JsonResponse::error('Error inesperado', 500, ['error' => $e->getMessage()]);
}
