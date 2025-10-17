# LatinAd Challenge - Backend

## 📋 Challenge

Este es el repositorio para el challenge de backend de LatinAd. El objetivo es implementar una API RESTful usando Laravel 12.

### Arquitectura del Challenge

El proyecto está configurado con:
- **Laravel 12** - Framework PHP principal
- **MySQL 8.0** - Base de datos
- **Redis** - Cache y sesiones
- **Nginx** - Servidor web
- **Docker** - Contenedores para desarrollo local

## 🚀 Inicio Rápido - Desarrollo Local

### Requisitos
- Docker y Docker Compose instalados
- Sistema Linux/Mac (o WSL en Windows)

### Ejecutar en Local

**Un solo comando para levantar todo:**

```bash
./start.sh
```

**Esto automáticamente:**
- ✅ Levanta todos los contenedores (Laravel, MySQL, Redis, Nginx)
- ✅ Crea el proyecto Laravel 12
- ✅ Configura la base de datos
- ✅ Instala dependencias
- ✅ Ejecuta migraciones
- ✅ **Pobla las tablas con datos de prueba** (2 usuarios + 10 displays)
- ✅ Optimiza la aplicación

### URLs de Acceso

Una vez ejecutado `./start.sh`, puedes acceder a:

- **API Laravel**: http://localhost:8080
- **phpMyAdmin (Base de Datos)**: http://localhost:8081
- **MailHog (Email Testing)**: http://localhost:8025
- **Base de Datos Directa**: localhost:3306
- **Redis**: localhost:6379


## 📚 API Documentation

### 🎯 Servicios Disponibles

La API maneja **Displays** (pantallas digitales) con operaciones CRUD completas.

**Base URL**: `http://localhost:8080/api`

### 🔄 **Flujo de Trabajo Completo**

**1. Iniciar sesión para obtener token:**
```bash
curl -X POST http://localhost:8080/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "tumail@gmail.com", "password": "1234"}'
```

**2. Guardar el token de la respuesta y usarlo en todas las operaciones:**
```bash
# Ejemplo: Listar displays
curl -H "Authorization: Bearer [tu_token_aqui]" \
  http://localhost:8080/api/displays
```

### 🔐 **Autenticación**

#### **Login**
```bash
POST /api/login
```

**Ejemplo:**
```bash
curl -X POST http://localhost:8080/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "tumail@gmail.com",
    "password": "1234"
  }'
```

**Respuesta Exitosa (200):**
```json
{
  "success": true,
  "message": "Login exitoso",
  "data": {
    "user": {
      "id": 1,
      "name": "Test User",
      "email": "tumail@gmail.com"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

**Error de Credenciales (401):**
```json
{
  "success": false,
  "message": "Credenciales inválidas"
}
```

**Campos requeridos:**
- `email` - Email del usuario
- `password` - Contraseña del usuario

**Uso del Token JWT:**
Para acceder a los endpoints protegidos, incluye el token en el header:
```bash
curl -H "Authorization: Bearer tu_token_aqui" http://localhost:8080/api/displays
```

---

### 🖥️ **Displays**

> **⚠️ IMPORTANTE:** Todas las rutas de Displays requieren autenticación JWT. Debes incluir el header `Authorization: Bearer [token]` obtenido del endpoint `/api/login`.

#### 1. **Listar Displays**
```bash
GET /api/displays
```

**Ejemplo:**
```bash
curl -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..." \
  http://localhost:8080/api/displays
```

**Filtros opcionales:**
- `?type=indoor` - Solo displays interiores
- `?type=outdoor` - Solo displays exteriores
- `?user_id=1` - Displays de un usuario específico

---

#### 2. **Obtener Display por ID**
```bash
GET /api/displays/{id}
```

**Ejemplo:**
```bash
curl -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..." \
  http://localhost:8080/api/displays/1
```

---

#### 3. **Crear Display**
```bash
POST /api/displays
```

**Ejemplo:**
```bash
curl -X POST http://localhost:8080/api/displays \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..." \
  -d '{
    "name": "Display Centro Comercial",
    "description": "Pantalla LED para shopping",
    "price_per_day": 250.00,
    "resolution_width": 1920,
    "resolution_height": 1080,
    "type": "indoor",
    "user_id": 1
  }'
