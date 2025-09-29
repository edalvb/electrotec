# API Electrotec (Clean + Vertical Slice)

Entrypoints (por defecto expuestos en [http://localhost:8080/](http://localhost:8080/)):

- GET /api/health.php
- GET /api/users.php?action=list&limit=50&offset=0
- GET /api/clients.php?action=list&limit=50&offset=0
- GET /api/equipment.php?action=list&limit=50&offset=0
- GET /api/equipment.php?action=listByClientId&client_id={UUID}&limit=50&offset=0
- GET /api/certificates.php?action=listAll&limit=50&offset=0
- GET /api/certificates.php?action=listByClientId&client_id={UUID}&limit=50&offset=0
- GET /api/certificates.php?action=listForClientUser&user_profile_id={UUID}&limit=50&offset=0
- GET /api/dashboard.php?action=overview
- GET /api/dashboard.php?action=coverageByClient
- GET /api/dashboard.php?action=expiringSoon&days={N}
- GET /api/dashboard.php?action=riskRanking&limit={N}
- GET /api/dashboard.php?action=productivityByTechnician
- GET /api/dashboard.php?action=certificatesByMonth&months={N}
- GET /api/dashboard.php?action=distributionByEquipmentType
- GET /api/dashboard.php?action=equipmentWithoutCertificates
- GET /api/dashboard.php?action=failRates&months={N}
- GET /api/dashboard.php?action=missingPdfCertificates&limit={N}
- POST /api/seed.php (token por query `?token=`, header `X-Seed-Token` o body JSON `{ "token": "..." }`) — crea/actualiza el esquema y luego ejecuta la semilla idempotente.

Arquitectura:

- app/Features/`Feature`/{Domain,Application,Infrastructure,Presentation}
- Shared: Config, Http (JsonResponse), Infra DB (PDO Factory)

Notas:

- Las consultas usan PDO con prepared statements.
- Los equipos pueden asociarse con múltiples clientes mediante la tabla pivote `client_equipment`.
- El control de permisos para cliente-usuario se delega a la consulta y debe reforzarse en la capa de autenticación/sesión.
