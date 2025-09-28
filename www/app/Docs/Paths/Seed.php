<?php
namespace App\Docs\Paths;

use OpenApi\Attributes as OA;

#[OA\PathItem(
    path: '/api/seed.php',
    post: new OA\Post(
        summary: 'Semilla de datos base para entornos de desarrollo',
        description: 'Crea/actualiza el esquema principal (tablas e índices) y luego inserta datos de ejemplo idempotentes.',
        parameters: [
            new OA\Parameter(
                name: 'token',
                in: 'query',
                required: false,
                description: 'Token secreto configurado en SEED_TOKEN para autorizar la ejecución.',
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'X-Seed-Token',
                in: 'header',
                required: false,
                description: 'Alternativa para enviar el token mediante encabezado personalizado.',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: false,
            description: 'También puedes enviar el token en el cuerpo JSON como {"token": "..."}.',
            content: new OA\JsonContent(ref: '#/components/schemas/SeedTokenRequest')
        ),
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
