<?php
namespace App\Shared\Http;

final class JsonResponse
{
    public static function ok($data = null, int $status = 200): void
    {
        self::send(['ok' => true, 'data' => $data], $status);
    }

    public static function error(string $message, int $status = 400, $details = null): void
    {
        self::send(['ok' => false, 'message' => $message, 'details' => $details], $status);
    }

    private static function send(array $payload, int $status): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
