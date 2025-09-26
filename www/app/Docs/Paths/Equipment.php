<?php
namespace App\Docs\Paths;

use OpenApi\Attributes as OA;

#[OA\PathItem(
	path: '/api/equipment.php',
	get: new OA\Get(
		summary: 'Listar equipos o tipos',
		parameters: [
			new OA\Parameter(parameter: 'action', name: 'action', in: 'query', required: true, schema: new OA\Schema(type: 'string', enum: ['listByClientId','listTypes'])),
			new OA\Parameter(parameter: 'client_id', name: 'client_id', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'uuid')),
			new OA\Parameter(parameter: 'limit', name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1, default: 100)),
			new OA\Parameter(parameter: 'offset', name: 'offset', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 0, default: 0)),
		],
		responses: [
			new OA\Response(
				response: 200,
				description: 'OK',
				content: new OA\JsonContent(oneOf: [
					new OA\Schema(ref: '#/components/schemas/EnvelopeEquipmentList'),
					new OA\Schema(ref: '#/components/schemas/EnvelopeEquipmentTypes'),
				])
			),
			new OA\Response(response: 422, description: 'Parámetros inválidos', content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeError')),
		]
	),
	post: new OA\Post(
		summary: 'Crear equipo',
		parameters: [
			new OA\Parameter(parameter: 'action', name: 'action', in: 'query', required: true, schema: new OA\Schema(type: 'string', enum: ['create']))
		],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\MediaType(
				mediaType: 'application/json',
				schema: new OA\Schema(
					type: 'object', required: ['serial_number','brand','model','equipment_type_id'],
					properties: [
						new OA\Property(property: 'serial_number', type: 'string'),
						new OA\Property(property: 'brand', type: 'string'),
						new OA\Property(property: 'model', type: 'string'),
						new OA\Property(property: 'equipment_type_id', type: 'integer', minimum: 1),
						new OA\Property(property: 'owner_client_id', type: 'string', format: 'uuid', nullable: true),
					]
				)
			)
		),
		responses: [
			new OA\Response(response: 201, description: 'Creado', content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeEquipment')),
			new OA\Response(response: 422, description: 'Validación', content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeError')),
		]
	)
)]
final class Equipment {}
