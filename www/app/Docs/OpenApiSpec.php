<?php
namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
	info: new OA\Info(
		version: '0.2.0',
		title: 'Electrotec API',
		description: 'Documentación generada automáticamente desde anotaciones.'
	),
	servers: [
		new OA\Server(
			url: 'http://localhost:8082',
			description: 'Entorno local (docker-compose)'
		)
	],
	components: new OA\Components(
		schemas: [
			new OA\Schema(
				schema: 'EnvelopeError', type: 'object',
				properties: [
					new OA\Property(property: 'ok', type: 'boolean', example: false),
					new OA\Property(property: 'message', type: 'string'),
					new OA\Property(property: 'details')
				]
			),
			new OA\Schema(
				schema: 'EnvelopeHealth', type: 'object',
				properties: [
					new OA\Property(property: 'ok', type: 'boolean', example: true),
					new OA\Property(property: 'data', type: 'object', properties: [
						new OA\Property(property: 'status', type: 'string'),
						new OA\Property(property: 'time', type: 'string', format: 'date-time'),
					])
				]
			),
			new OA\Schema(
				schema: 'EnvelopeUsers', type: 'object',
				properties: [
					new OA\Property(property: 'ok', type: 'boolean', example: true),
					new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/UserProfile')),
				]
			),
			new OA\Schema(
				schema: 'EnvelopeClients', type: 'object',
				properties: [
					new OA\Property(property: 'ok', type: 'boolean', example: true),
					new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Client')),
				]
			),
			new OA\Schema(
				schema: 'EnvelopeClient', type: 'object',
				properties: [
					new OA\Property(property: 'ok', type: 'boolean', example: true),
					new OA\Property(property: 'data', ref: '#/components/schemas/Client'),
				]
			),
			new OA\Schema(
				schema: 'EnvelopeEquipmentList', type: 'object',
				properties: [
					new OA\Property(property: 'ok', type: 'boolean', example: true),
					new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Equipment')),
				]
			),
			new OA\Schema(
				schema: 'EnvelopeEquipment', type: 'object',
				properties: [
					new OA\Property(property: 'ok', type: 'boolean', example: true),
					new OA\Property(property: 'data', ref: '#/components/schemas/Equipment'),
				]
			),
			new OA\Schema(
				schema: 'EnvelopeEquipmentTypes', type: 'object',
				properties: [
					new OA\Property(property: 'ok', type: 'boolean', example: true),
					new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/EquipmentType')),
				]
			),
			new OA\Schema(
				schema: 'EnvelopeCertificates', type: 'object',
				properties: [
					new OA\Property(property: 'ok', type: 'boolean', example: true),
					new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Certificate')),
				]
			),
			new OA\Schema(
				schema: 'SeedTableSummary', type: 'object',
				properties: [
					new OA\Property(property: 'inserted', type: 'integer', example: 2),
					new OA\Property(property: 'updated', type: 'integer', example: 0),
				]
			),
			new OA\Schema(
				schema: 'SeedSummary', type: 'object',
				properties: [
					new OA\Property(property: 'user_profiles', ref: '#/components/schemas/SeedTableSummary'),
					new OA\Property(property: 'clients', ref: '#/components/schemas/SeedTableSummary'),
					new OA\Property(property: 'equipment_types', ref: '#/components/schemas/SeedTableSummary'),
					new OA\Property(property: 'equipment', ref: '#/components/schemas/SeedTableSummary'),
					new OA\Property(property: 'certificates', ref: '#/components/schemas/SeedTableSummary'),
					new OA\Property(property: 'client_users', ref: '#/components/schemas/SeedTableSummary'),
				]
			),
			new OA\Schema(
				schema: 'EnvelopeSeed', type: 'object',
				properties: [
					new OA\Property(property: 'ok', type: 'boolean', example: true),
					new OA\Property(property: 'data', type: 'object', properties: [
						new OA\Property(property: 'summary', ref: '#/components/schemas/SeedSummary'),
					]),
				]
			),
			new OA\Schema(
				schema: 'UserProfile', type: 'object',
				properties: [
					new OA\Property(property: 'id', type: 'string', format: 'uuid'),
					new OA\Property(property: 'full_name', type: 'string'),
					new OA\Property(property: 'role', type: 'string', enum: ['SUPERADMIN','ADMIN','TECHNICIAN','CLIENT']),
					new OA\Property(property: 'is_active', type: 'boolean'),
					new OA\Property(property: 'deleted_at', type: 'string', format: 'date-time', nullable: true),
					new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
					new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
				]
			),
			new OA\Schema(
				schema: 'Client', type: 'object',
				properties: [
					new OA\Property(property: 'id', type: 'string', format: 'uuid'),
					new OA\Property(property: 'name', type: 'string'),
					new OA\Property(property: 'contact_details', type: 'object', nullable: true),
					new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
				]
			),
			new OA\Schema(
				schema: 'Equipment', type: 'object',
				properties: [
					new OA\Property(property: 'id', type: 'string', format: 'uuid'),
					new OA\Property(property: 'serial_number', type: 'string'),
					new OA\Property(property: 'brand', type: 'string'),
					new OA\Property(property: 'model', type: 'string'),
					new OA\Property(property: 'owner_client_id', type: 'string', format: 'uuid', nullable: true),
					new OA\Property(property: 'equipment_type_id', type: 'integer'),
					new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
				]
			),
			new OA\Schema(
				schema: 'EquipmentType', type: 'object',
				properties: [
					new OA\Property(property: 'id', type: 'integer'),
					new OA\Property(property: 'name', type: 'string'),
				]
			),
			new OA\Schema(
				schema: 'Certificate', type: 'object',
				properties: [
					new OA\Property(property: 'id', type: 'string', format: 'uuid'),
					new OA\Property(property: 'certificate_number', type: 'string'),
					new OA\Property(property: 'equipment_id', type: 'string', format: 'uuid'),
					new OA\Property(property: 'technician_id', type: 'string', format: 'uuid'),
					new OA\Property(property: 'calibration_date', type: 'string', format: 'date'),
					new OA\Property(property: 'next_calibration_date', type: 'string', format: 'date'),
					new OA\Property(property: 'results', type: 'object'),
					new OA\Property(property: 'lab_conditions', type: 'object', nullable: true),
					new OA\Property(property: 'pdf_url', type: 'string', nullable: true),
					new OA\Property(property: 'client_id', type: 'string', format: 'uuid', nullable: true),
					new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
					new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
					new OA\Property(property: 'deleted_at', type: 'string', format: 'date-time', nullable: true),
				]
			),
		]
	)
)]
final class OpenApiSpec {}
