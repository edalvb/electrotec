<?php
namespace App\Docs\Paths;

use OpenApi\Attributes as OA;

#[OA\PathItem(
	path: '/api/certificates.php',
	get: new OA\Get(
		summary: 'Listar certificados',
		parameters: [
			new OA\Parameter(parameter: 'action', name: 'action', in: 'query', required: true, schema: new OA\Schema(type: 'string', enum: ['listByClientId','listForClientUser'])),
			new OA\Parameter(parameter: 'client_id', name: 'client_id', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'uuid')),
			new OA\Parameter(parameter: 'user_profile_id', name: 'user_profile_id', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'uuid')),
			new OA\Parameter(parameter: 'limit', name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1, default: 50)),
			new OA\Parameter(parameter: 'offset', name: 'offset', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 0, default: 0)),
		],
		responses: [
			new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeCertificates')),
			new OA\Response(response: 422, description: 'Parámetros inválidos', content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeError')),
		]
	),
	post: new OA\Post(
		summary: 'Crear certificado',
		parameters: [
			new OA\Parameter(parameter: 'action', name: 'action', in: 'query', required: true, schema: new OA\Schema(type: 'string', enum: ['create']))
		],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				properties: [
					new OA\Property(property: 'equipment_id', type: 'string', format: 'uuid'),
					new OA\Property(property: 'client_id', type: 'string', format: 'uuid'),
					new OA\Property(property: 'calibration_date', type: 'string', format: 'date'),
					new OA\Property(property: 'next_calibration_date', type: 'string', format: 'date', nullable: true),
					new OA\Property(property: 'results', type: 'object', nullable: true),
					new OA\Property(property: 'environmental_conditions', type: 'object', nullable: true),
					new OA\Property(property: 'calibrator_id', type: 'string', format: 'uuid', nullable: true, description: 'ID del usuario que realizó la calibración (normalmente un Administrador).'),
					new OA\Property(property: 'technician_id', type: 'string', format: 'uuid', nullable: true, description: 'DEPRECATED. Usar calibrator_id. Aceptado temporalmente para compatibilidad.'),
				]
			)
		),
		responses: [
			new OA\Response(response: 201, description: 'Creado', content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeCertificate')),
			new OA\Response(response: 422, description: 'Parámetros inválidos', content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeError'))
		]
	)
)]
final class Certificates {}
