<?php
namespace App\Docs\Paths;

use OpenApi\Attributes as OA;

#[OA\PathItem(
    path: '/api/seed.php',
    post: new OA\Post(
        summary: 'Semilla de datos base para entornos de desarrollo',
        parameters: [
            new OA\Parameter(
                name: 'token',
                in: 'query',
                required: true,
                description: 'Token secreto configurado en SEED_TOKEN para autorizar la ejecución.',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Semilla ejecutada correctamente.',
                content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeSeed')
            ),
            new OA\Response(
                response: 403,
                description: 'Token inválido o ausente.',
                content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeError')
            ),
            new OA\Response(
                response: 405,
                description: 'Método no permitido.',
                content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeError')
            ),
        ]
    )
)]
final class Seed {}
