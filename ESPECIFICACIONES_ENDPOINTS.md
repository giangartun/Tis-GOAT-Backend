# Documentación de Endpoints - API 
 Base URL para peticiones al backend

```
http://127.0.0.1:8000/api
```
Comando de instalacion de entorno de apip para php:
```
php artisan install:api
```
---

## 1. Registro de Usuario

### Endpoint:
```
POST http://127.0.0.1:8000/api/usuario/register
```


### Headers

```
Content-Type: application/json
```

### Body (Entrada)
```json
{
  "email": "string (requerido, formato email)",
  "contrasena": "string (requerido, mínimo 6 caracteres)",
  "contrasena_confirmation": "string (debe coincidir)",
  "nombre": "string (requerido)",
  "apellido_paterno": "string (requerido)",
  "apellido_materno": "string (requerido)",
  "biografia": "string (opcional)",
  "foto": "string (opcional)"
}
```

### Respuesta exitosa (201)

```json
{
  "message": "Usuario registrado correctamente",
  "usuario": {
    "id_usuario": "string",
    "email": "string",
    "nombre": "string",
    "apellido_paterno": "string",
    "apellido_materno": "string",
    "biografia": "string|null",
    "foto": "string|null",
    "fecha": "datetime"
  }
}
```

## Errores
400 - Validación:

```json
{
  "errors": {
    "email": ["El correo es obligatorio o inválido"],
    "contrasena": ["Debe tener mínimo 6 caracteres"]
  }
}
```
409 - Conflicto (correo duplicado):

```json
{
  "message": "El correo ya está registrado"
}
```

---

# 2. Login de Usuario

## Endpoint

```
POST http://127.0.0.1:8000/api/usuario/register
```

## Headers

```
Content-Type: application/json
Accept: application/json
```

## Body (Entrada)

```json
{
  "email": "string (requerido, formato email)",
  "contrasena": "string (requerido)"
}
```

## Respuesta exitosa (200)

```json
{
  "message": "Login exitoso",
  "usuario": {
    "id_usuario": "string",
    "email": "string",
    "nombre": "string",
    "apellido_paterno": "string",
    "apellido_materno": "string",
    "biografia": "string|null",
    "foto": "string|null",
    "fecha": "datetime"
  }
}
```

## Errores
400 - Validación:

```json
{
  "errors": {
    "email": ["El correo es obligatorio"],
    "contrasena": ["La contraseña es obligatoria"]
  }
}
```
401 - Credenciales incorrectas:

```json
{
  "message": "Credenciales incorrectas"
}
```