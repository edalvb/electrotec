<?php
namespace App\Features\Certificates\Application;

use App\Features\Certificates\Domain\CertificateRepository;
use DomainException;

final class UpdateCertificate
{
    public function __construct(private CertificateRepository $repo) {}

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function __invoke(string $id, array $payload): array
    {
        $id = trim($id);
        if ($id === '') { throw new DomainException('id es requerido'); }

        // Validar campos editables
        $allowedKeys = [
            'calibration_date', 'next_calibration_date',
            'resultados', 'resultados_distancia', 'environmental_conditions',
            'service_type', 'observations', 'status',
            'is_calibration', 'is_maintenance',
        ];

        $updateData = [];
        foreach ($allowedKeys as $k) {
            if (array_key_exists($k, $payload)) {
                $updateData[$k] = $payload[$k];
            }
        }

        if (isset($updateData['calibration_date']) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$updateData['calibration_date'])) {
            throw new DomainException('Formato de fecha inválido en calibration_date');
        }
        if (isset($updateData['next_calibration_date']) && $updateData['next_calibration_date'] !== null && $updateData['next_calibration_date'] !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$updateData['next_calibration_date'])) {
            throw new DomainException('Formato de fecha inválido en next_calibration_date');
        }

        if (!method_exists($this->repo, 'update')) {
            throw new DomainException('Repositorio no soporta actualización');
        }

        return $this->repo->update($id, $updateData);
    }
}
