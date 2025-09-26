# API Electrotec (Clean + Vertical Slice)

Entrypoints (por defecto expuestos en [http://localhost:8080/](http://localhost:8080/)):

- GET /api/health.php
- GET /api/users.php?action=list&limit=50&offset=0
- GET /api/clients.php?action=list&limit=50&offset=0
- GET /api/equipment.php?action=listByClientId&client_id={UUID}&limit=50&offset=0
- GET /api/certificates.php?action=listByClientId&client_id={UUID}&limit=50&offset=0
- GET /api/certificates.php?action=listForClientUser&user_profile_id={UUID}&limit=50&offset=0
- POST /api/seed.php?token={SEED_TOKEN}

Arquitectura:

- app/Features/`Feature`/{Domain,Application,Infrastructure,Presentation}
- Shared: Config, Http (JsonResponse), Infra DB (PDO Factory)

Notas:

- Las consultas usan PDO con prepared statements.
- El control de permisos para cliente-usuario se delega a la consulta y debe reforzarse en la capa de autenticación/sesión.
