<?php
// Genera y sirve el OpenAPI spec escaneando anotaciones PHPDoc con swagger-php
// Uso: GET /api/openapi.php (opcional ?format=json|yaml)

use OpenApi\Annotations as OA;

// Evitar que warnings/notice rompan la salida del JSON/YAML
@ini_set('display_errors', '0');
@ini_set('display_startup_errors', '0');
@error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

require __DIR__ . '/../bootstrap.php';

// Composer autoload si existe
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
}

// Si la librería no está instalada, devolver un error claro en vez de fatal error
if (!class_exists('\\OpenApi\\Generator')) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "OpenApi\\Generator no está disponible.\n";
    echo "Probablemente falte instalar dependencias con Composer.\n\n";
    echo "Solución rápida (dentro del contenedor):\n";
    echo "1) docker compose up -d --build\n";
    echo "2) docker compose exec app composer install\n\n";
    echo "Luego vuelve a abrir /api/openapi.php?format=yaml o ?format=json";
    exit;
}

$format = isset($_GET['format']) ? strtolower((string)$_GET['format']) : 'yaml';
if ($format !== 'yaml' && $format !== 'json') { $format = 'yaml'; }

// Rutas a escanear: app/ y api/
$rootApp = realpath(__DIR__ . '/../app') ?: (__DIR__ . '/../app');
$rootApi = __DIR__;
$scanSources = [$rootApp, $rootApi];

$generatorClass = '\\OpenApi\\Generator';

// Silenciar warnings del validador para no romper el output (se pueden ver en logs)
$logger = class_exists('Psr\\Log\\NullLogger') ? new \Psr\Log\NullLogger() : null;

$options = [
    // Aceptar tanto @oa como @OA sin necesidad de 'use' en cada archivo
    'aliases' => [
        'oa' => 'OpenApi\\Annotations',
        'OA' => 'OpenApi\\Annotations',
    ],
    // Mostrar el spec aunque existan validaciones pendientes
    'validate' => false,
];
if ($logger) { $options['logger'] = $logger; }

// Usar TokenAnalyser para descubrir anotaciones incluso si la clase no es autoloadable
// Nota: TokenAnalyser puede fallar con ciertas versiones; dejamos el analizador por defecto (ReflectionAnalyser)
// if (class_exists('OpenApi\\Analysers\\TokenAnalyser')) {
//     $options['analyser'] = new \OpenApi\Analysers\TokenAnalyser();
// }

$openapi = call_user_func([$generatorClass, 'scan'], $scanSources, $options);

$appHost = $_ENV['APP_HOST'] ?? getenv('APP_HOST') ?: 'localhost';
$appPort = (string)($_ENV['APP_PORT'] ?? getenv('APP_PORT') ?: '8082');
$portSuffix = $appPort === '80' ? '' : ':' . $appPort;
$serverUrl = sprintf('http://%s%s', $appHost, $portSuffix);

$openapi->servers = [
    new OA\Server([
        'url' => $serverUrl,
        'description' => 'Entorno local (configurable via APP_PORT)',
    ]),
];

if ($format === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    echo $openapi->toJson();
} else {
    header('Content-Type: application/yaml; charset=utf-8');
    echo $openapi->toYaml();
}
