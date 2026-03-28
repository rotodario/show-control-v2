# Roadmap Siguiente Fase

Orden de trabajo recomendado para seguir ampliando Show Control sin mezclar demasiados frentes a la vez.

## Regla de trabajo

Cada bloque debe cerrarse entero antes de abrir el siguiente:

1. implementar
2. probar en local
3. subir a hosting
4. validar en hosting
5. actualizar documentacion si aplica
6. commit y push

## Bloque 1. Cierre de lo ya hecho

Objetivo:
- consolidar correo y sistema de idioma base ya implementados

Checklist:
- probar `Cuenta > Correo`
- probar `Enviar hoja de ruta`
- probar `Enviar alerta`
- probar `Plataforma > Correo`
- probar `Plataforma > Ajustes`
- probar idioma por defecto de plataforma
- probar idioma por usuario en `Cuenta > Preferencias`
- revisar textos raros, labels o mensajes poco claros

Resultado esperado:
- bloque estable
- sin fallos funcionales
- listo para documentar y versionar

## Bloque 2. Traduccion de Cuenta y Plataforma

Objetivo:
- terminar de traducir completamente la UI de `Cuenta` y `Plataforma`

Incluye:
- `Cuenta > Perfil`
- `Cuenta > Alertas`
- `Cuenta > PDF y branding`
- `Cuenta > Preferencias`
- `Cuenta > Correo`
- `Plataforma > Usuarios`
- `Plataforma > Correo`
- `Plataforma > Ajustes`
- `Plataforma > Herramientas`

No incluye todavia:
- bolos
- giras
- vistas publicas

## Bloque 3. Correo UX

Objetivo:
- pulir la experiencia del sistema de correo

Incluye:
- boton de correo de prueba en `Cuenta > Correo`
- boton de correo de prueba en `Plataforma > Correo`
- mensajes de estado mas claros
- ayuda visible sobre SMTP global
- documentacion de `.env` para mail real

## Bloque 4. Traduccion de Bolos y Giras

Objetivo:
- llevar el sistema de idioma a la operativa principal

Incluye:
- listado de bolos
- ficha de bolo
- editar bolo
- PDF
- giras
- calendario
- accesos compartidos internos

## Bloque 5. Limpieza y versionado

Objetivo:
- cerrar la fase con el repo limpio

Incluye:
- actualizar `README.md`
- actualizar `DEPLOY_HOSTING.md`
- revisar instalador
- commit
- push
- tag o release si procede

## Siguiente paso inmediato

El siguiente bloque activo recomendado es:

`Bloque 1. Cierre de lo ya hecho`

No abrir nuevas funciones antes de completar esa validacion.
