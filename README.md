# Show Control v2

Aplicacion Laravel para gestion de giras, bolos, documentos, accesos compartidos, actividad, alertas, PDF, calendario y mensajeria interna por secciones.

Licencia: [MIT](LICENSE)

## Requisitos

- PHP 8.1 o superior
- MySQL o MariaDB
- Extensiones PHP: `pdo`, `pdo_mysql`, `mbstring`, `openssl`, `json`
- Carpeta `storage/` escribible
- Carpeta `bootstrap/cache/` escribible

## Nota de produccion

Las dependencias de PDF y permisos forman parte del runtime de la aplicacion. En una instalacion de produccion deben estar presentes aunque se instale con optimizacion.

## Instalacion rapida

### Opcion 1. Instalador web

Recomendada para hosting compartido tipo Arsys.

1. Sube el proyecto al hosting.
2. Crea una base de datos vacia desde el panel o `phpMyAdmin`.
3. Asegura que el dominio apunta a la carpeta `public/`.
4. Asegura permisos de escritura en:
   - `storage/`
   - `bootstrap/cache/`
   - `.env` o, si no existe, en la raiz del proyecto para poder crearlo
5. Abre `/install` en el navegador.
6. Rellena:
   - nombre de la aplicacion
   - URL publica
   - datos de la base de datos
   - usuario super admin inicial
7. El instalador:
   - escribe `.env`
   - ejecuta migraciones actuales del proyecto
   - crea roles y permisos
   - configura `ADMIN_NAME`, `ADMIN_EMAIL` y `ADMIN_PASSWORD`
   - crea el primer usuario `super_admin`
   - marca la aplicacion como instalada

Cuando termina, entra directamente al dashboard.

## Instalacion manual

1. Copia `.env.example` a `.env`
2. Ajusta al menos:

```env
APP_NAME="Show Control v2"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com
ASSET_URL=https://tu-dominio.com
APP_PUBLIC_PATH=/ruta/real/a/public
ADMIN_NAME="Super Admin"
ADMIN_EMAIL=admin@tu-dominio.com
ADMIN_PASSWORD=una_password_segura

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

3. Instala dependencias:

```bash
composer install --no-dev --optimize-autoloader
```

4. Genera la clave:

```bash
php artisan key:generate
```

5. Ejecuta migraciones:

```bash
php artisan migrate --force
```

6. Crea roles y permisos:

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder --force
```

7. Crea o verifica el super admin inicial:

- si has definido `ADMIN_NAME`, `ADMIN_EMAIL` y `ADMIN_PASSWORD`, el seeder crea ese `super_admin`
- si no defines esas variables en produccion, el seeder no crea ningun usuario bootstrap

8. Si usas archivos publicos:

```bash
php artisan storage:link
```

## Despliegue en hosting compartido

Puntos importantes:

- La raiz publica del dominio debe ser `public/`
- Si el hosting no permite cambiarla, hay que preparar un `index.php` frontal en la raiz y no es la opcion ideal
- Si lo montas en subcarpeta, por ejemplo `/sc/`, usa:
  - proyecto completo en `html/sc_app`
  - contenido de `public/` en `html/sc`
  - `APP_URL=https://tu-dominio.com/sc`
  - `ASSET_URL=https://tu-dominio.com/sc`
  - `APP_PUBLIC_PATH=/ruta/real/html/sc`
- Si no tienes SSH:
  - sube tambien `vendor/`
  - compila assets antes en local
  - usa un runner temporal protegido por `MAINTENANCE_RUN_TOKEN` para `migrate` y `optimize:clear`
- Si tienes SSH:
  - mejor ejecutar `composer install`, `php artisan migrate --force` y `php artisan storage:link`

## Funcionamiento multiusuario

- Cada usuario gestiona solo sus propias giras, bolos y tokens
- Los usuarios nuevos se registran como `admin`
- El primer usuario creado por el instalador es `super_admin`
- Ser `admin` no da acceso a los datos de otros usuarios, solo a su propio espacio
- `super_admin` accede a `Plataforma > Usuarios` para gestionar cuentas globales

## Correo

- El envio real usa el SMTP global configurado en `.env`
- `Cuenta > Correo` permite configurar:
  - `Hoja de ruta` externa con PDF adjunto
  - `Alerta operativa` con detalle real de alertas del bolo
- `Cuenta > Correo` incluye vista previa real de ambos correos
- Las plantillas por defecto salen traducidas en `es/en` y se pueden restaurar con un boton
- `Bolos > Enviar mail` obliga a revisar preview antes del envio
- `{{show_url}}` usa la URL publica del bolo, no la ficha interna de admin
- La vista previa de `Cuenta > Correo` funciona incluso si aun no existe ningun bolo, usando un ejemplo interno
- `Plataforma > Correo` permite configurar avisos globales como nuevos registros
- El sistema no usa todavia un SMTP distinto por cuenta

## Idioma de interfaz

