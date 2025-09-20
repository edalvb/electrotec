<?php
require __DIR__ . '/../bootstrap.php';

use App\Shared\Http\JsonResponse;

/**
 * @OA\Get(
 *   path="/api/health.php",
 *   summary="Health check",
 *   @OA\Response(response=200, description="OK",
 *     @OA\MediaType(mediaType="application/json",
 *       @OA\Schema(ref="#/components/schemas/EnvelopeHealth")
 *     )
 *   )
 * )
 */
JsonResponse::ok(['status' => 'healthy', 'time' => date('c')]);
