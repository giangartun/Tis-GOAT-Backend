# Buenas prácticas para nombres de commits

Estas prácticas ayudan a que el historial del proyecto sea **claro, ordenado y fácil de entender** para todo el equipo.

---

# 1. Estructura recomendada del commit

Una estructura muy usada es:

**También puede seguirse una estructura semántica simple: verbo + funcionalidad + efecto (opcional)**

Ejemplo:

```
feat: agregar formulario de registro
fix: corregir error en validación de login
docs: actualizar README de instalación
```

---

# 2. Tipos de commit (convención común)

| Tipo     | Uso                                                                |
| -------- | ------------------------------------------------------------------ |
| feat     | nueva funcionalidad                                                |
| fix      | corrección de errores                                              |
| docs     | cambios en documentación                                           |
| style    | cambios de formato (espacios, indentación, CSS sin afectar lógica) |
| refactor | mejorar código sin cambiar funcionalidad                           |
| test     | agregar o modificar pruebas                                        |
| chore    | tareas de mantenimiento o configuración                            |
| perf     | mejora de rendimiento                                              |
| build    | cambios en herramientas o dependencias                             |
| ci       | cambios en integración continua                                    |

Ejemplos:

```
feat: agregar sistema de autenticación
fix: corregir error al cargar productos
docs: agregar guía de instalación
refactor: simplificar lógica del controlador de usuarios
```

---

# 3. Reglas para escribir el mensaje

✔ usar **minúsculas**
✔ escribir en **modo imperativo**
✔ máximo **50–70 caracteres**
✔ describir **qué hace el cambio**

Ejemplo correcto:

```
feat: agregar validación de correo en registro
```

Ejemplo incorrecto:

```
agregué varias cosas
```

---

# 4. Cuándo agregar descripción (cuerpo del commit)

La descripción **no siempre es necesaria**, pero se recomienda cuando:

* el cambio es complejo
* modifica varias partes del sistema
* requiere explicación

Formato:

```
tipo: descripción corta

explicación más detallada del cambio
motivo del cambio
impacto en el sistema
```

Ejemplo:

```
feat: agregar autenticación con JWT

se implementa autenticación basada en tokens JWT
para permitir acceso seguro a la API desde el frontend
```

---

# 5. Cada cuánto hacer un commit

Un commit debe representar **una unidad lógica de cambio**.

Buenas prácticas:

✔ cuando terminas **una funcionalidad**
✔ cuando corriges **un error específico**
✔ cuando agregas **una mejora clara**

Evitar:

❌ commits gigantes con muchos cambios
❌ commits por cada línea modificada

Regla simple:

```
1 cambio lógico = 1 commit
```

---

# 6. Cantidad de archivos por commit

No existe un número fijo, pero la idea es:

* incluir **solo archivos relacionados al mismo cambio**
* evitar commits que mezclen muchas cosas diferentes

Ejemplo bueno:

```
feat: agregar formulario de login
```

Archivos incluidos:

```
Login.tsx
login.css
authController.php
```

Todo pertenece a **la misma funcionalidad**.

Ejemplo malo:

```
feat: login y cambio de estilos y arreglo de rutas
```

Ese commit debería dividirse.

---

# 7. Ejemplos de commits bien escritos

```
feat: agregar página de listado de inmuebles
fix: corregir error en carga de imágenes
refactor: reorganizar estructura de controladores
docs: actualizar instrucciones de instalación
style: mejorar estilos del navbar
chore: actualizar dependencias del proyecto
```

---

# 8. Buen flujo de trabajo

```
1. modificar código
2. revisar cambios
3. agregar archivos al staging
4. crear commit claro
```

Ejemplo:

```
git add .
git commit -m "feat: agregar sistema de registro de usuarios"
```

---

# 9. Recomendación final

Un buen commit debe permitir entender:

* **qué se cambió**
* **por qué se cambió**

sin necesidad de revisar todo el código.
