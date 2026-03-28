# Deploy Hosting

Guia simple para desplegar este proyecto en hosting compartido con esta estructura:

- `html/sc_app` -> proyecto Laravel completo
- `html/sc` -> carpeta publica

## Estructura recomendada

En `html/sc_app` subir:

- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `resources/`
- `routes/`
- `vendor/`
- `storage/` si necesitas conservar ficheros o subidas
- `.env`
- `artisan`
- `composer.json`
- `composer.lock`

En `html/sc` subir:

- todo el contenido de `public/`

## Variables minimas de entorno

Configurar en `.env` del hosting:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com/sc
ASSET_URL=https://tu-dominio.com/sc
APP_PUBLIC_PATH=/ruta/real/html/sc
ADMIN_NAME="Super Admin"
ADMIN_EMAIL=admin@tu-dominio.com
ADMIN_PASSWORD=una_password_segura
MAINTENANCE_RUN_TOKEN=token_largo_y_privado

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

## Comandos despues de subir

Ejecutar:

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan storage:link
```

Si no tienes SSH:

- sube un runner temporal protegido por `MAINTENANCE_RUN_TOKEN`
- ejecutalo por URL
- borralo al terminar

## Flujo para cambios pequenos

Si cambias PHP o Blade:

- subir solo los archivos modificados a `html/sc_app`

Si cambias assets:

- ejecutar `npm run build` en local
- subir `public/build/` a `html/sc/build/`

Si cambias dependencias:

- actualizar `composer.json` y `composer.lock`
- subir `vendor/` a `html/sc_app/vendor/`

Si cambias base de datos:

- subir migraciones nuevas a `html/sc_app/database/migrations/`
- ejecutar `php artisan migrate --force` o el runner web equivalente

Si cambias logica de vistas, contadores, chats o permisos:

- subir archivos modificados de `app/` y `resources/`
- ejecutar `php artisan optimize:clear` o el runner web equivalente

Si cambias herramientas de plataforma o backups:

- subir archivos modificados de `app/Http/Controllers/`, `app/Support/`, `app/Http/Requests/` y `resources/views/platform/`
- ejecutar `php artisan optimize:clear` o el runner web equivalente

Si cambias logistica de ruta o transporte de bolos:

- subir archivos modificados de `app/Support/`, `app/Http/Controllers/`, `app/Http/Requests/`, `app/Models/`, `resources/views/shows/` y `routes/`
- si hay migraciones nuevas de bolos, subirlas a `html/sc_app/database/migrations/`
- ejecutar `php artisan migrate --force` y luego `php artisan optimize:clear` o el runner web equivalente

## No subir normalmente

- `node_modules/`
- `database/database.sqlite`
- `.phpunit.result.cache`
- runners temporales
- logs locales
- caches locales generadas

## Limpieza de seguridad

Si subes scripts temporales para mantenimiento o migraciones:

- usarlos una sola vez
- borrarlos despues del proceso
- no dejar credenciales o tokens por defecto
- no dejar `ADMIN_PASSWORD` debil en produccion

## Validacion final

Comprobar:

- login correcto
- dashboard carga
- dashboard muestra contadores y proximos bolos correctamente
- lista de bolos carga
- lista de bolos muestra alertas y mensajes nuevos
- acceso compartido funciona
- acceso compartido muestra alertas y mensajes nuevos segun el rol del token
- PDF abre
- subida de documentos funciona
- chat por secciones funciona
- ficha de bolo calcula ruta por carretera y muestra el mapa embebido
- modo `avion` muestra los datos manuales de vuelo
- `Plataforma > Usuarios` carga y permite gestionar cuentas
- `Plataforma > Herramientas` muestra chequeos, crea backup y lista backups