```

**Campos requeridos:**
- `name` - Nombre del display
- `price_per_day` - Precio por día (número)
- `resolution_width` - Ancho en píxeles
- `resolution_height` - Alto en píxeles
- `type` - `"indoor"` o `"outdoor"`
- `user_id` - ID del usuario propietario

**Campos opcionales:**
- `description` - Descripción del display

---

#### 4. **Actualizar Display**
```bash
PUT /api/displays/{id}
```

**Ejemplo:**
```bash
curl -X PUT http://localhost:8080/api/displays/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..." \
  -d '{
    "name": "Display Actualizado",
    "price_per_day": 300.00
  }'
```

**Nota:** Todos los campos son opcionales en la actualización.

---

#### 5. **Eliminar Display**
```bash
DELETE /api/displays/{id}
```

**Ejemplo:**
```bash
curl -X DELETE http://localhost:8080/api/displays/1 \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
```

### 📊 Respuestas de la API

#### ✅ **Respuesta Exitosa (200/201)**
```json
{
  "success": true,
  "message": "Display created successfully",
  "data": {
    "id": 1,
    "name": "Display Centro Comercial",
    "description": "Pantalla LED para shopping",
    "price_per_day": 250,
    "formatted_price": "$250.00",
    "resolution": {
      "width": 1920,
      "height": 1080,
      "formatted": "1920x1080"
    },
    "type": "indoor",
    "type_label": "Interior",
    "user": {
      "id": 1,
      "name": "Test User",
      "email": "test@example.com"
    },
    "created_at": "2025-10-15 20:57:52",
    "updated_at": "2025-10-15 20:57:52"
  }
}
```

#### ❌ **Error de Validación (422)**
```json
{
  "message": "El nombre del display es obligatorio.",
  "errors": {
    "name": ["El nombre del display es obligatorio."],
    "price_per_day": ["El precio no puede ser negativo."]
  }
}
```

#### ❌ **No Encontrado (404)**
```json
{
  "success": false,
  "message": "Display not found"
}
```

#### ❌ **No Autenticado (401)**
```json
{
  "message": "Unauthenticated."
}
```

> **Nota:** Este error aparece cuando:
> - No incluyes el header `Authorization`
> - El token JWT ha expirado
> - El token JWT es inválido

### 🔧 Códigos de Estado HTTP

- **200** - OK (GET, PUT, DELETE exitosos)
- **201** - Created (POST exitoso)
- **401** - Unauthorized (token faltante o inválido)
- **404** - Not Found (recurso no existe)
- **422** - Unprocessable Entity (errores de validación)

## 🧪 Testing

### Ejecutar Tests

Para ejecutar los tests del sistema:

```bash
# Ejecutar todos los tests
docker exec -it latinad_app php artisan test

# Ejecutar tests específicos de ownership
docker exec -it latinad_app php artisan test tests/Feature/DisplayOwnershipTest.php
```

### Resultado Esperado de los Tests

Al ejecutar `php artisan test`, deberías ver algo similar a:

```
PASS  Tests\Feature\DisplayOwnershipTest
✓ user can only see their own displays in the listing
✓ user cannot see displays from another user  
✓ user cannot access specific display from another user
✓ user can access their own specific display
✓ user cannot update display from another user
✓ user can update their own display
✓ user cannot delete display from another user
✓ user can delete their own display
✓ new display is automatically assigned to authenticated user
✓ endpoints require authentication

Tests:  10 passed
Time:   0.45s
```

### ¿Qué Hacen los Tests?

Los tests validan que:

1. **🔒 Ownership Protection**: Un usuario solo puede ver, editar y eliminar sus propios displays
2. **🚫 Access Control**: No puede acceder a displays de otros usuarios (retorna 404)
3. **✅ Authentication**: Todos los endpoints requieren token JWT válido
4. **🔄 Auto-assignment**: Los nuevos displays se asignan automáticamente al usuario autenticado
5. **📊 Data Integrity**: Verifica que los datos se mantengan consistentes en la base de datos

### Datos de Prueba Creados

El seeder crea automáticamente:

- **2 usuarios de prueba:**
  - `test1@example.com` / `password123`
  - `test2@example.com` / `password123`
- **10 displays distribuidos** entre los usuarios
- **Datos visibles** para testing con Postman

## 🤝 Contribución
Adrian Sirianni.

