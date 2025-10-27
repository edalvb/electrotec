# Electrotec

## API Docs (Swagger / OpenAPI)

La documentación de la API se genera automáticamente a partir de anotaciones (PHP Attributes `#[OA\...]`) en el código usando `zircote/swagger-php`.

- Spec en YAML: `http://localhost:${APP_PORT}/api/openapi.php?format=yaml` (por defecto 8080)
- Spec en JSON: `http://localhost:${APP_PORT}/api/openapi.php?format=json`
- Swagger UI (sirviendo YAML generado): `http://localhost:${APP_PORT}/api/docs/`

El archivo `www/api/docs/index.yaml` se genera desde las anotaciones y está ignorado en Git. Para regenerarlo dentro del contenedor Docker:

```powershell
docker compose exec app sh -c "php /var/www/html/api/openapi.php > /var/www/html/api/docs/index.yaml"
```

También puedes usar la tarea de VS Code “Generate OpenAPI YAML (local)”.

Para que el generador funcione dentro del contenedor, asegúrate de instalar las dependencias PHP una vez:

```bash
composer install
```

Si usas docker-compose, esto se hace dentro del servicio `app` (puedes ejecutar `composer install` dentro del contenedor). A partir de ahí, cualquier cambio en las anotaciones se reflejará automáticamente en el endpoint `openapi.php` y en la página de Swagger UI.

Puertos por defecto (docker-compose actual):

- app: <http://localhost:8080> (configurable via `APP_PORT` en `.env`)
- phpMyAdmin: <http://localhost:8085>

### Dependencias PHP (Composer)

- Instalar dependencias dentro del contenedor (Windows PowerShell):

```powershell
./scripts/composer-install.ps1
```

- Instalar dependencias dentro del contenedor (Bash):

```bash
./scripts/composer-install.sh
```

Esto genera la carpeta `www/vendor/` (excluida del repo vía `.gitignore`). Asegúrate de commitear siempre `www/composer.json` y `www/composer.lock`.

## Dashboard analítico

El panel `www/dashboard.php` ahora consume los nuevos endpoints de `/api/dashboard.php` para mostrar métricas clave, cobertura de clientes, alertas de certificados próximos a vencer, tasas de fallos y distribución del parque de equipos. Asegúrate de ejecutar el seeder (`POST /api/seed.php`) para poblar datos de prueba antes de revisar las visualizaciones.

## Despliegue en cPanel (producción)

Esta guía documenta el procedimiento aplicado para desplegar el sistema en el dominio `electrotecconsulting.com` usando cPanel, sin necesidad de Docker. Es válida para otros hostings con cPanel equivalentes.

### Requisitos

- PHP 8.1 o superior.
- Extensiones activas: pdo_mysql, gd, mbstring, dom, json, fileinfo, openssl, curl, zip, intl.
- MySQL 5.7+/8.0.
- Permisos de escritura para `www/api/certificates/stickers` (755/775).

### 1) Seleccionar versión de PHP y extensiones

En cPanel > Select PHP Version (o MultiPHP Manager) asignar PHP 8.1+ al dominio y activar las extensiones:

```text
pdo_mysql, gd, mbstring, dom, json, fileinfo, openssl, curl, zip, intl
```

### 2) Crear BD y usuario MySQL

En cPanel > MySQL Database Wizard:

- Crear la base de datos (ejemplo): `powertic_electroconsulting_db`.
- Crear el usuario (ejemplo): `powertic_electroconsulting_user` (contraseña segura).
- Otorgar ALL PRIVILEGES al usuario sobre la base de datos.

> Nota: en cPanel los nombres se crean con prefijos (ej.: `usuario_powertic_electroconsulting_db`). Usa los nombres reales que muestre tu cuenta.

### 3) Configurar el Document Root

En cPanel > Domains > Manage para `electrotecconsulting.com`, fijar el Document Root a:

```text
/public_html/electrotecconsulting.com/www
```

De esta forma el fichero `.env` se ubicará un nivel por encima de `www`, fuera del webroot.

### 4) Preparar el archivo `.env`

Crear el archivo `.env` en `/public_html/electrotecconsulting.com/` con la configuración (ejemplo):

```env
DB_HOST=localhost
DB_PORT=3306
MYSQL_DATABASE=<nombre_real_bd>
MYSQL_USER=<usuario_real_bd>
MYSQL_PASSWORD=<password_bd>

APP_HOST=electrotecconsulting.com
APP_PORT=443

SETUP_TOKEN=<token_unico_para_setup>
SEED_TOKEN=<token_unico_para_seed>

# Seguridad JWT
JWT_SECRET_KEY=<clave_larga_y_unica>
```

### 5) Subir el código al Document Root

1. Comprimir la carpeta `www/` en un `.zip`. Incluir la carpeta `vendor/` con dependencias.
2. Subir el `.zip` a `/public_html/electrotecconsulting.com/www` usando el Administrador de Archivos de cPanel.
3. Extraer el `.zip` en esa ruta.

> Si el servidor no dispone de "PHP Composer", subir `www/vendor/` dentro del `.zip` es suficiente. Si sí dispone, puede ejecutarse `composer install --no-dev --optimize-autoloader` en el webroot para generar `vendor/` allí.

### 6) Inicializar la base de datos

Abrir en el navegador (reemplazando los tokens por los definidos en tu `.env`):

- `https://electrotecconsulting.com/api/setup.php?action=init&token=TU_SETUP_TOKEN`
- `https://electrotecconsulting.com/api/seed.php?token=TU_SEED_TOKEN` (opcional, carga datos de ejemplo)

También puedes crear manualmente el usuario administrador (si no usas la semilla) ejecutando el siguiente SQL en tu base de datos:

```sql
-- Usuario admin (username: admin / password: admin123)
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
```

> Importante: En producción cambia la contraseña de `admin` y define una `JWT_SECRET_KEY` única en el `.env`.

### 7) Verificaciones rápidas

- Salud general: `https://electrotecconsulting.com/api/health.php`
- Salud con BD: `https://electrotecconsulting.com/api/health.php?db=1`
- Swagger UI (estático): `https://electrotecconsulting.com/api/docs/index.html`
- OpenAPI dinámico:
	- YAML (fallback, no requiere Composer): `https://electrotecconsulting.com/api/openapi.php?format=yaml`
	- JSON (requiere `zircote/swagger-php` en `vendor/`): `https://electrotecconsulting.com/api/openapi.php?format=json`
- Login web: `https://electrotecconsulting.com/login.php` (si usaste la semilla: `admin` / `abc123`; con el SQL superior: `admin` / `admin123`).

### Notas operativas

- Si el QR del sticker usara `http://` en lugar de `https://`, el código ya detecta el esquema del servidor y omite los puertos por defecto; ajusta `APP_PORT` a `443` y activa la redirección HTTPS del dominio.
- Si `vendor/` no existe, `auth.php` lanzará `Class "Firebase\\JWT\\JWT" not found`. Solución: subir `vendor/` o instalar dependencias.
- La carpeta `www/api/certificates/stickers` cachea imágenes; asegúrate de que sea escribible.
