<?php
require __DIR__ . '/../bootstrap.php';

use App\Shared\Http\JsonResponse;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;

$base = [
	'status' => 'healthy',
	'time' => date('c'),
];

// Chequeo rápido de extensión PDO si ?pdo=1
if (isset($_GET['pdo'])) {
	if (!class_exists('PDO')) {
		JsonResponse::error('PDO extension missing', 500, [
			'loaded_extensions' => get_loaded_extensions(),
		]);
		return;
	}
	$base['pdo'] = [
		'available_drivers' => \PDO::getAvailableDrivers(),
		'php_version' => PHP_VERSION,
	];
}

// Si se solicita ?db=1, incluir chequeo de base de datos
if (isset($_GET['db'])) {
	try {
		$pdo = (new PdoFactory(new Config()))->create();
		$dbName = (string)($pdo->query('SELECT DATABASE()')->fetchColumn() ?: '');
		$tables = (int)$pdo->query('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()')->fetchColumn();
		$base['db'] = [
			'name' => $dbName,
			'tables' => $tables,
		];
	} catch (\Throwable $e) {
		JsonResponse::error('DB unhealthy', 500, [
			'error' => $e->getMessage(),
		]);
		return;
	}
}

JsonResponse::ok($base);
