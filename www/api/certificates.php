<?php
require __DIR__ . '/../bootstrap.php';

use App\Features\Certificates\Presentation\CertificatesController;
use App\Shared\Http\JsonResponse;

$controller = new CertificatesController();

/**
 * @OA\Get(
 *   path="/api/certificates.php",
 *   summary="Listar certificados",
 *   @OA\Parameter(name="action", in="query", required=true, @OA\Schema(type="string", enum={"listByClientId","listForClientUser"})),
 *   @OA\Parameter(name="client_id", in="query", required=false, @OA\Schema(type="string", format="uuid")),
 *   @OA\Parameter(name="user_profile_id", in="query", required=false, @OA\Schema(type="string", format="uuid")),
 *   @OA\Parameter(name="limit", in="query", required=false, @OA\Schema(type="integer", minimum=1, default=50)),
 *   @OA\Parameter(name="offset", in="query", required=false, @OA\Schema(type="integer", minimum=0, default=0)),
 *   @OA\Response(response=200, description="OK",
 *     @OA\MediaType(mediaType="application/json",
 *       @OA\Schema(ref="#/components/schemas/EnvelopeCertificates")
 *     )
 *   ),
 *   @OA\Response(response=422, description="Parámetros inválidos",
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
        case 'listForClientUser':
            $controller->listForClientUser();
            break;
        default:
            JsonResponse::error('Acción no válida', 404);
    }
} catch (Throwable $e) {
    JsonResponse::error('Error inesperado', 500, ['error' => $e->getMessage()]);
}
