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
	)
)]
final class Certificates {}
