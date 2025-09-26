<?php
namespace App\Docs\Paths;

use OpenApi\Attributes as OA;

#[OA\PathItem(
	path: '/api/clients.php',
	get: new OA\Get(
		summary: 'Listar clientes',
		parameters: [
			new OA\Parameter(parameter: 'action', name: 'action', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['list'])),
			new OA\Parameter(parameter: 'limit', name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1, default: 100)),
			new OA\Parameter(parameter: 'offset', name: 'offset', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 0, default: 0)),
		],
		responses: [
			new OA\Response(
				response: 200,
				description: 'OK',
				content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeClients')
			)
		]
	),
	post: new OA\Post(
		summary: 'Crear cliente',
		parameters: [
			new OA\Parameter(parameter: 'action', name: 'action', in: 'query', required: true, schema: new OA\Schema(type: 'string', enum: ['create']))
		],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\MediaType(
				mediaType: 'application/json',
				schema: new OA\Schema(
					type: 'object', required: ['name'],
					properties: [
						new OA\Property(property: 'name', type: 'string'),
						new OA\Property(property: 'contact_details', type: 'object', nullable: true),
					]
				)
			)
		),
		responses: [
			new OA\Response(response: 201, description: 'Creado', content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeClient')),
			new OA\Response(response: 422, description: 'Validación', content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeError')),
		]
	)
)]
final class Clients {}
