<?php
namespace App\Features\Certificates\Application;

use App\Features\Certificates\Domain\CertificateRepository;
use App\Shared\Utils\Uuid;
use DomainException;

final class CreateCertificate
{
    public function __construct(private CertificateRepository $repo) {}

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function __invoke(array $payload): array
    {
        $equipmentId = trim((string)($payload['equipment_id'] ?? ''));
        $clientId = trim((string)($payload['client_id'] ?? ''));
        $calibrationDate = trim((string)($payload['calibration_date'] ?? ''));
        $nextCalibrationDate = trim((string)($payload['next_calibration_date'] ?? ''));

        if ($equipmentId === '' || $clientId === '' || $calibrationDate === '') {
            throw new DomainException('Campos requeridos: equipment_id, client_id, calibration_date');
        }

        // Validaciones básicas de fecha (YYYY-MM-DD)
        foreach ([['calibration_date', $calibrationDate], ['next_calibration_date', $nextCalibrationDate]] as [$field, $value]) {
            if ($value !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                throw new DomainException("Formato de fecha inválido en {$field}. Use YYYY-MM-DD");
            }
        }

        // TODO: technician_id debería venir del usuario autenticado; por ahora, permitir inyección opcional
        $technicianId = trim((string)($payload['technician_id'] ?? ''));
        if ($technicianId === '') {
            // Fallback: en entornos de demo, permitir un UUID fijo o error; aquí generamos uno para no bloquear
            $technicianId = Uuid::v4();
        }

        $id = Uuid::v4();

        $data = [
            'id' => $id,
            'equipment_id' => $equipmentId,
            'technician_id' => $technicianId,
            'calibration_date' => $calibrationDate,
            'next_calibration_date' => $nextCalibrationDate !== '' ? $nextCalibrationDate : $calibrationDate,
            'results' => $payload['results'] ?? [
                'service_type' => $payload['service_type'] ?? null,
                'observations' => $payload['observations'] ?? null,
                'status' => $payload['status'] ?? null,
            ],
            'lab_conditions' => isset($payload['environmental_conditions']) ? $payload['environmental_conditions'] : null,
            'pdf_url' => $payload['pdf_url'] ?? null,
            'client_id' => $clientId,
        ];

        return $this->repo->create($data);
    }
}
