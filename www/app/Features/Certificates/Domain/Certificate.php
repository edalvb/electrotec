<?php
namespace App\Features\Certificates\Domain;

final class Certificate
{
    public string $id;
    public string $certificateNumber;
    public string $equipmentId;
    public string $technicianId;
    public string $calibrationDate; // YYYY-MM-DD
    public string $nextCalibrationDate; // YYYY-MM-DD
    public array $results;
    public ?array $labConditions;
    public ?string $pdfUrl;
    public ?string $clientId;

    public function __construct(
        string $id,
        string $certificateNumber,
        string $equipmentId,
        string $technicianId,
        string $calibrationDate,
        string $nextCalibrationDate,
        array $results,
        ?array $labConditions,
        ?string $pdfUrl,
        ?string $clientId
    ) {
        $this->id = $id;
        $this->certificateNumber = $certificateNumber;
        $this->equipmentId = $equipmentId;
        $this->technicianId = $technicianId;
        $this->calibrationDate = $calibrationDate;
        $this->nextCalibrationDate = $nextCalibrationDate;
        $this->results = $results;
        $this->labConditions = $labConditions;
        $this->pdfUrl = $pdfUrl;
        $this->clientId = $clientId;
    }
}
