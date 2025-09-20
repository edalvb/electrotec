<?php
/**
 * @OA\Info(
 *   version="0.2.0",
 *   title="Electrotec API",
 *   description="Documentación generada automáticamente desde anotaciones PHPDoc."
 * )
 *
 * @OA\Server(
 *   url="http://localhost:8080",
 *   description="Entorno local (docker-compose)"
 * )
 *
 * @OA\Schema(schema="EnvelopeError",
 *   type="object",
 *   @OA\Property(property="ok", type="boolean", example=false),
 *   @OA\Property(property="message", type="string"),
 *   @OA\Property(property="details")
 * )
 *
 * @OA\Schema(schema="EnvelopeHealth",
 *   type="object",
 *   @OA\Property(property="ok", type="boolean", example=true),
 *   @OA\Property(property="data", type="object",
 *     @OA\Property(property="status", type="string"),
 *     @OA\Property(property="time", type="string", format="date-time")
 *   )
 * )
 *
 * @OA\Schema(schema="EnvelopeUsers",
 *   type="object",
 *   @OA\Property(property="ok", type="boolean", example=true),
 *   @OA\Property(property="data", type="array",
 *     @OA\Items(ref="#/components/schemas/UserProfile")
 *   )
 * )
 *
 * @OA\Schema(schema="EnvelopeClients",
 *   type="object",
 *   @OA\Property(property="ok", type="boolean", example=true),
 *   @OA\Property(property="data", type="array",
 *     @OA\Items(ref="#/components/schemas/Client")
 *   )
 * )
 *
 * @OA\Schema(schema="EnvelopeClient",
 *   type="object",
 *   @OA\Property(property="ok", type="boolean", example=true),
 *   @OA\Property(property="data", ref="#/components/schemas/Client")
 * )
 *
 * @OA\Schema(schema="EnvelopeEquipmentList",
 *   type="object",
 *   @OA\Property(property="ok", type="boolean", example=true),
 *   @OA\Property(property="data", type="array",
 *     @OA\Items(ref="#/components/schemas/Equipment")
 *   )
 * )
 *
 * @OA\Schema(schema="EnvelopeEquipment",
 *   type="object",
 *   @OA\Property(property="ok", type="boolean", example=true),
 *   @OA\Property(property="data", ref="#/components/schemas/Equipment")
 * )
 *
 * @OA\Schema(schema="EnvelopeEquipmentTypes",
 *   type="object",
 *   @OA\Property(property="ok", type="boolean", example=true),
 *   @OA\Property(property="data", type="array",
 *     @OA\Items(ref="#/components/schemas/EquipmentType")
 *   )
 * )
 *
 * @OA\Schema(schema="EnvelopeCertificates",
 *   type="object",
 *   @OA\Property(property="ok", type="boolean", example=true),
 *   @OA\Property(property="data", type="array",
 *     @OA\Items(ref="#/components/schemas/Certificate")
 *   )
 * )
 *
 * @OA\Schema(schema="UserProfile",
 *   type="object",
 *   @OA\Property(property="id", type="string", format="uuid"),
 *   @OA\Property(property="full_name", type="string"),
 *   @OA\Property(property="role", type="string", enum={"SUPERADMIN","ADMIN","TECHNICIAN","CLIENT"}),
 *   @OA\Property(property="is_active", type="boolean"),
 *   @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(schema="Client",
 *   type="object",
 *   @OA\Property(property="id", type="string", format="uuid"),
 *   @OA\Property(property="name", type="string"),
 *   @OA\Property(property="contact_details", type="object", nullable=true),
 *   @OA\Property(property="created_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(schema="Equipment",
 *   type="object",
 *   @OA\Property(property="id", type="string", format="uuid"),
 *   @OA\Property(property="serial_number", type="string"),
 *   @OA\Property(property="brand", type="string"),
 *   @OA\Property(property="model", type="string"),
 *   @OA\Property(property="owner_client_id", type="string", format="uuid", nullable=true),
 *   @OA\Property(property="equipment_type_id", type="integer"),
 *   @OA\Property(property="created_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(schema="EquipmentType",
 *   type="object",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="name", type="string")
 * )
 *
 * @OA\Schema(schema="Certificate",
 *   type="object",
 *   @OA\Property(property="id", type="string", format="uuid"),
 *   @OA\Property(property="certificate_number", type="string"),
 *   @OA\Property(property="equipment_id", type="string", format="uuid"),
 *   @OA\Property(property="technician_id", type="string", format="uuid"),
 *   @OA\Property(property="calibration_date", type="string", format="date"),
 *   @OA\Property(property="next_calibration_date", type="string", format="date"),
 *   @OA\Property(property="results", type="object"),
 *   @OA\Property(property="lab_conditions", type="object", nullable=true),
 *   @OA\Property(property="pdf_url", type="string", nullable=true),
 *   @OA\Property(property="client_id", type="string", format="uuid", nullable=true),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time"),
 *   @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
 * )
 */
