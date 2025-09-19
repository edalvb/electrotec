<?php
require __DIR__ . '/../bootstrap.php';

use App\Shared\Http\JsonResponse;

JsonResponse::ok(['status' => 'healthy', 'time' => date('c')]);
