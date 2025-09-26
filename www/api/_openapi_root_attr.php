<?php
use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(title: 'Electrotec Attr', version: '0.0.1'),
    servers: [new OA\Server(url: 'http://localhost:8082')]
)]
#[OA\PathItem(path: '/api/_ping_attr')]
#[OA\Get(summary: 'Ping Attr', responses: [new OA\Response(response: 200, description: 'OK')])]
final class OpenApiRootAttr {}
