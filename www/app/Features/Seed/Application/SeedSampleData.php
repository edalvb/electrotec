<?php
namespace App\Features\Seed\Application;

use PDO;
use RuntimeException;
use Throwable;

final class SeedSampleData
{
    private const SUPERADMIN_ID = 'c2b6f79b-4d0c-4e15-8a23-8e06a9a3e4aa';
    private const TECHNICIAN_ID = 'f530b387-23c8-490a-9b86-37ab3f786014';
    private const CLIENT_USER_ID = 'b8ef98d7-2ff2-4d4b-a6de-6a6f53037b1a';

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
            $summary['user_profiles'] = $this->seedUsers();
            $summary['clients'] = $this->seedClients();
            $types = $this->seedEquipmentTypes();
            $summary['equipment_types'] = ['inserted' => $types['inserted'], 'updated' => $types['updated']];
            $summary['equipment'] = $this->seedEquipment($types['byName']);
            $summary['certificates'] = $this->seedCertificates();
            $summary['client_users'] = $this->seedClientUsers();
            $this->pdo->commit();
            return $summary;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /** @return array<string, int> */
    private function seedUsers(): array
    {
        $users = [
            [
                'id' => self::SUPERADMIN_ID,
                'full_name' => 'Ana Martínez',
                'signature_image_url' => null,
                'role' => 'SUPERADMIN',
                'is_active' => true,
            ],
            [
                'id' => self::TECHNICIAN_ID,
                'full_name' => 'Carlos Pérez',
                'signature_image_url' => null,
                'role' => 'TECHNICIAN',
                'is_active' => true,
            ],
            [
                'id' => self::CLIENT_USER_ID,
                'full_name' => 'Patricia Gómez',
                'signature_image_url' => null,
                'role' => 'CLIENT',
                'is_active' => true,
            ],
        ];

        $sql = "INSERT INTO user_profiles (id, full_name, signature_image_url, role, is_active, deleted_at)\n                VALUES (:id, :full_name, :signature_image_url, :role, :is_active, NULL)\n                ON DUPLICATE KEY UPDATE\n                    full_name = VALUES(full_name),\n                    signature_image_url = VALUES(signature_image_url),\n                    role = VALUES(role),\n                    is_active = VALUES(is_active),\n                    deleted_at = NULL";

        $stmt = $this->pdo->prepare($sql);
        $inserted = 0;
        $updated = 0;

        foreach ($users as $user) {
            $stmt->execute([
                ':id' => $user['id'],
                ':full_name' => $user['full_name'],
                ':signature_image_url' => $user['signature_image_url'],
                ':role' => $user['role'],
                ':is_active' => $user['is_active'] ? 1 : 0,
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
    private function seedClients(): array
    {
        $clients = [
            [
                'id' => self::CLIENT_ALPHA_ID,
                'name' => 'Energía Andina S.A.',
                'contact_details' => [
                    'email' => 'contacto@energia-andina.com',
                    'phone' => '+57 1 555 1234',
                    'address' => 'Av. Libertador 321, Bogotá',
                ],
            ],
            [
                'id' => self::CLIENT_BETA_ID,
                'name' => 'Hospital Central del Norte',
                'contact_details' => [
                    'email' => 'compras@hcnorte.org',
                    'phone' => '+57 1 555 9876',
                    'address' => 'Cra. 45 #82-14, Bogotá',
                ],
            ],
        ];

        $sql = "INSERT INTO clients (id, name, contact_details, created_at)\n                VALUES (:id, :name, :contact_details, NOW())\n                ON DUPLICATE KEY UPDATE\n                    name = VALUES(name),\n                    contact_details = VALUES(contact_details)";

        $stmt = $this->pdo->prepare($sql);
        $inserted = 0;
        $updated = 0;

        foreach ($clients as $client) {
            $contact = $this->toJson($client['contact_details']);
            $stmt->execute([
                ':id' => $client['id'],
                ':name' => $client['name'],
                ':contact_details' => $contact,
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
            'Balanza de precisión',
            'Multímetro digital',
            'Cámara térmica',
        ];

        $sql = "INSERT INTO equipment_types (name) VALUES (:name)\n                ON DUPLICATE KEY UPDATE name = VALUES(name)";
        $stmt = $this->pdo->prepare($sql);
        $inserted = 0;
        $updated = 0;

        foreach ($types as $name) {
            $stmt->execute([':name' => $name]);
            $count = $stmt->rowCount();
            if ($count === 1) {
                $inserted++;
            } else {
                $updated++;
            }
        }

        $mappingStmt = $this->pdo->prepare('SELECT id, name FROM equipment_types WHERE name IN (?,?,?)');
        $mappingStmt->execute([$types[0], $types[1], $types[2]]);
        $byName = [];
        foreach ($mappingStmt->fetchAll() as $row) {
            $byName[$row['name']] = (int)$row['id'];
        }

        // Validar que se obtuvieron todos los IDs necesarios
        foreach ($types as $typeName) {
            if (!isset($byName[$typeName])) {
                throw new RuntimeException('No se pudo resolver el ID para el tipo de equipo: ' . $typeName);
            }
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
                'equipment_type_name' => 'Balanza de precisión',
                'owner_client_id' => self::CLIENT_ALPHA_ID,
            ],
            [
                'id' => self::EQUIPMENT_MULTIMETER_ID,
                'serial_number' => 'MM-2025-0009',
                'brand' => 'Fluke',
                'model' => '87V',
                'equipment_type_name' => 'Multímetro digital',
                'owner_client_id' => self::CLIENT_BETA_ID,
            ],
        ];

        $sql = "INSERT INTO equipment (id, serial_number, brand, model, owner_client_id, equipment_type_id, created_at)\n                VALUES (:id, :serial_number, :brand, :model, :owner_client_id, :equipment_type_id, NOW())\n                ON DUPLICATE KEY UPDATE\n                    serial_number = VALUES(serial_number),\n                    brand = VALUES(brand),\n                    model = VALUES(model),\n                    owner_client_id = VALUES(owner_client_id),\n                    equipment_type_id = VALUES(equipment_type_id)";

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
                ':owner_client_id' => $equipment['owner_client_id'],
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
                'technician_id' => self::TECHNICIAN_ID,
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
                'technician_id' => self::TECHNICIAN_ID,
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

        $sql = "INSERT INTO certificates (id, certificate_number, equipment_id, technician_id, calibration_date, next_calibration_date, results, lab_conditions, pdf_url, client_id, created_at, updated_at, deleted_at)\n                VALUES (:id, :certificate_number, :equipment_id, :technician_id, :calibration_date, :next_calibration_date, :results, :lab_conditions, :pdf_url, :client_id, NOW(), NOW(), NULL)\n                ON DUPLICATE KEY UPDATE\n                    certificate_number = VALUES(certificate_number),\n                    equipment_id = VALUES(equipment_id),\n                    technician_id = VALUES(technician_id),\n                    calibration_date = VALUES(calibration_date),\n                    next_calibration_date = VALUES(next_calibration_date),\n                    results = VALUES(results),\n                    lab_conditions = VALUES(lab_conditions),\n                    pdf_url = VALUES(pdf_url),\n                    client_id = VALUES(client_id),\n                    deleted_at = VALUES(deleted_at)";

        $stmt = $this->pdo->prepare($sql);
        $inserted = 0;
        $updated = 0;

        foreach ($certificates as $certificate) {
            $stmt->execute([
                ':id' => $certificate['id'],
                ':certificate_number' => $certificate['certificate_number'],
                ':equipment_id' => $certificate['equipment_id'],
                ':technician_id' => $certificate['technician_id'],
                ':calibration_date' => $certificate['calibration_date'],
                ':next_calibration_date' => $certificate['next_calibration_date'],
                ':results' => $this->toJson($certificate['results']),
                ':lab_conditions' => $this->toJson($certificate['lab_conditions']),
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

        return ['inserted' => $inserted, 'updated' => $updated];
    }

    /** @return array<string, int> */
    private function seedClientUsers(): array
    {
        $links = [
            [
                'id' => '5bdd1b26-8127-4ca2-b384-f2e9419b5bc6',
                'client_id' => self::CLIENT_ALPHA_ID,
                'user_profile_id' => self::CLIENT_USER_ID,
                'permissions' => [
                    'view_certificates' => true,
                    'view_equipment' => true,
                ],
            ],
        ];

        $sql = "INSERT INTO client_users (id, client_id, user_profile_id, permissions, created_at)\n                VALUES (:id, :client_id, :user_profile_id, :permissions, NOW())\n                ON DUPLICATE KEY UPDATE\n                    permissions = VALUES(permissions)";

        $stmt = $this->pdo->prepare($sql);
        $inserted = 0;
        $updated = 0;

        foreach ($links as $link) {
            $stmt->execute([
                ':id' => $link['id'],
                ':client_id' => $link['client_id'],
                ':user_profile_id' => $link['user_profile_id'],
                ':permissions' => $this->toJson($link['permissions']),
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
