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
- ✅ Optimiza la aplicación

### URLs de Acceso

Una vez ejecutado `./start.sh`, puedes acceder a:

- **API Laravel**: http://localhost:8080
- **phpMyAdmin (Base de Datos)**: http://localhost:8081
- **MailHog (Email Testing)**: http://localhost:8025
- **Base de Datos Directa**: localhost:3306
- **Redis**: localhost:6379


## 📚 API Documentation - Displays CRUD

### 🏗️ Arquitectura de Capas
- **Request**: Validación unificada en `DisplayRequest`
- **Controller**: Lógica de negocio limpia y desacoplada  
- **Resource**: Formateo de datos de respuesta en `DisplayResource`

<style>
.api-section {
  margin: 20px 0;
  border: 1px solid #e1e5e9;
  border-radius: 8px;
  overflow: hidden;
}

.api-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 15px 20px;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: bold;
  transition: all 0.3s ease;
}

.api-header:hover {
  background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
}

.api-header .method {
  background: rgba(255,255,255,0.2);
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: bold;
  text-transform: uppercase;
}

.api-header .method.get { background: #28a745; }
.api-header .method.post { background: #007bff; }
.api-header .method.put { background: #ffc107; color: #000; }
.api-header .method.delete { background: #dc3545; }

.api-content {
  padding: 0;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s ease;
}

.api-content.active {
  max-height: 2000px;
}

.api-content-inner {
  padding: 20px;
}

.curl-box {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 6px;
  padding: 15px;
  margin: 15px 0;
  font-family: 'Courier New', monospace;
  font-size: 14px;
  overflow-x: auto;
}

.schema-box {
  background: #f8f9fa;
  border-left: 4px solid #007bff;
  padding: 15px;
  margin: 15px 0;
  border-radius: 0 6px 6px 0;
}

.schema-title {
  font-weight: bold;
  color: #007bff;
  margin-bottom: 10px;
}

.response-box {
  background: #f8f9fa;
  border-left: 4px solid #28a745;
  padding: 15px;
  margin: 15px 0;
  border-radius: 0 6px 6px 0;
  font-family: 'Courier New', monospace;
  font-size: 14px;
  overflow-x: auto;
}

.response-title {
  font-weight: bold;
  color: #28a745;
  margin-bottom: 10px;
}

.arrow {
  transition: transform 0.3s ease;
}

.arrow.rotated {
  transform: rotate(180deg);
}

.status-code {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: bold;
  margin-left: 10px;
}

.status-200 { background: #d4edda; color: #155724; }
.status-201 { background: #d1ecf1; color: #0c5460; }
.status-404 { background: #f8d7da; color: #721c24; }
.status-422 { background: #fff3cd; color: #856404; }
</style>

<script>
function toggleApiSection(element) {
  const content = element.nextElementSibling;
  const arrow = element.querySelector('.arrow');
  
  if (content.classList.contains('active')) {
    content.classList.remove('active');
    arrow.classList.remove('rotated');
  } else {
    content.classList.add('active');
    arrow.classList.add('rotated');
  }
}
</script>

### 📋 Endpoints Disponibles

<div class="api-section">
  <div class="api-header" onclick="toggleApiSection(this)">
    <div>
      <span class="method get">GET</span>
      <span>/api/displays</span>
      <span class="status-code status-200">200</span>
    </div>
    <div class="arrow">▼</div>
  </div>
  <div class="api-content">
    <div class="api-content-inner">
      <h4>Listar todos los displays</h4>
      
      <div class="curl-box">
curl -X GET http://localhost:8080/api/displays \
  -H "Accept: application/json"
      </div>

      <div class="schema-box">
        <div class="schema-title">📥 Parámetros de Query (Opcionales)</div>
        <ul>
          <li><code>type</code> - Filtrar por tipo: <code>indoor</code> o <code>outdoor</code></li>
          <li><code>user_id</code> - Filtrar por ID de usuario</li>
          <li><code>page</code> - Número de página (paginación)</li>
        </ul>
      </div>

      <div class="response-box">
        <div class="response-title">✅ Respuesta Exitosa</div>
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Display Digital Centro",
      "description": "Display digital ubicado en el centro",
      "price_per_day": 150.5,
      "formatted_price": "$150.50",
      "resolution": {
        "width": 1920,
        "height": 1080,
        "formatted": "1920x1080"
      },
      "type": "outdoor",
      "type_label": "Exterior",
      "user": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com"
      },
      "created_at": "2025-10-15 20:57:52",
      "updated_at": "2025-10-15 20:57:52"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
      </div>
    </div>
  </div>
</div>

<div class="api-section">
  <div class="api-header" onclick="toggleApiSection(this)">
    <div>
      <span class="method get">GET</span>
      <span>/api/displays/{id}</span>
      <span class="status-code status-200">200</span>
    </div>
    <div class="arrow">▼</div>
  </div>
  <div class="api-content">
    <div class="api-content-inner">
      <h4>Obtener un display específico</h4>
      
      <div class="curl-box">
curl -X GET http://localhost:8080/api/displays/1 \
  -H "Accept: application/json"
      </div>

      <div class="response-box">
        <div class="response-title">✅ Respuesta Exitosa</div>
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Display Digital Centro",
    "description": "Display digital ubicado en el centro",
    "price_per_day": 150.5,
    "formatted_price": "$150.50",
    "resolution": {
      "width": 1920,
      "height": 1080,
      "formatted": "1920x1080"
    },
    "type": "outdoor",
    "type_label": "Exterior",
    "user": {
      "id": 1,
      "name": "Test User",
      "email": "test@example.com"
    },
    "created_at": "2025-10-15 20:57:52",
    "updated_at": "2025-10-15 20:57:52"
  }
}
      </div>

      <div class="response-box">
        <div class="response-title">❌ Error 404</div>
{
  "success": false,
  "message": "Display not found"
}
      </div>
    </div>
  </div>
</div>

<div class="api-section">
  <div class="api-header" onclick="toggleApiSection(this)">
    <div>
      <span class="method post">POST</span>
      <span>/api/displays</span>
      <span class="status-code status-201">201</span>
    </div>
    <div class="arrow">▼</div>
  </div>
  <div class="api-content">
    <div class="api-content-inner">
      <h4>Crear un nuevo display</h4>
      
      <div class="curl-box">
curl -X POST http://localhost:8080/api/displays \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Display Indoor Shopping",
    "description": "Display para centro comercial",
    "price_per_day": 200.00,
    "resolution_height": 1440,
    "resolution_width": 2560,
    "type": "indoor",
    "user_id": 1
  }'
      </div>

      <div class="schema-box">
        <div class="schema-title">📥 Datos de Entrada (JSON)</div>
        <ul>
          <li><code>name</code> <strong>(required)</strong> - Nombre del display (string, max: 255)</li>
          <li><code>description</code> <strong>(optional)</strong> - Descripción del display (string)</li>
          <li><code>price_per_day</code> <strong>(required)</strong> - Precio por día (numeric, min: 0)</li>
          <li><code>resolution_height</code> <strong>(required)</strong> - Altura en píxeles (integer, min: 1)</li>
          <li><code>resolution_width</code> <strong>(required)</strong> - Ancho en píxeles (integer, min: 1)</li>
          <li><code>type</code> <strong>(required)</strong> - Tipo: <code>"indoor"</code> o <code>"outdoor"</code></li>
          <li><code>user_id</code> <strong>(required)</strong> - ID del usuario propietario (exists: users, id)</li>
        </ul>
      </div>

      <div class="response-box">
        <div class="response-title">✅ Respuesta Exitosa (201)</div>
{
  "success": true,
  "message": "Display created successfully",
  "data": {
    "id": 2,
    "name": "Display Indoor Shopping",
    "description": "Display para centro comercial",
    "price_per_day": 200,
    "formatted_price": "$200.00",
    "resolution": {
      "width": 2560,
      "height": 1440,
      "formatted": "2560x1440"
    },
    "type": "indoor",
    "type_label": "Interior",
    "user": {
      "id": 1,
      "name": "Test User",
      "email": "test@example.com"
    },
    "created_at": "2025-10-15 21:15:30",
    "updated_at": "2025-10-15 21:15:30"
  }
}
      </div>

      <div class="response-box">
        <div class="response-title">❌ Error de Validación (422)</div>
{
  "message": "El nombre del display es obligatorio. (and 3 more errors)",
  "errors": {
    "name": ["El nombre del display es obligatorio."],
    "price_per_day": ["El precio no puede ser negativo."],
    "type": ["El tipo debe ser \"indoor\" o \"outdoor\"."],
    "user_id": ["El usuario especificado no existe."]
  }
}
      </div>
    </div>
  </div>
</div>

<div class="api-section">
  <div class="api-header" onclick="toggleApiSection(this)">
    <div>
      <span class="method put">PUT</span>
      <span>/api/displays/{id}</span>
      <span class="status-code status-200">200</span>
    </div>
    <div class="arrow">▼</div>
  </div>
  <div class="api-content">
    <div class="api-content-inner">
      <h4>Actualizar un display</h4>
      
      <div class="curl-box">
curl -X PUT http://localhost:8080/api/displays/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Display Actualizado",
    "price_per_day": 175.50
  }'
      </div>

      <div class="schema-box">
        <div class="schema-title">📥 Datos de Entrada (JSON) - Todos opcionales</div>
        <ul>
          <li><code>name</code> <strong>(optional)</strong> - Nombre del display (string, max: 255)</li>
          <li><code>description</code> <strong>(optional)</strong> - Descripción del display (string)</li>
          <li><code>price_per_day</code> <strong>(optional)</strong> - Precio por día (numeric, min: 0)</li>
          <li><code>resolution_height</code> <strong>(optional)</strong> - Altura en píxeles (integer, min: 1)</li>
          <li><code>resolution_width</code> <strong>(optional)</strong> - Ancho en píxeles (integer, min: 1)</li>
          <li><code>type</code> <strong>(optional)</strong> - Tipo: <code>"indoor"</code> o <code>"outdoor"</code></li>
          <li><code>user_id</code> <strong>(optional)</strong> - ID del usuario propietario (exists: users, id)</li>
        </ul>
      </div>

      <div class="response-box">
        <div class="response-title">✅ Respuesta Exitosa (200)</div>
{
  "success": true,
  "message": "Display updated successfully",
  "data": {
    "id": 1,
    "name": "Display Actualizado",
    "description": "Display digital ubicado en el centro",
    "price_per_day": 175.5,
    "formatted_price": "$175.50",
    "resolution": {
      "width": 1920,
      "height": 1080,
      "formatted": "1920x1080"
    },
    "type": "outdoor",
    "type_label": "Exterior",
    "user": {
      "id": 1,
      "name": "Test User",
      "email": "test@example.com"
    },
    "created_at": "2025-10-15 20:57:52",
    "updated_at": "2025-10-15 21:20:15"
  }
}
      </div>
    </div>
  </div>
</div>

<div class="api-section">
  <div class="api-header" onclick="toggleApiSection(this)">
    <div>
      <span class="method delete">DELETE</span>
      <span>/api/displays/{id}</span>
      <span class="status-code status-200">200</span>
    </div>
    <div class="arrow">▼</div>
  </div>
  <div class="api-content">
    <div class="api-content-inner">
      <h4>Eliminar un display</h4>
      
      <div class="curl-box">
curl -X DELETE http://localhost:8080/api/displays/1 \
  -H "Accept: application/json"
      </div>

      <div class="response-box">
        <div class="response-title">✅ Respuesta Exitosa (200)</div>
{
  "success": true,
  "message": "Display deleted successfully"
}
      </div>

      <div class="response-box">
        <div class="response-title">❌ Error 404</div>
{
  "success": false,
  "message": "Display not found"
}
      </div>
    </div>
  </div>
</div>

## 🤝 Contribución

Este es un challenge de Adrian Sirianni.

