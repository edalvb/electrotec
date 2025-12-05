<?php
namespace App\Features\Seed\Application;

use PDO;
use RuntimeException;
use Throwable;

final class SeedSampleData
{
    private const ADMIN_ID = 1; // usuario admin (no se usa como técnico desde ahora)
    private const TECNICO_JUAN_ID = 1;
    private const TECNICO_LUISA_ID = 2;

    private const CLIENT_ALPHA_ID = 'c30a21df-9a58-4fd1-9ba9-31a2d2c686d4';
    private const CLIENT_BETA_ID = '4d6d9f9e-7406-4fb7-8f72-8eb8da4cbf4f';

    private const EQUIPMENT_BALANCE_ID = '1f9121c0-3b8a-4e4a-a4d6-6e00f48f47c3';
    private const EQUIPMENT_MULTIMETER_ID = 'e2a3f49b-0b7d-4e9d-a4fb-7b5b5e3f81a9';

    private const CERTIFICATE_BALANCE_ID = '5928e6a2-5134-4f12-857c-9da9bec6a440';
    private const CERTIFICATE_MULTIMETER_ID = '0ff7c1ad-8a02-4fba-9a29-2f664a13c48f';

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Ejecuta la semilla con datos base coherentes para el dominio.
     *
     * @return array<string, array<string, int>> Resumen por tabla con contadores de insert/update.
     */
    public function __invoke(): array
    {
        $this->pdo->beginTransaction();
        try {
            $summary = [];
            $summary['users'] = $this->seedUsers();
            $summary['clients'] = $this->seedClients();
            $types = $this->seedEquipmentTypes();
            $summary['equipment_types'] = ['inserted' => $types['inserted'], 'updated' => $types['updated']];
            $summary['equipment'] = $this->seedEquipment($types['byName']);
            $summary['tecnico'] = $this->seedTechnicians();
            $summary['certificates'] = $this->seedCertificates();
            $this->pdo->commit();
            return $summary;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /** @return array<string,int> */
    private function seedTechnicians(): array
    {
        $rows = [
            ['id' => self::TECNICO_JUAN_ID, 'nombre_completo' => 'Juan Pérez', 'cargo' => 'Técnico de laboratorio', 'path_firma' => null],
            ['id' => self::TECNICO_LUISA_ID, 'nombre_completo' => 'Luisa Gómez', 'cargo' => 'Técnico de laboratorio', 'path_firma' => null],
        ];
        $sql = "INSERT INTO tecnico (id, nombre_completo, cargo, path_firma)
                VALUES (:id, :n, :c, :p)
                ON DUPLICATE KEY UPDATE nombre_completo = VALUES(nombre_completo), cargo = VALUES(cargo), path_firma = VALUES(path_firma)";
        $stmt = $this->pdo->prepare($sql);
        $ins = 0; $upd = 0;
        foreach($rows as $r){
            $stmt->execute([':id'=>$r['id'], ':n'=>$r['nombre_completo'], ':c'=>$r['cargo'], ':p'=>$r['path_firma']]);
            $count = $stmt->rowCount(); if ($count === 1) { $ins++; } else { $upd++; }
        }
        return ['inserted'=>$ins,'updated'=>$upd];
    }

    /** @return array<string, int> */
    private function seedUsers(): array
    {
        // Usuarios del sistema
        $defaultPassword = password_hash('abc123', PASSWORD_DEFAULT);
        
        // Clientes deben tener user/pass = RUC
        $ru_client1 = '20123456789';
        $ru_client2 = '20987654321';

        $users = [
            [
                'id' => 1,
                'username' => 'admin',
                'password_hash' => $defaultPassword,
                'tipo' => 'admin',
            ],
            [
                'id' => 2,
                'username' => $ru_client1, // RUC de Energia Andina
                'password_hash' => password_hash($ru_client1, PASSWORD_DEFAULT),
                'tipo' => 'client',
            ],
            [
                'id' => 3,
                'username' => $ru_client2, // RUC de Hospital Central
                'password_hash' => password_hash($ru_client2, PASSWORD_DEFAULT),
                'tipo' => 'client',
            ],
        ];

        $sql = "INSERT INTO users (id, username, password_hash, tipo)
                VALUES (:id, :username, :password_hash, :tipo)
                ON DUPLICATE KEY UPDATE
                    username = VALUES(username),
                    password_hash = VALUES(password_hash),
                    tipo = VALUES(tipo)";

        $stmt = $this->pdo->prepare($sql);
        $inserted = 0;
        $updated = 0;

        foreach ($users as $user) {
            $stmt->execute([
                ':id' => $user['id'],
                ':username' => $user['username'],
                ':password_hash' => $user['password_hash'],
                ':tipo' => $user['tipo'],
            ]);
            $count = $stmt->rowCount();
            if ($count === 1) {
                $inserted++;
            } else {
                $updated++;
            }
        }

        return ['inserted' => $inserted, 'updated' => $updated];
    }

    // Creación de clientes

    /** @return array<string, int> */
    private function seedClients(): array
    {
        $clients = [
            [
                'id' => self::CLIENT_ALPHA_ID,
                'user_id' => 2,
                'nombre' => 'Energía Andina S.A.',
                'ruc' => '20123456789',
                'dni' => null,
                'email' => 'contacto@energia-andina.com',
                'celular' => '+57 1 555 1234',
                'direccion' => 'Av. Libertador 321, Bogotá',
            ],
            [
                'id' => self::CLIENT_BETA_ID,
                'user_id' => 3,
                'nombre' => 'Hospital Central del Norte',
                'ruc' => '20987654321',
                'dni' => null,
                'email' => 'compras@hcnorte.org',
                'celular' => '+57 1 555 9876',
                'direccion' => 'Cra. 45 #82-14, Bogotá',
            ],
        ];

        $sql = "INSERT INTO clients (id, user_id, nombre, ruc, dni, email, celular, direccion, created_at)
                VALUES (:id, :user_id, :nombre, :ruc, :dni, :email, :celular, :direccion, NOW())
                ON DUPLICATE KEY UPDATE
                    user_id = VALUES(user_id),
                    nombre = VALUES(nombre),
                    ruc = VALUES(ruc),
                    dni = VALUES(dni),
                    email = VALUES(email),
                    celular = VALUES(celular),
                    direccion = VALUES(direccion)";

        $stmt = $this->pdo->prepare($sql);
        $inserted = 0;
        $updated = 0;

        foreach ($clients as $client) {
            $stmt->execute([
                ':id' => $client['id'],
                ':user_id' => $client['user_id'],
                ':nombre' => $client['nombre'],
                ':ruc' => $client['ruc'],
                ':dni' => $client['dni'],
                ':email' => $client['email'],
                ':celular' => $client['celular'],
                ':direccion' => $client['direccion'],
            ]);
            $count = $stmt->rowCount();
            if ($count === 1) {
                $inserted++;
            } else {
                $updated++;
            }
        }

        return ['inserted' => $inserted, 'updated' => $updated];
    }

    /**
     * @return array{inserted:int,updated:int,byName:array<string,int>}
     */
    private function seedEquipmentTypes(): array
    {
        $types = [
            ['name' => 'Teodolito', 'resultado_precision' => 'vertical_horizontal', 'resultado_conprisma' => 1],
            ['name' => 'Nivel', 'resultado_precision' => 'lineal', 'resultado_conprisma' => 0],
            ['name' => 'Estación', 'resultado_precision' => 'vertical_horizontal', 'resultado_conprisma' => 0],
        ];

        $sql = "INSERT INTO equipment_types (name, resultado_precision, resultado_conprisma) VALUES (:name, :precision, :conprisma)\n                ON DUPLICATE KEY UPDATE resultado_precision = VALUES(resultado_precision), resultado_conprisma = VALUES(resultado_conprisma)";
        $stmt = $this->pdo->prepare($sql);
        $inserted = 0;
        $updated = 0;

        foreach ($types as $t) {
            $stmt->execute([
                ':name' => $t['name'],
                ':precision' => $t['resultado_precision'],
                ':conprisma' => $t['resultado_conprisma'],
            ]);
            $count = $stmt->rowCount();
            if ($count === 1) {
                $inserted++;
            } else {
                $updated++;
            }
        }

        $names = array_column($types, 'name');
        // Resolver IDs por nombre delegando normalización de mayúsculas y acentos al motor SQL
        $mappingStmt = $this->pdo->prepare('SELECT id FROM equipment_types WHERE name = UPPER(:name) LIMIT 1');
        $byName = [];
        foreach ($names as $originalName) {
            $mappingStmt->execute([':name' => $originalName]);
            $row = $mappingStmt->fetch();
            if ($row === false || !isset($row['id'])) {
                throw new RuntimeException('No se pudo resolver el ID para el tipo de equipo: ' . $originalName);
            }
            $byName[$originalName] = (int)$row['id'];
        }

        return ['inserted' => $inserted, 'updated' => $updated, 'byName' => $byName];
    }

    /**
     * @param array<string,int> $equipmentTypesByName
     * @return array<string, int>
     */
    private function seedEquipment(array $equipmentTypesByName): array
    {
        $equipments = [
            [
                'id' => self::EQUIPMENT_BALANCE_ID,
                'serial_number' => 'BAL-2025-0001',
                'brand' => 'Mettler Toledo',
                'model' => 'ML204',
                'equipment_type_name' => 'Nivel',
            ],
            [
                'id' => self::EQUIPMENT_MULTIMETER_ID,
                'serial_number' => 'MM-2025-0009',
                'brand' => 'Fluke',
                'model' => '87V',
                'equipment_type_name' => 'Estación',
            ],
        ];

        $sql = "INSERT INTO equipment (id, serial_number, brand, model, equipment_type_id, created_at)\n                VALUES (:id, :serial_number, :brand, :model, :equipment_type_id, NOW())\n                ON DUPLICATE KEY UPDATE\n                    serial_number = VALUES(serial_number),\n                    brand = VALUES(brand),\n                    model = VALUES(model),\n                    equipment_type_id = VALUES(equipment_type_id)";

        $stmt = $this->pdo->prepare($sql);
        $inserted = 0;
        $updated = 0;

        foreach ($equipments as $equipment) {
            $typeName = $equipment['equipment_type_name'];
            if (!isset($equipmentTypesByName[$typeName])) {
                throw new RuntimeException('No se encontró el tipo de equipo: ' . $typeName);
            }
            $typeId = $equipmentTypesByName[$typeName];
            $stmt->execute([
                ':id' => $equipment['id'],
                ':serial_number' => $equipment['serial_number'],
                ':brand' => $equipment['brand'],
                ':model' => $equipment['model'],
                ':equipment_type_id' => $typeId,
            ]);
            $count = $stmt->rowCount();
            if ($count === 1) {
                $inserted++;
            } else {
                $updated++;
            }
        }

        return ['inserted' => $inserted, 'updated' => $updated];
    }

    /** @return array<string, int> */
    private function seedCertificates(): array
    {
        $certificates = [
            [
                'id' => self::CERTIFICATE_BALANCE_ID,
                'certificate_number' => 'CAL-2025-0001',
                'equipment_id' => self::EQUIPMENT_BALANCE_ID,
                'calibrator_id' => self::TECNICO_JUAN_ID,
                'calibration_date' => '2025-01-15',
                'next_calibration_date' => '2026-01-15',
                'results' => [
                    'status' => 'APROBADO',
                    'observations' => 'Dentro de tolerancia ±0.001 g',
                    'measurements' => [
                        ['point' => 50, 'observed' => 49.999],
                        ['point' => 100, 'observed' => 100.001],
                    ],
                ],
                'lab_conditions' => [
                    'temperature' => '23°C',
                    'humidity' => '45%',
                ],
                'pdf_url' => null,
                'client_id' => self::CLIENT_ALPHA_ID,
            ],
            [
                'id' => self::CERTIFICATE_MULTIMETER_ID,
                'certificate_number' => 'CAL-2025-0002',
                'equipment_id' => self::EQUIPMENT_MULTIMETER_ID,
                'calibrator_id' => self::TECNICO_LUISA_ID,
                'calibration_date' => '2025-02-03',
                'next_calibration_date' => '2026-02-03',
                'results' => [
                    'status' => 'APROBADO',
                    'observations' => 'Respuestas estables en escalas de corriente y voltaje',
                    'measurements' => [
                        ['function' => 'Vdc', 'range' => '10V', 'observed' => 9.999],
                        ['function' => 'Adc', 'range' => '1A', 'observed' => 1.001],
                    ],
                ],
                'lab_conditions' => [
                    'temperature' => '22°C',
                    'humidity' => '47%',
                ],
                'pdf_url' => null,
                'client_id' => self::CLIENT_BETA_ID,
            ],
        ];

    $sql = "INSERT INTO certificates (id, certificate_number, equipment_id, calibrator_id, calibration_date, next_calibration_date, results, pdf_url, client_id, created_at, updated_at, deleted_at)\n                VALUES (:id, :certificate_number, :equipment_id, :calibrator_id, :calibration_date, :next_calibration_date, :results, :pdf_url, :client_id, NOW(), NOW(), NULL)\n                ON DUPLICATE KEY UPDATE\n                    certificate_number = VALUES(certificate_number),\n                    equipment_id = VALUES(equipment_id),\n                    calibrator_id = VALUES(calibrator_id),\n                    calibration_date = VALUES(calibration_date),\n                    next_calibration_date = VALUES(next_calibration_date),\n                    results = VALUES(results),\n                    pdf_url = VALUES(pdf_url),\n                    client_id = VALUES(client_id),\n                    deleted_at = VALUES(deleted_at)";

        $stmt = $this->pdo->prepare($sql);
        $inserted = 0;
        $updated = 0;

        foreach ($certificates as $certificate) {
            $stmt->execute([
                ':id' => $certificate['id'],
                ':certificate_number' => $certificate['certificate_number'],
                ':equipment_id' => $certificate['equipment_id'],
                ':calibrator_id' => $certificate['calibrator_id'],
                ':calibration_date' => $certificate['calibration_date'],
                ':next_calibration_date' => $certificate['next_calibration_date'],
                ':results' => $this->toJson($certificate['results']),
                ':pdf_url' => $certificate['pdf_url'],
                ':client_id' => $certificate['client_id'],
            ]);
            $count = $stmt->rowCount();
            if ($count === 1) {
                $inserted++;
            } else {
                $updated++;
            }
        }

        // Insertar condiciones ambientales desacopladas
        $stmtCond = $this->pdo->prepare('INSERT INTO condiciones_ambientales (id_certificado, temperatura_celsius, humedad_relativa_porc, presion_atm_mmhg) VALUES (:idc, :t, :h, :p) ON DUPLICATE KEY UPDATE temperatura_celsius=VALUES(temperatura_celsius), humedad_relativa_porc=VALUES(humedad_relativa_porc), presion_atm_mmhg=VALUES(presion_atm_mmhg)');
        foreach ($certificates as $certificate) {
            $lab = $certificate['lab_conditions'] ?? null;
            if ($lab) {
                $stmtCond->execute([
                    ':idc' => $certificate['id'],
                    ':t' => isset($lab['temperature']) ? (float)$lab['temperature'] : null,
                    ':h' => isset($lab['humidity']) ? (float)$lab['humidity'] : null,
                    ':p' => isset($lab['pressure']) ? (int)round((float)$lab['pressure']) : null,
                ]);
            }
        }

        return ['inserted' => $inserted, 'updated' => $updated];
    }

    /**
     * @param array<string, mixed>|null $data
     */
    private function toJson(?array $data): ?string
    {
        if ($data === null) {
            return null;
        }
        $encoded = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($encoded === false) {
            throw new RuntimeException('No se pudo serializar datos a JSON.');
        }
        return $encoded;
    }
}
