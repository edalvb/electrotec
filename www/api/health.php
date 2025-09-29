<?php
require __DIR__ . '/../bootstrap.php';

use App\Shared\Http\JsonResponse;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;

// Si se solicita ?db=1, incluir chequeo de base de datos
if (isset($_GET['db'])) {
	try {
		$pdo = (new PdoFactory(new Config()))->create();
		$dbName = (string)($pdo->query('SELECT DATABASE()')->fetchColumn() ?: '');
		$tables = (int)$pdo->query('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()')->fetchColumn();
		JsonResponse::ok([
			'status' => 'healthy',
			'time' => date('c'),
			'db' => [
				'name' => $dbName,
				'tables' => $tables,
			],
		]);
		return;
	} catch (\Throwable $e) {
		JsonResponse::error('DB unhealthy', 500, [
			'error' => $e->getMessage(),
		]);
		return;
	}
}

JsonResponse::ok(['status' => 'healthy', 'time' => date('c')]);
