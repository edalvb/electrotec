<?php
namespace App\Features\Certificates\Presentation;

use App\Features\Certificates\Application\ListAllCertificates;
use App\Features\Certificates\Application\ListCertificatesByClientId;
use App\Features\Certificates\Application\ListCertificatesForClientUser;
use App\Features\Certificates\Application\CreateCertificate;
use App\Features\Certificates\Application\UpdateCertificate;
use App\Features\Certificates\Infrastructure\PdoCertificateRepository;
use App\Features\Clients\Infrastructure\PdoClientRepository;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;
use App\Shared\Http\JsonResponse;
use App\Shared\Auth\JwtService;

final class CertificatesController
{
    public function find(): void
    {
        $id = (string)($_GET['id'] ?? '');
        if ($id === '') { JsonResponse::error('id es requerido', 422); return; }

        $repo = new PdoCertificateRepository((new PdoFactory(new Config()))->create());
        if (!method_exists($repo, 'findByIdWithDetails')) {
            JsonResponse::error('Método no disponible', 500);
            return;
        }
        $data = $repo->findByIdWithDetails($id);
        if ($data === null) { JsonResponse::error('Certificado no encontrado', 404); return; }
        JsonResponse::ok($data);
    }
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

    // Datos complementarios para vista HTML
    public function getConditions(): void
    {
        $id = (string)($_GET['id'] ?? '');
        if ($id === '') { JsonResponse::error('id es requerido', 422); return; }
        $pdo = (new PdoFactory(new Config()))->create();
        $stmt = $pdo->prepare('SELECT * FROM condiciones_ambientales WHERE id_certificado = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        JsonResponse::ok($row);
    }

    public function getResults(): void
    {
        $id = (string)($_GET['id'] ?? '');
        if ($id === '') { JsonResponse::error('id es requerido', 422); return; }
        $pdo = (new PdoFactory(new Config()))->create();
        $stmt = $pdo->prepare('SELECT * FROM resultados WHERE id_certificado = :id ORDER BY id ASC');
        $stmt->execute([':id' => $id]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        JsonResponse::ok($rows);
    }

    public function getDistanceResults(): void
    {
        $id = (string)($_GET['id'] ?? '');
        if ($id === '') { JsonResponse::error('id es requerido', 422); return; }
        $pdo = (new PdoFactory(new Config()))->create();
        $stmt = $pdo->prepare('SELECT * FROM resultados_distancia WHERE id_certificado = :id ORDER BY id_resultado ASC');
        $stmt->execute([':id' => $id]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        JsonResponse::ok($rows);
    }

    /**
     * Lista certificados para el cliente asociado al usuario autenticado (portal de clientes)
     */
    public function listForMe(): void
    {
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 50;
        $offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;

        $jwt = new JwtService();
        $user = $jwt->getCurrentUser();
        if (!$user) { JsonResponse::error('No autorizado', 401); return; }

        $pdo = (new PdoFactory(new Config()))->create();
        $clientsRepo = new PdoClientRepository($pdo);
        $client = $clientsRepo->findByUserId((int)$user->id);
        if (!$client || empty($client['id'])) { JsonResponse::ok([]); return; }

        $certRepo = new PdoCertificateRepository($pdo);
        $useCase = new ListCertificatesByClientId($certRepo);
        $data = $useCase((string)$client['id'], $limit, $offset);
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

        // calibrator_id ahora referencia tecnico.id; si no viene, se requerirá explícitamente en la capa de aplicación

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

    public function update(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (!in_array($method, ['PUT','PATCH'], true)) {
            JsonResponse::error('Método no permitido', 405);
            return;
        }

        $id = (string)($_GET['id'] ?? '');
        if ($id === '') { JsonResponse::error('id es requerido', 422); return; }

        $raw = file_get_contents('php://input') ?: '{}';
        $input = json_decode($raw, true);
        if (!is_array($input)) {
            JsonResponse::error('JSON inválido', 400);
            return;
        }

        $repo = new PdoCertificateRepository((new PdoFactory(new Config()))->create());
        if (!method_exists($repo, 'update')) {
            JsonResponse::error('Operación de actualización no disponible', 500);
            return;
        }
        $useCase = new UpdateCertificate($repo);
        try {
            $updated = $useCase($id, $input);
            JsonResponse::ok($updated);
        } catch (\DomainException $e) {
            JsonResponse::error($e->getMessage(), 422);
        } catch (\Throwable $e) {
            JsonResponse::error('No se pudo actualizar el certificado.', 500, ['error' => $e->getMessage()]);
        }
    }
}
