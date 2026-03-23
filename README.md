<div align="center">

# TIS-GOAT-Backend

**Taller de Ingeniería de Software — Gestión de Portafolios Académicos**

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Neon-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)
![Drive](https://img.shields.io/badge/Google_Drive-Storage-4285F4?style=for-the-badge&logo=googledrive&logoColor=white)

</div>

---

## 1. Descripción

Sistema backend para la gestión de portafolios académicos de estudiantes y docentes universitarios. Proporciona una API REST que permite administrar usuarios, plantillas de portafolio, almacenamiento de archivos multimedia y registro de actividades del sistema.

---

## 2. Stack tecnológico

| Capa | Tecnología | Uso |
|---|---|---|
| Framework | Laravel 11 | API REST principal |
| Base de datos | PostgreSQL (Neon.tech) | Almacenamiento de datos |
| Archivos | Cloudinary / R2 | Fotos, videos y documentos |
| Frontend assets | Vite | Compilación de recursos |
| Testing | PHPUnit | Pruebas automatizadas |

---

## 3. Estructura del proyecto 📁 (MODIFICABLE POR GOAT)

```
TIS-GOAT-BACKEND/
├── app/
│   ├── Http/Controllers/     # Controladores de la API
│   └── Models/               # Modelos Eloquent
│       ├── Usuario.php
│       ├── Administrador.php
│       ├── RegistroActividad.php
│       ├── Plantilla.php
│       └── ParametrosSistema.php
├── database/
│   ├── migrations/           # Estructura de tablas
│   ├── seeders/              # Datos iniciales
│   └── factories/            # Datos de prueba
├── routes/
│   └── api.php               # Definición de endpoints
└── storage/                  # Archivos temporales locales
```

---

## 4. Instalación

### Requisitos previos
- PHP >= 8.2
- Composer
- Node.js >= 18

### Pasos desde terminal bash

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/TIS-GOAT-Backend.git
cd TIS-GOAT-Backend

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias Node
npm install

# 4. Configurar variables de entorno
cp .env.example .env
php artisan key:generate

# 5. Ejecutar migraciones
php artisan migrate

# 6. (Opcional) Poblar con datos de prueba
php artisan migrate:fresh --seed
```

---

## 5. Variables de entorno

Copia `.env.example` a `.env` y configura las siguientes variables:

```env
# Base de datos (Neon.tech)
DB_CONNECTION=pgsql
DB_URL=postgres://usuario:contraseña@host.neon.tech/neondb?sslmode=require

# Cloudinary (almacenamiento de imágenes y videos)
CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name

# Cloudflare R2 (almacenamiento de documentos)
R2_ACCESS_KEY_ID=
R2_SECRET_ACCESS_KEY=
R2_BUCKET=
R2_URL=
```

---

## 6. Base de datos (MODIFICABLE POR GOAT)

### Tablas principales

| Tabla | Descripción |
|---|---|
| `usuario` | Usuarios del sistema (estudiantes/docentes) |
| `administrador` | Administradores del sistema |
| `registro_actividad` | Log de eventos por usuario |
| `plantilla` | Plantillas de portafolio disponibles |
| `parametros_sistema` | Configuración general del sistema |

### Comandos útiles

```bash
php artisan migrate:status       # Ver estado de migraciones
php artisan migrate              # Ejecutar migraciones pendientes
php artisan migrate:fresh --seed # Reiniciar BD con datos de prueba
php artisan migrate:rollback     # Deshacer último batch
```

---

## 7. API Endpoints (MODIFICABLE POR GOAT)

---

## 8. Documentación adicional (MODIFICABLE POR GOAT)

| Documento | Descripción |
|---|---|
| [📋 Especificaciones Backend](./ESPECIFICACIONES_BACKEND.MD) | Especificaciones técnicas del sistema |
| [📊 Métricas de Commits](./METRICAS_COMMITS.md) | Registro y métricas del equipo |

---

## 9. Equipo GOAT (MODIFICABLE POR GOAT)

| Integrante | Rol |
|---|---|
| [![GitHub](https://img.shields.io/badge/-AdrianVallejosFlores-181717?style=flat&logo=github)](https://github.com/AdrianVallejosFlores) | Backend Developer / Database Manager |
| [![GitHub](https://img.shields.io/badge/-Usuario2-181717?style=flat&logo=github)](https://github.com/usuario2) | Backend Developer |
| [![GitHub](https://img.shields.io/badge/-Usuario3-181717?style=flat&logo=github)](https://github.com/usuario3) | Backend Developer |