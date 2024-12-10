# Challenge API

API REST para sistema de desafíos técnicos. Este proyecto permite a los usuarios autenticarse, obtener desafíos y validar sus respuestas, implementado con Laravel sin necesidad de base de datos.

## Requerimientos

- PHP >= 8.2
- Composer
- Laravel 10.x

## Instalación

1. Clonar el repositorio
```bash
git clone https://github.com/Xaviierkasvar/test-laravel-fullstack.git
cd test-laravel-fullstack
```

2. Instalar dependencias
```bash
composer install
```

3. Configurar el entorno
```bash
cp .env.example .env
php artisan key:generate
```

## Uso

### Ejecutar el servidor de desarrollo
```bash
php artisan serve
```
El servidor estará disponible en `http://localhost:8000`

### Generar documentación Swagger
```bash
php artisan l5-swagger:generate
```

### Ejecutar pruebas unitarias
```bash
php artisan test --testsuite=Unit
```

### Documentación de la API

La documentación de la API está disponible en:
```
http://localhost:8000/api/documentation
```

## Endpoints Principales

### Autenticación
- POST `/api/login` - Iniciar sesión y obtener token
  - Credenciales requeridas:
    ```json
    {
        "username": "queo_challenge",
        "password": "queoChallenge"
    }
    ```

### Desafíos
- GET `/api/challenge` - Obtener el desafío actual (requiere token)
- GET `/api/dumps/{dump_type}` - Obtener dumps (tipos disponibles: json, sql)
- POST `/api/validate` - Validar respuesta al desafío

## Seguridad y Rate Limiting

- Todos los endpoints (excepto login) requieren autenticación mediante Bearer token
- El endpoint de validación está limitado a:
  - 1 intento por minuto
  - Requiere autenticación válida

## Estructura de Respuestas

### Respuesta Exitosa de Login
```json
{
    "status": "success",
    "data": {
        "token": "token_generado",
        "message": "Authentication successful",
        "expires_in": 900
    }
}
```

### Respuesta de Error
```json
{
    "status": "error",
    "message": "Descripción del error",
    "code": 401
}
```

### Respuesta del Desafío
```json
{
    "status": "success",
    "data": {
        "challenge_id": 1,
        "description": "Analyze the group structure in the dump",
        "hint": "Use the dumps endpoint to get the necessary data"
    }
}
```

## Características Técnicas

- Implementado en PHP 8.2
- Sin dependencia de base de datos
- Autenticación basada en tokens
- Rate limiting implementado
- Documentación con Swagger/OpenAPI
- Manejo de errores estandarizado
- Tests unitarios implementados

## Demo

- [Enlace al repositorio](https://github.com/Xaviierkasvar/test-laravel-fullstack)

- [Url a VPS](http://93.189.94.162/)

- [Video explicativo YouTube](https://youtu.be/mCaS6jjt28Q)

## Autor

Francisco Javier Castillo Barrios - [Portafolio Online](http://200.234.237.73/)

## Licencia

Este proyecto es software propietario. Todos los derechos reservados.
