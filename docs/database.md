# Estructura lógica de la base de datos — Clientes como usuarios (nuevo diseño)

> **Resumen:** diseño de base de datos **nuevo**, pensado para que los clientes puedan ser usuarios con acceso limitado: solo pueden ver certificados y únicamente los asociados a su cliente.

---

## Resumen y propósito

Este documento describe la estructura lógica de la base de datos para una implementación desde cero. Está adaptado para un stack PHP + MySQL, incluyendo tablas, columnas, tipos de datos sugeridos, claves primarias y foráneas, índices y notas de implementación.

### Decisiones principales

* Separar la identidad (`user_profiles`) del perfil de la aplicación.
* Permitir que los clientes tengan usuarios con permisos específicos (`client_users`).
* Usar UUIDs para identificadores públicos donde sea útil.
* Mantener campos de auditoría (`created_at`, `updated_at`, `deleted_at`).
* Incluir columna `client_id` en `certificates` para facilitar consultas y permisos.

---

## Tablas y relaciones (modelo lógico)

### 1) user\_profiles

* id: CHAR(36) PRIMARY KEY
* full\_name: VARCHAR(255) NOT NULL
* signature\_image\_url: VARCHAR(2048) NULL
* role: ENUM('SUPERADMIN','ADMIN','TECHNICIAN','CLIENT') NOT NULL DEFAULT 'TECHNICIAN'
* is\_active: BOOLEAN NOT NULL DEFAULT TRUE
* deleted\_at: DATETIME NULL
* created\_at: DATETIME NOT NULL DEFAULT CURRENT\_TIMESTAMP
* updated\_at: DATETIME NOT NULL DEFAULT CURRENT\_TIMESTAMP ON UPDATE CURRENT\_TIMESTAMP

**Notas:** Una cuenta con `role='CLIENT'` representa un usuario ligado a un cliente.

---

### 2) clients

* id: CHAR(36) PRIMARY KEY
* name: VARCHAR(255) NOT NULL
* contact\_details: JSON NULL
* created\_at: DATETIME NOT NULL DEFAULT CURRENT\_TIMESTAMP

---

### 3) client\_users

* id: CHAR(36) PRIMARY KEY
* client\_id: CHAR(36) NOT NULL REFERENCES clients(id) ON DELETE CASCADE
* user\_profile\_id: CHAR(36) NOT NULL REFERENCES user\_profiles(id) ON DELETE CASCADE
* permissions: JSON NOT NULL  -- ejemplo: `{ "view_certificates": true, "only_own_certificates": true }`
* created\_at: DATETIME NOT NULL DEFAULT CURRENT\_TIMESTAMP

Índices:

* UNIQUE(user\_profile\_id, client\_id)
* INDEX(client\_id)

---

### 4) equipment\_types

* id: INT AUTO\_INCREMENT PRIMARY KEY
* name: VARCHAR(255) NOT NULL UNIQUE

---

### 5) equipment

* id: CHAR(36) PRIMARY KEY
* serial\_number: VARCHAR(255) NOT NULL UNIQUE
* brand: VARCHAR(255) NOT NULL
* model: VARCHAR(255) NOT NULL
* owner\_client\_id: CHAR(36) NULL REFERENCES clients(id) ON DELETE SET NULL
* equipment\_type\_id: INT NOT NULL REFERENCES equipment\_types(id) ON DELETE RESTRICT
* created\_at: DATETIME NOT NULL DEFAULT CURRENT\_TIMESTAMP

---

### 6) certificates

* id: CHAR(36) PRIMARY KEY
* certificate\_number: VARCHAR(255) NOT NULL UNIQUE
* equipment\_id: CHAR(36) NOT NULL REFERENCES equipment(id) ON DELETE RESTRICT
* technician\_id: CHAR(36) NOT NULL REFERENCES user\_profiles(id) ON DELETE RESTRICT
* calibration\_date: DATE NOT NULL
* next\_calibration\_date: DATE NOT NULL
* results: JSON NOT NULL
* lab\_conditions: JSON NULL
* pdf\_url: VARCHAR(2048) NULL
* client\_id: CHAR(36) NULL REFERENCES clients(id) ON DELETE SET NULL
* created\_at: DATETIME NOT NULL DEFAULT CURRENT\_TIMESTAMP
* updated\_at: DATETIME NOT NULL DEFAULT CURRENT\_TIMESTAMP ON UPDATE CURRENT\_TIMESTAMP
* deleted\_at: DATETIME NULL

