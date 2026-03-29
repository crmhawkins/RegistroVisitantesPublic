# Hawkins Check-In Webapp

Aplicación web para el registro digital (check-in) de huéspedes, construida con Laravel 11. 
Está diseñada con un enfoque **Mobile-First**, garantizando usabilidad en pantallas pequeñas y navegadores antiguos usando CSS nativo y JavaScript Vanilla sin dependencias.

## Características

- Base de datos lista con campos cifrados (`address`, `email`, `phone`, `document_number`) para cumplimiento de la RGPD.
- Flujo de dos pasos (Paso 1: Foto Frontal/Trasera, Paso 2: Corrección/Validación de datos y firma).
- Servicio simulado de IA (`DocumentExtractionService`) para OCR de documentos, listo para ser conectado a un contenedor local.
- Servicio preparado (`TravelerRegistryService`) listos para futura integración con el Registro de Viajeros de España.
- Interfaz multilenguaje (ES/EN) auto-detectada desde el navegador del huésped.

## Instalación en Producción o Desarrollo

Debido a la ausencia de un entorno local de prueba preconfigurado, el repositorio se instaló clonando la estructura. Para hacerlo funcionar, sigue estos pasos:

1. **Instalar dependencias de PHP:**
   Navega a la carpeta desde tu terminal con PHP/Composer (ej. desde Laragon o WSL) y ejecuta:
   ```bash
   composer install
   ```
   
2. **Generar la clave de aplicación:**
   ```bash
   php artisan key:generate
   ```

3. **Configurar el entorno SQLite (Por defecto):**
   La base de datos SQLite ya está inicializada en `database/database.sqlite`. Ejecuta las migraciones:
   ```bash
   php artisan migrate
   ```

4. **Vincular el almacenamiento privado y público:**
   Asegúrate de ejecutar:
   ```bash
   php artisan storage:link
   ```
   *Nota de seguridad:* Las firmas y DNI se guardan intencionadamente en `storage/app/private/` para **no** ser de acceso público por URL directa, protegiendo así los datos sensibles.

## Modo Mock (Simulación IA)

En tu archivo `.env`, puedes controlar el servicio simulado de IA:
```env
MOCK_AI_EXTRACTION=true
```
Esto habilita un retraso de 2 segundos que devuelve datos falsos (o falla el 20% de las veces) para probar la resiliencia del formulario sin requerir una IA real.

## Arquitectura para Futuras Integraciones

Si deseas implementar la lógica real:
1. **IA OCR:** Ve a `app/Services/DocumentExtractionService.php` y reemplaza el bloque condicional del `$isMockMode` por llamadas `Http::post(...)` a tu motor de IA.
2. **Registro de Viajeros:** Ve a `app/Services/TravelerRegistryService.php`, lee la estructura y mapea el Payload XML/JSON basándote en la API oficial.

Todo el código prioriza la legibilidad, mantenimiento a largo plazo y la máxima compatibilidad en clientes móviles.
