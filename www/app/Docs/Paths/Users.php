<?php
namespace App\Docs\Paths;

use OpenApi\Attributes as OA;

#[OA\PathItem(
	path: '/api/users.php',
	get: new OA\Get(
		summary: 'Listar usuarios',
		parameters: [
			new OA\Parameter(parameter: 'action', name: 'action', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['list'])),
			new OA\Parameter(parameter: 'limit', name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1, default: 100)),
			new OA\Parameter(parameter: 'offset', name: 'offset', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 0, default: 0)),
		],
		responses: [
			new OA\Response(
				response: 200,
				description: 'OK',
				content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeUsers')
			)
		]
	)
)]
final class Users {}
