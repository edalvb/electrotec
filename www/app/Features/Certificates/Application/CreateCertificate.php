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

        // ID del calibrador: aceptar calibrator_id y, por compatibilidad, technician_id
        // Debe ser INT (users.id)
        $rawCalibrator = $payload['calibrator_id'] ?? ($payload['technician_id'] ?? null);
        if ($rawCalibrator === null || $rawCalibrator === '') {
            throw new DomainException('calibrator_id es requerido y debe referir al usuario autenticado.');
        }
        if (is_string($rawCalibrator)) {
            $rawCalibrator = trim($rawCalibrator);
        }
        if (!is_numeric($rawCalibrator)) {
            throw new DomainException('calibrator_id debe ser numérico (id de usuario).');
        }
        $calibratorId = (int)$rawCalibrator;
        if ($calibratorId <= 0) {
            throw new DomainException('calibrator_id inválido.');
        }

        $id = Uuid::v4();

        $data = [
            'id' => $id,
            'equipment_id' => $equipmentId,
            'calibrator_id' => $calibratorId,
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
