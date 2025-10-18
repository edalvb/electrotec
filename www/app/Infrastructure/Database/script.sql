-- Electrotec DB bootstrap script
-- Creates database, tables, indexes and seeds the initial admin user.
-- Safe to run multiple times (uses IF NOT EXISTS and ON DUPLICATE KEY where applicable).

-- 1) Database
CREATE DATABASE IF NOT EXISTS electrotec_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE electrotec_db;

-- 2) Core tables

-- Users used by the legacy/simple auth (UserRepository)
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  tipo ENUM('admin','client') NOT NULL DEFAULT 'client',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Client profiles (business customers)
CREATE TABLE IF NOT EXISTS clients (
  id CHAR(36) NOT NULL,
  user_id INT UNSIGNED NULL,
  nombre VARCHAR(255) NOT NULL,
  ruc VARCHAR(20) NULL,
  dni VARCHAR(20) NULL,
  email VARCHAR(255) NULL,
  celular VARCHAR(50) NULL,
  direccion VARCHAR(500) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_clients_user (user_id),
  KEY idx_clients_nombre (nombre),
  CONSTRAINT fk_clients_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Equipment types (domain presets)
CREATE TABLE IF NOT EXISTS equipment_types (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(200) NOT NULL UNIQUE,
  resultado_precision ENUM('segundos','lineal') NOT NULL DEFAULT 'segundos',
  resultado_conprisma TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Equipment assigned to clients or generic
CREATE TABLE IF NOT EXISTS equipment (
  id CHAR(36) NOT NULL,
  serial_number VARCHAR(100) NOT NULL,
  brand VARCHAR(100) NULL,
  model VARCHAR(100) NULL,
  equipment_type_id INT UNSIGNED NOT NULL,
  client_id CHAR(36) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_equipment_serial (serial_number),
  KEY idx_equipment_type (equipment_type_id),
  KEY idx_equipment_client (client_id),
  CONSTRAINT fk_equipment_type FOREIGN KEY (equipment_type_id) REFERENCES equipment_types(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_equipment_client FOREIGN KEY (client_id) REFERENCES clients(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Technicians that sign certificates
CREATE TABLE IF NOT EXISTS tecnico (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre_completo VARCHAR(200) NOT NULL,
  cargo VARCHAR(150) NULL,
  path_firma VARCHAR(500) NULL,
  firma_base64 MEDIUMTEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Certificates
CREATE TABLE IF NOT EXISTS certificates (
  id CHAR(36) NOT NULL,
  certificate_number VARCHAR(50) NOT NULL,
  equipment_id CHAR(36) NOT NULL,
  calibrator_id INT UNSIGNED NOT NULL,
  calibration_date DATE NOT NULL,
  next_calibration_date DATE NULL,
  results JSON NULL,
  pdf_url VARCHAR(500) NULL,
  client_id CHAR(36) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_certificate_number (certificate_number),
  KEY idx_cert_equipment (equipment_id),
  KEY idx_cert_client (client_id),
  KEY idx_cert_calibration_date (calibration_date),
  KEY idx_cert_next_cal_date (next_calibration_date),
  CONSTRAINT fk_cert_equipment FOREIGN KEY (equipment_id) REFERENCES equipment(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_cert_technician FOREIGN KEY (calibrator_id) REFERENCES tecnico(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_cert_client FOREIGN KEY (client_id) REFERENCES clients(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Per-year sequence for certificate numbers
CREATE TABLE IF NOT EXISTS certificate_sequences (
  year INT NOT NULL,
  last_number INT NOT NULL DEFAULT 0,
  PRIMARY KEY (year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Environmental conditions for a certificate (1:1)
CREATE TABLE IF NOT EXISTS condiciones_ambientales (
  id_certificado CHAR(36) NOT NULL,
  temperatura_celsius DECIMAL(5,2) NULL,
  humedad_relativa_porc DECIMAL(5,2) NULL,
  presion_atm_mmhg INT NULL,
  PRIMARY KEY (id_certificado),
  CONSTRAINT fk_cond_cert FOREIGN KEY (id_certificado) REFERENCES certificates(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Angular/linear results linked to a certificate (1:N)
CREATE TABLE IF NOT EXISTS resultados (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_certificado CHAR(36) NOT NULL,
  tipo_resultado ENUM('segundos','lineal') NOT NULL DEFAULT 'segundos',
  valor_patron_grados INT NOT NULL DEFAULT 0,
  valor_patron_minutos INT NOT NULL DEFAULT 0,
  valor_patron_segundos INT NOT NULL DEFAULT 0,
  valor_obtenido_grados INT NOT NULL DEFAULT 0,
  valor_obtenido_minutos INT NOT NULL DEFAULT 0,
  valor_obtenido_segundos INT NOT NULL DEFAULT 0,
  precision_val DECIMAL(10,4) NOT NULL DEFAULT 0,
  error_segundos INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_res_cert (id_certificado),
  CONSTRAINT fk_res_cert FOREIGN KEY (id_certificado) REFERENCES certificates(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Distance results linked to a (angular) result id or certificate - we use id_resultado for grouping order
CREATE TABLE IF NOT EXISTS resultados_distancia (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_certificado CHAR(36) NOT NULL,
  id_resultado INT UNSIGNED NULL,
  punto_control_metros DECIMAL(10,3) NOT NULL DEFAULT 0,
  distancia_obtenida_metros DECIMAL(10,3) NOT NULL DEFAULT 0,
  variacion_metros DECIMAL(10,3) NOT NULL DEFAULT 0,
  precision_base_mm INT NOT NULL DEFAULT 0,
  precision_ppm INT NOT NULL DEFAULT 0,
  con_prisma TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_resd_cert (id_certificado),
  KEY idx_resd_res (id_resultado),
  CONSTRAINT fk_resd_cert FOREIGN KEY (id_certificado) REFERENCES certificates(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional associations for client users (used by some repository methods)
CREATE TABLE IF NOT EXISTS user_profiles (
  id CHAR(36) NOT NULL,
  full_name VARCHAR(200) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  signature_image_url VARCHAR(500) NULL,
  role ENUM('admin','client','tech') NOT NULL DEFAULT 'client',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  deleted_at TIMESTAMP NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS client_users (
  id CHAR(36) NOT NULL,
  client_id CHAR(36) NOT NULL,
  user_profile_id CHAR(36) NOT NULL,
  permissions JSON NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_cu_client (client_id),
  KEY idx_cu_user (user_profile_id),
  CONSTRAINT fk_cu_client FOREIGN KEY (client_id) REFERENCES clients(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_cu_user FOREIGN KEY (user_profile_id) REFERENCES user_profiles(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3) Seed: initial admin user (username: admin / password: admin123)
INSERT INTO users (id, username, password_hash, tipo)
VALUES (
  1,
  'admin',
  '$2y$10$hecI4BcuB.x4Q1CcQe5jkeppj/dZwZ4rT2xhmvzi.cJqTg/yzA0JO',
  'admin'
)
ON DUPLICATE KEY UPDATE
  username = VALUES(username),
  password_hash = VALUES(password_hash),
  tipo = VALUES(tipo);
