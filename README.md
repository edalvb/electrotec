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
