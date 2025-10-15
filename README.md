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


## 🤝 Contribución

Este es un challenge de Adrian Sirianni.

