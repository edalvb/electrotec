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
	parameters: [ ],
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
					type: 'object', required: ['nombre', 'ruc'],
					properties: [
						new OA\Property(property: 'nombre', type: 'string', description: 'Nombre del cliente'),
						new OA\Property(property: 'ruc', type: 'string', description: 'RUC del cliente (11 dígitos)', pattern: '^\d{11}$'),
						new OA\Property(property: 'dni', type: 'string', nullable: true, description: 'DNI del cliente'),
						new OA\Property(property: 'email', type: 'string', nullable: true, description: 'Email del cliente'),
						new OA\Property(property: 'celular', type: 'string', nullable: true, description: 'Celular del cliente'),
						new OA\Property(property: 'direccion', type: 'string', nullable: true, description: 'Dirección del cliente'),
					]
				)
			)
		),
		responses: [
			new OA\Response(response: 201, description: 'Creado', content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeClient')),
			new OA\Response(response: 422, description: 'Validación', content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeError')),
		]
	),
	put: new OA\Put(
		summary: 'Actualizar cliente',
		parameters: [
			new OA\Parameter(parameter: 'action', name: 'action', in: 'query', required: true, schema: new OA\Schema(type: 'string', enum: ['update'])),
			new OA\Parameter(parameter: 'id', name: 'id', in: 'query', required: true, schema: new OA\Schema(type: 'string')),
		],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\MediaType(
				mediaType: 'application/json',
				schema: new OA\Schema(
					type: 'object', required: ['nombre', 'ruc'],
					properties: [
						new OA\Property(property: 'nombre', type: 'string'),
						new OA\Property(property: 'ruc', type: 'string', pattern: '^\d{11}$'),
						new OA\Property(property: 'dni', type: 'string', nullable: true),
						new OA\Property(property: 'email', type: 'string', nullable: true),
						new OA\Property(property: 'celular', type: 'string', nullable: true),
						new OA\Property(property: 'direccion', type: 'string', nullable: true),
					]
				)
			)
		),
		responses: [
			new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeClient')),
			new OA\Response(response: 404, description: 'No encontrado', content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeError')),
			new OA\Response(response: 422, description: 'Validación', content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeError')),
		]
	),
	delete: new OA\Delete(
		summary: 'Eliminar cliente',
		parameters: [
			new OA\Parameter(parameter: 'action', name: 'action', in: 'query', required: true, schema: new OA\Schema(type: 'string', enum: ['delete'])),
			new OA\Parameter(parameter: 'id', name: 'id', in: 'query', required: true, schema: new OA\Schema(type: 'string')),
		],
		responses: [
			new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeSuccess')),
			new OA\Response(response: 409, description: 'No permitido si tiene certificados asociados', content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeError')),
		]
	)
)]
final class Clients {}
