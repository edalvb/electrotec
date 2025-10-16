<?php
namespace App\Features\Dashboard\Infrastructure;

use App\Features\Dashboard\Domain\DashboardRepository;
use PDO;

final class PdoDashboardRepository implements DashboardRepository
{
    public function __construct(private PDO $pdo) {}

    public function getOverview(): array
    {
        $sql = "WITH last_cert AS (
                SELECT c.*
                FROM certificates c
                JOIN (
                    SELECT equipment_id, MAX(calibration_date) AS max_cal_date
                    FROM certificates
                    WHERE deleted_at IS NULL
                    GROUP BY equipment_id
                ) lc ON lc.equipment_id = c.equipment_id AND lc.max_cal_date = c.calibration_date
                WHERE c.deleted_at IS NULL
            ), equipment_state AS (
                SELECT e.id AS equipment_id, lc.next_calibration_date
                FROM equipment e
                LEFT JOIN last_cert lc ON lc.equipment_id = e.id
            ), equipment_metrics AS (
                SELECT
                    COUNT(*) AS total_equipment,
                    SUM(CASE WHEN next_calibration_date IS NULL THEN 1 ELSE 0 END) AS without_certificate,
                    SUM(CASE WHEN next_calibration_date IS NOT NULL AND next_calibration_date < CURDATE() THEN 1 ELSE 0 END) AS overdue,
                    SUM(CASE WHEN next_calibration_date IS NOT NULL AND next_calibration_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS due_30,
                    SUM(CASE WHEN next_calibration_date IS NOT NULL AND next_calibration_date BETWEEN DATE_ADD(CURDATE(), INTERVAL 31 DAY) AND DATE_ADD(CURDATE(), INTERVAL 60 DAY) THEN 1 ELSE 0 END) AS due_60,
                    SUM(CASE WHEN next_calibration_date IS NOT NULL AND next_calibration_date BETWEEN DATE_ADD(CURDATE(), INTERVAL 61 DAY) AND DATE_ADD(CURDATE(), INTERVAL 90 DAY) THEN 1 ELSE 0 END) AS due_90,
                    SUM(CASE WHEN next_calibration_date IS NOT NULL AND next_calibration_date >= DATE_ADD(CURDATE(), INTERVAL 91 DAY) THEN 1 ELSE 0 END) AS compliant
                FROM equipment_state
            ), certificate_metrics AS (
                SELECT
                    SUM(CASE WHEN calibration_date BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE()) THEN 1 ELSE 0 END) AS certificates_this_month,
                    SUM(CASE WHEN pdf_url IS NOT NULL AND pdf_url <> '' THEN 1 ELSE 0 END) AS with_pdf,
                    COUNT(*) AS certificates_total
                FROM certificates
                WHERE deleted_at IS NULL
            ), client_metrics AS (
                SELECT
                    SUM(CASE WHEN last_cert.calibration_date >= DATE_SUB(CURDATE(), INTERVAL 365 DAY) THEN 1 ELSE 0 END) AS active_clients
                FROM (
                    SELECT cl.id, MAX(c.calibration_date) AS calibration_date
                    FROM clients cl
                    LEFT JOIN certificates c ON c.client_id = cl.id AND c.deleted_at IS NULL
                    GROUP BY cl.id
                ) last_cert
            ), new_clients AS (
                SELECT COUNT(*) AS new_clients_this_month
                FROM clients
                WHERE created_at BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE())
            )
            SELECT
                equipment_metrics.total_equipment,
                equipment_metrics.without_certificate,
                equipment_metrics.overdue,
                equipment_metrics.due_30,
                equipment_metrics.due_60,
                equipment_metrics.due_90,
                equipment_metrics.compliant,
                certificate_metrics.certificates_this_month,
                certificate_metrics.with_pdf,
                certificate_metrics.certificates_total,
                client_metrics.active_clients,
                new_clients.new_clients_this_month
            FROM equipment_metrics, certificate_metrics, client_metrics, new_clients";

        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return [
                'equipment' => [
                    'total' => 0,
                    'compliant' => 0,
                    'due_30' => 0,
                    'due_60' => 0,
                    'due_90' => 0,
                    'overdue' => 0,
                    'without_certificate' => 0
                ],
                'certificates' => [
                    'this_month' => 0,
                    'pdf_completion_pct' => 0.0,
                    'pending_pdf' => 0,
                    'with_pdf' => 0,
                    'total' => 0
                ],
                'clients' => [
                    'active' => 0,
                    'new_this_month' => 0
                ]
            ];
        }

        $certificatesTotal = (int)($row['certificates_total'] ?? 0);
        $withPdf = (int)($row['with_pdf'] ?? 0);
        $pdfCompletion = $certificatesTotal > 0 ? round(($withPdf / $certificatesTotal) * 100, 1) : 0.0;

        return [
            'equipment' => [
                'total' => (int)($row['total_equipment'] ?? 0),
                'compliant' => (int)($row['compliant'] ?? 0),
                'due_30' => (int)($row['due_30'] ?? 0),
                'due_60' => (int)($row['due_60'] ?? 0),
                'due_90' => (int)($row['due_90'] ?? 0),
                'overdue' => (int)($row['overdue'] ?? 0),
                'without_certificate' => (int)($row['without_certificate'] ?? 0)
            ],
            'certificates' => [
                'this_month' => (int)($row['certificates_this_month'] ?? 0),
                'pdf_completion_pct' => $pdfCompletion,
                'pending_pdf' => $certificatesTotal - $withPdf,
                'with_pdf' => $withPdf,
                'total' => $certificatesTotal
            ],
            'clients' => [
                'active' => (int)($row['active_clients'] ?? 0),
                'new_this_month' => (int)($row['new_clients_this_month'] ?? 0)
            ]
        ];
    }

    public function getCoverageByClient(): array
    {
        // El equipo es independiente; base en certificados emitidos para cada cliente.
        $sql = "WITH last_cert AS (
                SELECT c.*
                FROM certificates c
                JOIN (
                    SELECT equipment_id, MAX(calibration_date) AS max_cal_date
                    FROM certificates
                    WHERE deleted_at IS NULL
                    GROUP BY equipment_id
                ) lc ON lc.equipment_id = c.equipment_id AND lc.max_cal_date = c.calibration_date
                WHERE c.deleted_at IS NULL
            )
            SELECT
                cl.id AS client_id,
                cl.nombre,
                COUNT(DISTINCT lc.equipment_id) AS total_equipment_with_history,
                SUM(CASE WHEN lc.next_calibration_date IS NOT NULL AND lc.next_calibration_date >= CURDATE() THEN 1 ELSE 0 END) AS compliant_equipment,
                SUM(CASE WHEN lc.next_calibration_date IS NOT NULL AND lc.next_calibration_date < CURDATE() THEN 1 ELSE 0 END) AS overdue_equipment,
                ROUND(100 * SUM(CASE WHEN lc.next_calibration_date IS NOT NULL AND lc.next_calibration_date >= CURDATE() THEN 1 ELSE 0 END) / NULLIF(COUNT(DISTINCT lc.equipment_id), 0), 1) AS coverage_pct
            FROM clients cl
            LEFT JOIN last_cert lc ON lc.client_id = cl.id
            GROUP BY cl.id, cl.nombre
            ORDER BY coverage_pct ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getExpiringSoon(int $days): array
    {
        $days = max(1, $days);
        $sql = "SELECT
                    c.certificate_number,
                    c.equipment_id,
                    e.serial_number,
                    e.brand,
                    e.model,
                    et.name AS equipment_type,
                    c.client_id,
                    cl.nombre AS client_name,
                    c.next_calibration_date
                FROM certificates c
                JOIN equipment e ON e.id = c.equipment_id
                JOIN equipment_types et ON et.id = e.equipment_type_id
                LEFT JOIN clients cl ON cl.id = c.client_id
                WHERE c.deleted_at IS NULL
                  AND c.next_calibration_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
                ORDER BY c.next_calibration_date ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRiskRanking(int $limit): array
    {
        $limit = max(1, $limit);
        $sql = "WITH last_cert AS (
                SELECT c.*
                FROM certificates c
                JOIN (
                    SELECT equipment_id, MAX(calibration_date) AS max_cal_date
                    FROM certificates
                    WHERE deleted_at IS NULL
                    GROUP BY equipment_id
                ) lc ON lc.equipment_id = c.equipment_id AND lc.max_cal_date = c.calibration_date
                WHERE c.deleted_at IS NULL
            )
            SELECT
                cl.id AS client_id,
                cl.nombre,
                COUNT(*) AS overdue_equipment
            FROM clients cl
            JOIN last_cert lc ON lc.client_id = cl.id
            WHERE lc.next_calibration_date IS NOT NULL AND lc.next_calibration_date < CURDATE()
            GROUP BY cl.id, cl.nombre
            ORDER BY overdue_equipment DESC
            LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductivityByTechnician(): array
    {
        $sql = "SELECT
                    DATE_FORMAT(c.calibration_date, '%Y-%m') AS yyyymm,
                    u.id AS calibrator_id,
                    u.nombre AS calibrator,
                    COUNT(*) AS certificates_count
                FROM certificates c
                JOIN users u ON u.id = c.calibrator_id
                WHERE c.deleted_at IS NULL
                GROUP BY yyyymm, u.id, u.nombre
                ORDER BY yyyymm, u.nombre";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCertificatesByMonth(int $months): array
    {
        $months = max(1, $months);
        $sql = "SELECT
                    DATE_FORMAT(c.calibration_date, '%Y-%m') AS yyyymm,
                    COUNT(*) AS total
                FROM certificates c
                WHERE c.deleted_at IS NULL
                  AND c.calibration_date >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL :months MONTH), '%Y-%m-01')
                GROUP BY yyyymm
                ORDER BY yyyymm";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':months', $months, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDistributionByEquipmentType(): array
    {
        $sql = "SELECT et.name AS equipment_type, COUNT(*) AS total
                FROM equipment e
                JOIN equipment_types et ON et.id = e.equipment_type_id
                GROUP BY et.id, et.name
                ORDER BY total DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEquipmentWithoutCertificates(): array
    {
        $sql = "SELECT
                    e.id,
                    e.serial_number,
                    e.brand,
                    e.model,
                    et.name AS equipment_type
                FROM equipment e
                JOIN equipment_types et ON et.id = e.equipment_type_id
                LEFT JOIN certificates c ON c.equipment_id = e.id AND c.deleted_at IS NULL
                WHERE c.id IS NULL";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFailRates(int $months): array
    {
        $months = max(1, $months);
        $sql = "SELECT
                    DATE_FORMAT(calibration_date, '%Y-%m') AS yyyymm,
                    SUM(CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(results, '$.status')) = 'FAIL' THEN 1 ELSE 0 END) AS fails,
                    COUNT(*) AS total,
                    ROUND(100 * SUM(CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(results, '$.status')) = 'FAIL' THEN 1 ELSE 0 END) / NULLIF(COUNT(*), 0), 2) AS fail_rate_pct
                FROM certificates
                WHERE deleted_at IS NULL
                  AND calibration_date >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL :months MONTH), '%Y-%m-01')
                GROUP BY yyyymm
                ORDER BY yyyymm";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':months', $months, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMissingPdfCertificates(int $limit): array
    {
        $limit = max(1, $limit);
        $sql = "SELECT
                    c.certificate_number,
                    c.calibration_date,
                    c.client_id,
                    cl.nombre AS client_name,
                    c.equipment_id,
                    e.serial_number
                FROM certificates c
                LEFT JOIN clients cl ON cl.id = c.client_id
                LEFT JOIN equipment e ON e.id = c.equipment_id
                WHERE c.deleted_at IS NULL
                  AND (c.pdf_url IS NULL OR c.pdf_url = '')
                ORDER BY c.calibration_date DESC
                LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
