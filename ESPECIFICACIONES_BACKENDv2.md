# 📁 TIS-GOAT-BACKEND — Documentación de Estructura del Proyecto

> Proyecto backend desarrollado con **Laravel (PHP)**, con soporte de **Vite** para assets del frontend y estructura estándar de aplicación web moderna.

---

## 🗂️ Estructura General de Carpetas

```
TIS-GOAT-BACKEND/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
├── vendor/
├── .editorconfig
├── .env
├── .env.example
├── .gitattributes
├── .gitignore
├── artisan
├── composer.json
├── composer.lock
├── METRICAS-COMMIT.md
├── package.json
├── phpunit.xml
├── README.md
└── vite.config.js
```

---

## 📂 Carpetas Principales

### `app/`
**Núcleo de la aplicación Laravel.**

Contiene toda la lógica de negocio del sistema. Se organiza internamente en subcarpetas como:
- `Models/` — Modelos Eloquent que representan las tablas de la base de datos.
- `Http/Controllers/` — Controladores que gestionan las peticiones HTTP.
- `Http/Middleware/` — Filtros que se ejecutan antes o después de las peticiones.
- `Http/Requests/` — Clases de validación de formularios y entradas.
- `Providers/` — Service Providers que registran servicios en el contenedor de Laravel.
- `Exceptions/` — Manejo personalizado de errores y excepciones.

---

### `bootstrap/`
**Arranque e inicialización del framework.**

Contiene el archivo `app.php` que crea la instancia de la aplicación Laravel y configura los bindings fundamentales. También incluye la carpeta `cache/` donde se almacenan archivos de configuración y rutas cacheadas para mejorar el rendimiento.

---

### `config/`
**Archivos de configuración del sistema.**

Cada archivo `.php` dentro de esta carpeta configura un aspecto específico de la aplicación:
- `app.php` — Configuración general (nombre, entorno, zona horaria, idioma).
- `database.php` — Conexiones a bases de datos.
- `auth.php` — Guardas y proveedores de autenticación.
- `mail.php` — Configuración del servicio de correo.
- `filesystems.php` — Discos de almacenamiento (local, S3, etc.).
- `cors.php` — Políticas de acceso entre dominios (CORS).
- Entre otros según las necesidades del proyecto.

---

### `database/`
**Todo lo relacionado con la base de datos.**

- `migrations/` — Scripts que crean y modifican tablas de forma versionada.
- `seeders/` — Datos de prueba o iniciales que se insertan en la base de datos.
- `factories/` — Fábricas para generar datos falsos en pruebas automatizadas.

---

### `public/`
**Punto de entrada público de la aplicación.**

Es la única carpeta expuesta al servidor web. Contiene:
- `index.php` — Archivo de entrada principal de Laravel.
- `assets/` — Archivos estáticos compilados (JS, CSS, imágenes) generados por Vite.
- `.htaccess` — Configuración de redireccionamiento para Apache.

> ⚠️ El servidor web (Apache/Nginx) debe apuntar a esta carpeta.

---

### `resources/`
**Recursos sin compilar del frontend y vistas.**

- `views/` — Plantillas Blade (motor de plantillas de Laravel).
- `js/` — Archivos JavaScript fuente (antes de compilar con Vite).
- `css/` — Hojas de estilo fuente (antes de compilar).
- `lang/` — Archivos de traducción e internacionalización.

---

### `routes/`
**Definición de todas las rutas del sistema.**

- `api.php` — Rutas de la API REST (prefijo `/api`, sin estado de sesión).
- `web.php` — Rutas web tradicionales con soporte de sesiones y cookies.
- `console.php` — Comandos personalizados de Artisan.
- `channels.php` — Canales de broadcasting en tiempo real.

---

### `storage/`
**Almacenamiento interno de la aplicación.**

- `app/` — Archivos generados por la aplicación (uploads, reportes, etc.).
- `framework/` — Caché, sesiones y vistas compiladas por Laravel.
- `logs/` — Archivos de registro (`laravel.log`) para depuración y monitoreo.

> ⚠️ Esta carpeta no debe ser accesible públicamente. Para servir archivos usa `storage:link`.

---

### `tests/`
**Pruebas automatizadas del sistema.**

- `Feature/` — Pruebas de integración que simulan peticiones HTTP completas.
- `Unit/` — Pruebas unitarias de clases y métodos individuales.
- `TestCase.php` — Clase base para todas las pruebas del proyecto.

---

### `vendor/`
**Dependencias de PHP gestionadas por Composer.**

Contiene todos los paquetes de terceros instalados. Esta carpeta es generada automáticamente y **no debe modificarse ni versionarse en Git**.

---

## 📄 Archivos Raíz Importantes

| Archivo | Descripción |
|---|---|
| `.editorconfig` | Define reglas de formato de código (indentación, charset, saltos de línea) para mantener consistencia entre editores y desarrolladores. |
| `.env` | **Variables de entorno del sistema.** Contiene credenciales, claves secretas, configuración de base de datos y servicios. **Nunca se sube al repositorio.** |
| `.env.example` | Plantilla pública del archivo `.env`. Documenta qué variables se necesitan sin exponer valores reales. Se versiona en Git como guía para otros desarrolladores. |
| `.gitattributes` | Configura el comportamiento de Git para ciertos tipos de archivos (normalización de saltos de línea, atributos de diff, etc.). |
| `.gitignore` | Lista de archivos y carpetas que Git debe ignorar (`/vendor`, `.env`, `/node_modules`, `/storage/logs`, etc.). |
| `artisan` | **CLI de Laravel.** Ejecutable PHP que permite correr comandos del framework: migraciones, seeders, cacheo, generación de código, etc. Ej: `php artisan migrate`. |
| `composer.json` | Define las dependencias de PHP del proyecto y metadatos (nombre, versión, autoload). Es el equivalente PHP del `package.json`. |
| `composer.lock` | Registra las versiones exactas instaladas de cada paquete PHP. Garantiza instalaciones reproducibles en todos los entornos. |
| `METRICAS-COMMIT.md` | Documento de seguimiento de métricas de commits del equipo de desarrollo. Registro de avances, convenciones o estadísticas del flujo de trabajo Git. |
| `package.json` | Define las dependencias de Node.js del proyecto (Vite, plugins, etc.) y scripts de compilación de assets del frontend. |
| `phpunit.xml` | Archivo de configuración de **PHPUnit** (framework de pruebas). Define suites de tests, variables de entorno de prueba y cobertura de código. |
| `README.md` | Documentación principal del proyecto: descripción, instrucciones de instalación, uso y contribución. Primer archivo que lee cualquier desarrollador nuevo. |
| `vite.config.js` | Configuración de **Vite**, el bundler de assets. Define los archivos de entrada (JS/CSS), plugins activos y la integración con Laravel a través del plugin oficial. |

---

## ⚙️ Flujo General del Sistema

```
Petición HTTP
     │
     ▼
 public/index.php          ← Punto de entrada
     │
     ▼
 bootstrap/app.php         ← Inicializa Laravel
     │
     ▼
 routes/api.php o web.php  ← Enruta la petición
     │
     ▼
 app/Http/Middleware/       ← Filtros (auth, throttle, etc.)
     │
     ▼
 app/Http/Controllers/      ← Lógica del controlador
     │
     ▼
 app/Models/                ← Interacción con la base de datos
     │
     ▼
 database/                  ← Esquema y datos (migrations/seeders)
     │
     ▼
 Respuesta JSON / Vista Blade
```

---

*Documentación generada para el proyecto **TIS-GOAT-BACKEND** — Laravel + Vite*