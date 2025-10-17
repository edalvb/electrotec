<?php
namespace App\Features\Certificates\Infrastructure;

use App\Features\Certificates\Domain\CertificateRepository;
use PDO;

final class PdoCertificateRepository implements CertificateRepository
{
    public function __construct(private PDO $pdo) {}

    public function listAll(int $limit = 50, int $offset = 0): array
    {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);

        $sql = "SELECT c.*, 
                       cl.nombre AS client_name,
                       e.serial_number AS equipment_serial_number,
                       e.brand AS equipment_brand,
                       e.model AS equipment_model
                FROM certificates c
                LEFT JOIN clients cl ON cl.id = c.client_id
                LEFT JOIN equipment e ON e.id = c.equipment_id
                ORDER BY c.created_at DESC
                LIMIT {$limit} OFFSET {$offset}";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function countAll(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM certificates');
        return (int)$stmt->fetchColumn();
    }

    public function listByClientId(string $clientId, int $limit = 50, int $offset = 0): array
    {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        $sql = "SELECT c.* FROM certificates c WHERE c.client_id = :client_id ORDER BY c.created_at DESC LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':client_id', $clientId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function listForClientUser(string $userProfileId, int $limit = 50, int $offset = 0): array
    {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        $sql = "SELECT c.*
                FROM client_users cu
                JOIN certificates c ON c.client_id = cu.client_id
                WHERE cu.user_profile_id = :uid
                  AND JSON_EXTRACT(cu.permissions, '$.view_certificates') = true
                ORDER BY c.created_at DESC
                LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':uid', $userProfileId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByCertificateNumber(string $certificateNumber): ?array
    {
        $sql = "SELECT c.* FROM certificates c WHERE c.certificate_number = :n LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':n', $certificateNumber);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Devuelve un certificado con sus datos relacionados (condiciones_ambientales, resultados, resultados_distancia)
     * @return array<string,mixed>|null
     */
    public function findByIdWithDetails(string $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM certificates WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $cert = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$cert) { return null; }

        // condiciones_ambientales
        $stmtC = $this->pdo->prepare('SELECT * FROM condiciones_ambientales WHERE id_certificado = :id LIMIT 1');
        $stmtC->execute([':id' => $id]);
        $cond = $stmtC->fetch(PDO::FETCH_ASSOC) ?: null;
        if ($cond) {
            $cert['lab_conditions'] = [
                'temperatura_celsius' => $cond['temperatura_celsius'],
                'humedad_relativa_porc' => $cond['humedad_relativa_porc'],
                'presion_atm_mmhg' => $cond['presion_atm_mmhg'],
            ];
        } else {
            $cert['lab_conditions'] = null;
        }

        // resultados
        $stmtR = $this->pdo->prepare('SELECT * FROM resultados WHERE id_certificado = :id ORDER BY id ASC');
        $stmtR->execute([':id' => $id]);
        $cert['resultados'] = $stmtR->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // resultados_distancia
        $stmtD = $this->pdo->prepare('SELECT * FROM resultados_distancia WHERE id_certificado = :id ORDER BY id_resultado ASC');
        $stmtD->execute([':id' => $id]);
        $cert['resultados_distancia'] = $stmtD->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return $cert;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        $nowYear = (int)date('Y');

        $this->pdo->beginTransaction();
        try {
            // Obtener y actualizar secuencia de forma segura por año
            // Usamos INSERT ... ON DUPLICATE KEY UPDATE para crear fila si no existe
            $initStmt = $this->pdo->prepare(
                'INSERT INTO certificate_sequences (year, last_number) VALUES (:y, 0)
                 ON DUPLICATE KEY UPDATE last_number = last_number'
            );
            $initStmt->execute([':y' => $nowYear]);

            // Bloqueo de la fila del año para incrementar de forma atómica
            $selStmt = $this->pdo->prepare('SELECT last_number FROM certificate_sequences WHERE year = :y FOR UPDATE');
            $selStmt->execute([':y' => $nowYear]);
            $row = $selStmt->fetch();
            $last = $row && isset($row['last_number']) ? (int)$row['last_number'] : 0;
            $next = $last + 1;

            $updStmt = $this->pdo->prepare('UPDATE certificate_sequences SET last_number = :n WHERE year = :y');
            $updStmt->execute([':n' => $next, ':y' => $nowYear]);

            // Construir número con padding (por ejemplo 4 dígitos): 2025-0001
            $certNumber = sprintf('%d-%04d', $nowYear, $next);

                // Insertar certificado
            $insert = $this->pdo->prepare(
                'INSERT INTO certificates (
                    id, certificate_number, equipment_id, calibrator_id,
                          calibration_date, next_calibration_date, results,
                    pdf_url, client_id, created_at, updated_at, deleted_at
                 ) VALUES (
                    :id, :certificate_number, :equipment_id, :calibrator_id,
                          :calibration_date, :next_calibration_date, :results,
                    :pdf_url, :client_id, NOW(), NOW(), NULL
                 )'
            );

            $insert->execute([
                ':id' => (string)$data['id'],
                ':certificate_number' => $certNumber,
                ':equipment_id' => (string)$data['equipment_id'],
                ':calibrator_id' => (int)$data['calibrator_id'],
                ':calibration_date' => (string)$data['calibration_date'],
                ':next_calibration_date' => (string)($data['next_calibration_date'] ?? $data['calibration_date']),
                ':results' => json_encode($data['results'] ?? new \stdClass(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                ':pdf_url' => $data['pdf_url'] ?? null,
                ':client_id' => $data['client_id'] ?? null,
            ]);

            // Insertar condiciones ambientales desacopladas si se proporcionaron
            if (!empty($data['lab_conditions']) && is_array($data['lab_conditions'])) {
                $cond = $data['lab_conditions'];
                $stmtCond = $this->pdo->prepare(
                    'INSERT INTO condiciones_ambientales (
                        id_certificado, temperatura_celsius, humedad_relativa_porc, presion_atm_mmhg
                    ) VALUES (
                        :idc, :temp, :hum, :pres
                    )
                    ON DUPLICATE KEY UPDATE
                        temperatura_celsius = VALUES(temperatura_celsius),
                        humedad_relativa_porc = VALUES(humedad_relativa_porc),
                        presion_atm_mmhg = VALUES(presion_atm_mmhg)'
                );
                $stmtCond->execute([
                    ':idc' => (string)$data['id'],
                    ':temp' => isset($cond['temperature']) ? (float)$cond['temperature'] : (isset($cond['temperatura_celsius']) ? (float)$cond['temperatura_celsius'] : null),
                    ':hum'  => isset($cond['humidity']) ? (float)$cond['humidity'] : (isset($cond['humedad_relativa_porc']) ? (float)$cond['humedad_relativa_porc'] : null),
                    ':pres' => isset($cond['pressure']) ? (int)round((float)$cond['pressure']) : (isset($cond['presion_atm_mmhg']) ? (int)$cond['presion_atm_mmhg'] : null),
                ]);
            }

            // Determinar configuración por defecto del equipo (resultado_precision y con_prisma)
            $eqDefaults = ['resultado_precision' => 'segundos', 'resultado_conprisma' => 0];
            try {
                $eqStmt = $this->pdo->prepare('SELECT et.resultado_precision, et.resultado_conprisma FROM equipment e JOIN equipment_types et ON et.id = e.equipment_type_id WHERE e.id = :eid LIMIT 1');
                $eqStmt->execute([':eid' => (string)$data['equipment_id']]);
                $eqRow = $eqStmt->fetch();
                if ($eqRow) {
                    $eqDefaults['resultado_precision'] = in_array(($eqRow['resultado_precision'] ?? 'segundos'), ['segundos','lineal'], true) ? $eqRow['resultado_precision'] : 'segundos';
                    $eqDefaults['resultado_conprisma'] = (int)($eqRow['resultado_conprisma'] ?? 0);
                }
            } catch (\Throwable $e) {
                // ignorar, mantener defaults
            }

            // Insertar resultados angulares/lineales (tabla resultados)
            if (!empty($data['resultados']) && is_array($data['resultados'])) {
                $stmtRes = $this->pdo->prepare(
                    'INSERT INTO resultados (
                        id_certificado, tipo_resultado,
                        valor_patron_grados, valor_patron_minutos, valor_patron_segundos,
                        valor_obtenido_grados, valor_obtenido_minutos, valor_obtenido_segundos,
                        precision_val, error_segundos
                    ) VALUES (
                        :idc, :tipo,
                        :pg, :pm, :ps,
                        :og, :om, :os,
                        :prec, :err
                    )'
                );
                foreach ($data['resultados'] as $row) {
                    if (!is_array($row)) { continue; }
                    $tipo = (string)($row['tipo_resultado'] ?? $eqDefaults['resultado_precision'] ?? 'segundos');
                    if (!in_array($tipo, ['segundos','lineal'], true)) { $tipo = 'segundos'; }
                    $stmtRes->execute([
                        ':idc' => (string)$data['id'],
                        ':tipo' => $tipo,
                        ':pg' => (int)($row['valor_patron_grados'] ?? 0),
                        ':pm' => (int)max(0, (int)($row['valor_patron_minutos'] ?? 0)),
                        ':ps' => (int)max(0, (int)($row['valor_patron_segundos'] ?? 0)),
                        ':og' => (int)($row['valor_obtenido_grados'] ?? 0),
                        ':om' => (int)max(0, (int)($row['valor_obtenido_minutos'] ?? 0)),
                        ':os' => (int)max(0, (int)($row['valor_obtenido_segundos'] ?? 0)),
                        ':prec' => (float)($row['precision'] ?? 0),
                        ':err' => (int)max(0, (int)($row['error_segundos'] ?? 0)),
                    ]);
                }
            }

            // Insertar resultados de distancia (tabla resultados_distancia)
            if (!empty($data['resultados_distancia']) && is_array($data['resultados_distancia'])) {
                $stmtDist = $this->pdo->prepare(
                    'INSERT INTO resultados_distancia (
                        id_certificado, punto_control_metros, distancia_obtenida_metros, variacion_metros,
                        precision_base_mm, precision_ppm, con_prisma
                    ) VALUES (
                        :idc, :pcm, :dom, :vm, :pb, :pp, :cp
                    )'
                );
                foreach ($data['resultados_distancia'] as $row) {
                    if (!is_array($row)) { continue; }
                    $stmtDist->execute([
                        ':idc' => (string)$data['id'],
                        ':pcm' => (float)($row['punto_control_metros'] ?? 0),
                        ':dom' => (float)($row['distancia_obtenida_metros'] ?? 0),
                        ':vm'  => (float)($row['variacion_metros'] ?? 0),
                        ':pb'  => (int)($row['precision_base_mm'] ?? 0),
                        ':pp'  => (int)($row['precision_ppm'] ?? 0),
                        ':cp'  => isset($row['con_prisma']) ? ((bool)$row['con_prisma'] ? 1 : 0) : (int)$eqDefaults['resultado_conprisma'],
                    ]);
                }
            }

            $this->pdo->commit();

            // Devolver el registro creado
            $stmt = $this->pdo->prepare('SELECT * FROM certificates WHERE id = :id');
            $stmt->execute([':id' => (string)$data['id']]);
            $created = $stmt->fetch();
            return $created ?: [
                'id' => (string)$data['id'],
                'certificate_number' => $certNumber,
            ];
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
