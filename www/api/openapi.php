<?php
// Genera y sirve el OpenAPI spec escaneando anotaciones PHPDoc con swagger-php
// Uso: GET /api/openapi.php (opcional ?format=json|yaml)

require __DIR__ . '/../bootstrap.php';

// Composer autoload si existe
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
}

use OpenApi\Generator;

$format = isset($_GET['format']) ? strtolower((string)$_GET['format']) : 'yaml';
if ($format !== 'yaml' && $format !== 'json') { $format = 'yaml'; }

// Directorios a escanear: código de app y endpoints api
$scanDirs = [
    realpath(__DIR__ . '/../app') ?: __DIR__ . '/../app',
    __DIR__,
];

$openapi = Generator::scan($scanDirs);

if ($format === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    echo $openapi->toJson();
} else {
    header('Content-Type: application/yaml; charset=utf-8');
    echo $openapi->toYaml();
}
