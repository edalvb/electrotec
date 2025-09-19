<?php
namespace App\Features\Certificates\Presentation;

use App\Features\Certificates\Application\ListCertificatesByClientId;
use App\Features\Certificates\Application\ListCertificatesForClientUser;
use App\Features\Certificates\Infrastructure\PdoCertificateRepository;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;
use App\Shared\Http\JsonResponse;

final class CertificatesController
{
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
}
