<?php
namespace App\Features\Certificates\Presentation;

use App\Features\Certificates\Application\ListAllCertificates;
use App\Features\Certificates\Application\ListCertificatesByClientId;
use App\Features\Certificates\Application\ListCertificatesForClientUser;
use App\Features\Certificates\Application\CreateCertificate;
use App\Features\Certificates\Infrastructure\PdoCertificateRepository;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;
use App\Shared\Http\JsonResponse;

final class CertificatesController
{
    public function listAll(): void
    {
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 50;
        $offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;

        $repo = new PdoCertificateRepository((new PdoFactory(new Config()))->create());
        $useCase = new ListAllCertificates($repo);
        $data = $useCase($limit, $offset);
        JsonResponse::ok($data);
    }

    public function listByClientId(): void
    {
        $clientId = (string)($_GET['client_id'] ?? '');
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 50;
        $offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;

        if ($clientId === '') {
            JsonResponse::error('client_id es requerido', 422);
            return;
        }

        $repo = new PdoCertificateRepository((new PdoFactory(new Config()))->create());
        $useCase = new ListCertificatesByClientId($repo);
        $data = $useCase($clientId, $limit, $offset);
        JsonResponse::ok($data);
    }

    public function listForClientUser(): void
    {
        $userProfileId = (string)($_GET['user_profile_id'] ?? '');
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 50;
        $offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;

        if ($userProfileId === '') {
            JsonResponse::error('user_profile_id es requerido', 422);
            return;
        }

        $repo = new PdoCertificateRepository((new PdoFactory(new Config()))->create());
        $useCase = new ListCertificatesForClientUser($repo);
        $data = $useCase($userProfileId, $limit, $offset);
        JsonResponse::ok($data);
    }

    public function create(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (!in_array($method, ['POST'], true)) {
            JsonResponse::error('Método no permitido', 405);
            return;
        }

        $raw = file_get_contents('php://input') ?: '{}';
        $input = json_decode($raw, true);
        if (!is_array($input)) {
            JsonResponse::error('JSON inválido', 400);
            return;
        }

        $repo = new PdoCertificateRepository((new PdoFactory(new Config()))->create());
        $useCase = new CreateCertificate($repo);
        try {
            $created = $useCase($input);
            JsonResponse::ok($created, 201);
        } catch (\DomainException $e) {
            JsonResponse::error($e->getMessage(), 422);
        } catch (\Throwable $e) {
            JsonResponse::error('No se pudo crear el certificado.', 500, ['error' => $e->getMessage()]);
        }
    }
}