Índices recomendados:

* INDEX(idx\_certificates\_client\_id) ON certificates(client\_id)
* INDEX(idx\_certificates\_equipment\_id) ON certificates(equipment\_id)
* INDEX(idx\_certificates\_number) ON certificates(certificate\_number)

---

## Triggers y procedimientos recomendados

### 1) Sincronización client\_id en certificados (opcional)

```sql
DELIMITER $$
CREATE TRIGGER certificates_before_insert
BEFORE INSERT ON certificates
FOR EACH ROW
BEGIN
  IF NEW.client_id IS NULL THEN
    SET NEW.client_id = (
      SELECT owner_client_id FROM equipment WHERE id = NEW.equipment_id
    );
  END IF;
END$$

CREATE TRIGGER certificates_before_update
BEFORE UPDATE ON certificates
FOR EACH ROW
BEGIN
  IF NEW.equipment_id <> OLD.equipment_id THEN
    SET NEW.client_id = (
      SELECT owner_client_id FROM equipment WHERE id = NEW.equipment_id
    );
  END IF;
END$$
DELIMITER ;
```

### 2) Procedimiento para obtener certificados de un cliente

```sql
DELIMITER $$
CREATE PROCEDURE get_certificates_for_client_user(IN p_user_profile_id CHAR(36))
BEGIN
  SELECT c.*
  FROM client_users cu
  JOIN certificates c ON c.client_id = cu.client_id
  WHERE cu.user_profile_id = p_user_profile_id
    AND JSON_EXTRACT(cu.permissions, '$.view_certificates') = true;
END$$
DELIMITER ;
```

> La autorización debe reforzarse en la capa de aplicación.

---

## DDL ejemplo (MySQL)

```sql
CREATE TABLE user_profiles (
    id CHAR(36) PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    signature_image_url VARCHAR(2048),
    role ENUM('SUPERADMIN','ADMIN','TECHNICIAN','CLIENT') NOT NULL DEFAULT 'TECHNICIAN',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    deleted_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE clients (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact_details JSON,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE client_users (
    id CHAR(36) PRIMARY KEY,
    client_id CHAR(36) NOT NULL,
    user_profile_id CHAR(36) NOT NULL,
    permissions JSON NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cu_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    CONSTRAINT fk_cu_user FOREIGN KEY (user_profile_id) REFERENCES user_profiles(id) ON DELETE CASCADE,
    UNIQUE (user_profile_id, client_id)
);

CREATE TABLE equipment_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE equipment (
    id CHAR(36) PRIMARY KEY,
    serial_number VARCHAR(255) NOT NULL UNIQUE,
    brand VARCHAR(255) NOT NULL,
    model VARCHAR(255) NOT NULL,
    owner_client_id CHAR(36) NULL,
    equipment_type_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_client_id) REFERENCES clients(id) ON DELETE SET NULL,
    FOREIGN KEY (equipment_type_id) REFERENCES equipment_types(id) ON DELETE RESTRICT
);

CREATE TABLE certificates (
    id CHAR(36) PRIMARY KEY,
    certificate_number VARCHAR(255) NOT NULL UNIQUE,
    equipment_id CHAR(36) NOT NULL,
    technician_id CHAR(36) NOT NULL,
    calibration_date DATE NOT NULL,
    next_calibration_date DATE NOT NULL,
    results JSON NOT NULL,
    lab_conditions JSON,
    pdf_url VARCHAR(2048),
    client_id CHAR(36) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE RESTRICT,
    FOREIGN KEY (technician_id) REFERENCES user_profiles(id) ON DELETE RESTRICT,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL
);

CREATE INDEX idx_certificates_client_id ON certificates(client_id);
CREATE INDEX idx_certificates_equipment_id ON certificates(equipment_id);
CREATE INDEX idx_certificates_number ON certificates(certificate_number);
CREATE INDEX idx_user_profiles_deleted_at ON user_profiles(deleted_at);
```
