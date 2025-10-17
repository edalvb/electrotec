<?php
namespace App\Docs\Paths;

use OpenApi\Attributes as OA;

#[OA\PathItem(
    path: '/api/technicians.php',
    get: new OA\Get(
        summary: 'Listar técnicos',
        parameters: [
            new OA\Parameter(parameter: 'action', name: 'action', in: 'query', required: true, schema: new OA\Schema(type: 'string', enum: ['list','get'])),
            new OA\Parameter(parameter: 'id', name: 'id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'ok', type: 'boolean', example: true),
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Technician')),
                ]
            )),
        ]
    ),
    post: new OA\Post(
        summary: 'Crear técnico',
        parameters: [new OA\Parameter(parameter: 'action', name: 'action', in: 'query', required: true, schema: new OA\Schema(type: 'string', enum: ['create']))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
            new OA\Property(property: 'nombre_completo', type: 'string'),
            new OA\Property(property: 'cargo', type: 'string', nullable: true),
            new OA\Property(property: 'path_firma', type: 'string', nullable: true, description: 'Ruta de imagen (obsoleto, usar firma_base64)'),
            new OA\Property(property: 'firma_base64', type: 'string', nullable: true, description: 'Imagen de firma como data URL/Base64 (p. ej. data:image/png;base64,...)'),
        ])),
        responses: [new OA\Response(response: 201, description: 'Creado')]
    ),
    put: new OA\Put(
        summary: 'Actualizar técnico',
        parameters: [
            new OA\Parameter(parameter: 'action', name: 'action', in: 'query', required: true, schema: new OA\Schema(type: 'string', enum: ['update'])),
            new OA\Parameter(parameter: 'id', name: 'id', in: 'query', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
            new OA\Property(property: 'nombre_completo', type: 'string'),
            new OA\Property(property: 'cargo', type: 'string', nullable: true),
            new OA\Property(property: 'path_firma', type: 'string', nullable: true, description: 'Ruta de imagen (obsoleto, usar firma_base64)'),
            new OA\Property(property: 'firma_base64', type: 'string', nullable: true, description: 'Imagen de firma como data URL/Base64 (p. ej. data:image/png;base64,...)'),
        ])),
        responses: [new OA\Response(response: 200, description: 'OK')]
    ),
    delete: new OA\Delete(
        summary: 'Eliminar técnico',
        parameters: [
            new OA\Parameter(parameter: 'action', name: 'action', in: 'query', required: true, schema: new OA\Schema(type: 'string', enum: ['delete'])),
            new OA\Parameter(parameter: 'id', name: 'id', in: 'query', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )
)]
final class Technicians {}
