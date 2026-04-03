# Documentación de Endpoints - API

Base URL para peticiones al backend:
```
http://127.0.0.1:8000/api
```

Comando de instalacion de entorno de api para php:
```
php artisan install:api
```

Comando para ver los endpoints activos en el backend:
```
php artisan route:list
```

---

## 1. Pre-Registro de Usuario

### Endpoint
```
POST http://127.0.0.1:8000/api/usuario/pre-registro
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
  "contrasena_confirmation": "string (debe coincidir con contrasena)",
  "nombre": "string (requerido, máximo 100 caracteres)",
  "apellido_paterno": "string (requerido, máximo 100 caracteres)",
  "apellido_materno": "string (requerido, máximo 100 caracteres)",
  "biografia": "string (opcional)",
  "foto": "string (opcional)"
}
```

### Respuesta exitosa (200)
```json
{
  "message": "Revisa tu correo para completar el registro. El enlace expira en 5 minutos."
}
```

### Errores

400 - Validación:
```json
{
  "errors": {
    "email": ["El correo es obligatorio o ya está registrado"],
    "contrasena": ["La contraseña debe tener al menos 6 caracteres"],
    "contrasena_confirmation": ["Las contraseñas no coinciden"],
    "nombre": ["El nombre es obligatorio"],
    "apellido_paterno": ["El apellido paterno es obligatorio"],
    "apellido_materno": ["El apellido materno es obligatorio"]
  }
}
```

---

## 2. Verificación de Email

### Endpoint
```
GET http://127.0.0.1:8000/api/usuario/verificar-email/{token}
```

### Descripción
El usuario recibe este link en su correo. Al hacer clic, el backend valida el token y recién crea el usuario en la base de datos. El token expira en 5 minutos.

### Parámetros de URL
```
token: string (requerido, token enviado al correo)
```

### Respuesta exitosa (201)
```json
{
  "message": "Email verificado. Ya puedes iniciar sesión."
}
```

### Errores

400 - Token inválido o expirado:
```json
{
  "message": "Token inválido o expirado."
}
```

---

## 3. Login de Usuario

### Endpoint
```
POST http://127.0.0.1:8000/api/usuario/login
```

### Headers
```
Content-Type: application/json
Accept: application/json
```

### Body (Entrada)
```json
{
  "email": "string (requerido, formato email)",
  "contrasena": "string (requerido)"
}
```

### Respuesta exitosa (200)
```json
{
  "message": "Login exitoso",
  "token": "string (JWT para usar en requests protegidos)",
  "usuario": {
    "id_usuario": "string",
    "nombre": "string",
    "email": "string"
  }
}
```

### Uso del token en requests posteriores
```
Authorization: Bearer {token}
```

### Errores

400 - Validación:
```json
{
  "errors": {
    "email": ["El correo es obligatorio."],
    "contrasena": ["La contraseña es obligatoria."]
  }
}
```

401 - Credenciales incorrectas:
```json
{
  "message": "Credenciales incorrectas"
}
```