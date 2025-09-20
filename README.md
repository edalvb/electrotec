# Electrotec

## API Docs (Swagger / OpenAPI)

La documentación de la API se genera automáticamente a partir de anotaciones (@OA) en el código usando `zircote/swagger-php`.

- Spec en YAML: <http://localhost:8080/api/openapi.php?format=yaml>
- Spec en JSON: <http://localhost:8080/api/openapi.php?format=json>

Para que el generador funcione dentro del contenedor, asegúrate de instalar las dependencias PHP una vez:

```bash
composer install
```

Si usas docker-compose, esto se hace dentro del servicio `app` (puedes ejecutar `composer install` dentro del contenedor). A partir de ahí, cualquier cambio en las anotaciones se reflejará automáticamente en el endpoint `openapi.php`.

> Nota: No hay archivos de spec estáticos en el repositorio; todo se produce dinámicamente. Si deseas una UI (Swagger UI) podemos agregar una página que consuma el endpoint anterior, pero por ahora se mantiene fuera del repo según la política de sólo generación automática.