- La app soporta locale base para `es` y `en`
- `Plataforma > Ajustes` define el idioma por defecto global
- `Cuenta > Preferencias` permite fijar un idioma propio por usuario
- La UI ya esta traducida en los bloques principales:
  - dashboard
  - cuenta
  - plataforma
  - bolos
  - giras
  - calendario
  - importacion ICS
  - accesos compartidos internos y publicos
  - PDF roadmap

## Mensajeria interna y alertas

- Cada bolo dispone de chat persistente por seccion:
  - iluminacion
  - sonido
  - espacio / venue
  - notas generales
- Los mensajes guardan autor y hora
- En acceso compartido, cada token solo ve y usa el chat de las secciones visibles para su rol
- El sistema calcula mensajes no leidos por usuario interno y por token compartido
- Los listados muestran:
  - alertas operativas
  - mensajes nuevos no leidos
- El dashboard muestra contadores globales de alertas y mensajes nuevos

## Importacion desde calendario

Desde `Giras > Importar Google Calendar`:

- pegas una URL `ICS`
- eliges rango de fechas
- se previsualizan eventos
- se importan bolos con formato recomendado `Gira - Lugar`

La importacion:

- crea la gira si no existe
- evita duplicados por `UID` del evento ICS
- guarda trazabilidad del origen

## Logistica de viaje

- Cada bolo puede guardar un `origen de viaje`
- Modos disponibles:
  - `coche`
  - `furgo`
  - `sleeper`
  - `avion`
- Para carretera se calcula:
  - tiempo estimado
  - distancia
  - mapa embebido
  - enlace de ruta
- Para `avion` se usan datos manuales de vuelo y traslados
- Los bolos pasados se muestran como `closed` de forma efectiva en la UI, mails y PDF sin depender de cron

## Vista de mapa de bolos

- `Bolos > Ver mapa` abre una vista geografica separada, sin cargar el dashboard
- El mapa usa la `city` del bolo como punto aproximado
- Los pines se numeran por orden cronologico
- La carga de coordenadas se hace por lotes cortos desde `Actualizar puntos`
- Una vez calculadas, las coordenadas quedan guardadas en la base de datos para que la vista vuelva a abrir rapido

## Resumen publico de bolo

- Cada bolo genera automaticamente una URL publica propia
- Esa URL se usa como `show_url` en correos y plantillas
- El resumen publico muestra informacion operativa del bolo sin exponer la ficha interna
- Incluye estado, horarios, contacto, notas visibles y resumen de ruta o vuelo

## Accesos compartidos

- `Accesos` permite crear enlaces por token sin login
- Cada token puede limitarse por:
  - rol
  - gira
- La parte publica soporta:
  - visualizacion de bolos
  - mini calendario mensual filtrado por token
  - alertas segun visibilidad
  - documentos visibles
  - chat por secciones visibles
  - creacion/edicion/borrado segun permisos del rol
- La parte interna y publica de accesos usa avatares visuales por iniciales
- La portada publica del token combina cabecera operativa, resumen rapido y mini calendario con filtro por dia

## Tests

```bash
php artisan test
```

## Estado actual

Implementado:

- autenticacion
- roles y permisos
- giras
- contactos de gira
- documentos de gira
- bolos
- documentos de bolo
- accesos compartidos por token
- actividad
- alertas
- PDF
- calendario/agenda
- dark mode
- importacion por ICS
- instalador web
- chat persistente por seccion en bolos
- mensajes no leidos por usuario y por token compartido
- contadores de alertas y mensajes nuevos en dashboard y listados
- `Cuenta > Alertas` con persistencia y efecto real en dashboard y listados
- `Cuenta > PDF y branding` con persistencia y efecto real en roadmap PDF
- `Cuenta > Preferencias` con valores por defecto para nuevos bolos
- `Cuenta > Correo` con `Hoja de ruta` externa y `Alerta operativa`
- preview de correos en `Cuenta > Correo` con restauracion de plantillas por defecto
- `Plataforma > Usuarios` con cambio de rol global y activacion/desactivacion
- `Plataforma > Correo` para avisos globales de registro
- `Plataforma > Ajustes` con idioma por defecto de plataforma
- `Plataforma > Herramientas` con chequeos de salud y backup/restauracion de base de datos
- logistica de viaje por bolo con origen configurable, modos `coche`, `furgo`, `sleeper` y `avion`
- calculo de ruta por carretera en la ficha del bolo con mapa embebido, distancia, tiempo y enlace
- datos manuales de vuelo para `avion` y resumen logistico de viaje en el PDF
- vista `Ver mapa` en bolos con sincronizacion de coordenadas por ciudad y carga por lotes
- URL publica propia por bolo para compartir resumen seguro fuera del panel
- infraestructura base de idioma `es/en` con traduccion aplicada a la mayoria de la UI operativa y publica
- cierre efectivo automatico de bolos pasados en la presentacion de estado

## Licencia

Distribuido bajo licencia MIT. Ver [LICENSE](LICENSE).
