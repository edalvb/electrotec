<?php
namespace App\Features\Certificates\Domain;

final class Certificate
{
    public string $id;
    public string $certificateNumber;
    public string $equipmentId;
    public string $calibratorId;
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
        string $calibratorId,
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
        $this->calibratorId = $calibratorId;
        $this->calibrationDate = $calibrationDate;
        $this->nextCalibrationDate = $nextCalibrationDate;
        $this->results = $results;
        $this->labConditions = $labConditions;
        $this->pdfUrl = $pdfUrl;
        $this->clientId = $clientId;
    }
}
