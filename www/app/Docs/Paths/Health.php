<?php
namespace App\Docs\Paths;

use OpenApi\Attributes as OA;

#[OA\PathItem(
	path: '/api/health.php',
	get: new OA\Get(
		summary: 'Health check',
		responses: [
			new OA\Response(
				response: 200,
				description: 'OK',
				content: new OA\JsonContent(ref: '#/components/schemas/EnvelopeHealth')
			)
		]
	)
)]
final class Health {}
