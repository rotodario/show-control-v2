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

MAIL_MAILER=smtp
MAIL_HOST=tu-servidor-smtp
MAIL_PORT=465
MAIL_USERNAME=tu-correo@tu-dominio.com
MAIL_PASSWORD=tu_password_smtp
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=tu-correo@tu-dominio.com
MAIL_FROM_NAME="Show Control"
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

Si cambias correo operativo, correo global o plantillas email:

- subir archivos modificados de `app/Mail/`, `app/Listeners/`, `app/Support/`, `app/Http/Controllers/`, `app/Http/Requests/`, `app/Models/`, `resources/views/account/`, `resources/views/platform/`, `resources/views/emails/` y `routes/`
- si hay migraciones nuevas de correo, subirlas a `html/sc_app/database/migrations/`
- ejecutar `php artisan migrate --force` y luego `php artisan optimize:clear` o el runner web equivalente
- si cambias previews o placeholders de `Cuenta > Correo`, revisar tambien `Bolos > Enviar mail`

Si cambias idioma o traducciones:

- subir archivos modificados de `lang/`, `config/app.php`, `app/Http/Middleware/`, `app/Http/Kernel.php`, `app/Http/Controllers/`, `app/Http/Requests/`, `app/Models/` y las vistas afectadas
- si hay migraciones nuevas de preferencias o ajustes de locale, subirlas a `html/sc_app/database/migrations/`
- ejecutar `php artisan migrate --force` y luego `php artisan optimize:clear` o el runner web equivalente

Si cambias accesos compartidos internos o publicos:

- subir archivos modificados de `app/Http/Controllers/SharedAccessController.php`, `app/Http/Controllers/PublicSharedAccessController.php`, `app/Models/SharedAccess.php`, `app/Support/SharedAccessService.php`, `resources/views/shared-accesses/`, `resources/views/public-access/`, `resources/views/components/public-access-layout.blade.php`, `lang/` y `routes/`
- ejecutar `php artisan optimize:clear` o el runner web equivalente
- si cambias el mini calendario publico o avatares, validar tambien la portada del token en movil

Si cambias logistica de ruta o transporte de bolos:

- subir archivos modificados de `app/Support/`, `app/Http/Controllers/`, `app/Http/Requests/`, `app/Models/`, `resources/views/shows/` y `routes/`
- si hay migraciones nuevas de bolos, subirlas a `html/sc_app/database/migrations/`
- ejecutar `php artisan migrate --force` y luego `php artisan optimize:clear` o el runner web equivalente

Si cambias la URL publica de resumen de bolo o el placeholder `show_url`:

- subir archivos modificados de `app/Models/Show.php`, `app/Http/Controllers/PublicShowController.php`, `app/Mail/`, `app/Support/`, `resources/views/public-shows/`, `resources/views/account/`, `lang/` y `routes/`
- si hay migracion nueva del token publico del bolo, subirla a `html/sc_app/database/migrations/`
- ejecutar `php artisan migrate --force` y luego `php artisan optimize:clear` o el runner web equivalente

Si cambias la vista `Bolos > Ver mapa` o la persistencia de coordenadas por ciudad:

- subir archivos modificados de `app/Support/OpenStreetMapRouteService.php`, `app/Http/Controllers/ShowController.php`, `app/Models/Show.php`, `resources/views/shows/`, `lang/` y `routes/`
- si hay migraciones nuevas de coordenadas, subirlas a `html/sc_app/database/migrations/`
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
- `Bolos > Ver mapa` carga y permite actualizar coordenadas por ciudad en lotes
- `Plataforma > Usuarios` carga y permite gestionar cuentas
- `Plataforma > Correo` carga y puede enviar avisos globales si el SMTP esta bien configurado
- `Plataforma > Ajustes` carga y el idioma por defecto cambia la UI compartida
- `Plataforma > Herramientas` muestra chequeos, crea backup y lista backups
- `Cuenta > Correo` carga y puede enviar `Hoja de ruta` y `Alerta operativa`
- `Cuenta > Correo` muestra previews y permite restaurar plantillas por defecto
- `Accesos` carga y permite crear/revocar enlaces por token
- el acceso publico por token respeta idioma, permisos y visibilidad
- el acceso publico por token muestra cabecera, avatares y mini calendario sin romper en movil
- la URL publica propia de un bolo abre correctamente y `{{show_url}}` en los mails apunta ahi
- `Tour` y `Dashboard` muestran correctamente las notas de giras creadas por importacion ICS segun el idioma activo
- el footer se integra bien en light/dark mode
