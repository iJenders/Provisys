# ProviSys

Este proyecto es un backend desarrollado en PHP que implementa un sistema modular para la gestión de envíos e inventario general.
Funciona mediante una API RESTful y utiliza una base de datos MySQL para almacenar la información.

Se utiliza una arquitectura de capas MVC (Modelo-Vista-Controlador) para organizar la lógica de la aplicación. Adicionalmente, se ha implementado un sistema de autenticación basado en tokens JWT (JSON Web Tokens) para garantizar la seguridad de las rutas y la gestión de permisos.

---

## Tabla de Contenidos

- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Rutas](#rutas)
- [Middlewares](#middlewares)
- [Utilidades](#utilidades)
- [Controladores](#controladores)
- [Variables de Entorno](#variables-de-entorno)

---

## Requisitos

- PHP 7.0 o superior.
- Servidor Apache con soporte para `.htaccess`.
- Composer (opcional, si se desea usar dependencias externas).
- Mysql 8.0 o superior. Para desarrollo, recomiendo usar Laragon, pues XAMPP no soporta la version 8.0.

---

## Instalación

1. Clona este repositorio en tu máquina local:

   ```bash
   git clone https://github.com/iJenders/Provisys/tree/dev-abarca/ProviSys-Backend
   cd Provisys/ProviSys-Backend
   ```

2. Configura tus variables de entorno en el archivo env.php.

3. Asegúrate de que el servidor Apache esté configurado para usar el archivo .htaccess, de lo contrario no funcionará el routing.

4. Inicia el servidor:

   1. Si usas php puro.

   ```powershell
   php -S localhost:8000
   ```

   2. En caso de usar un WAMP o LAMP, asegúrate de que la carpeta del proyecto esté en la ruta raíz de tu WAMP/LAMP (**htdocs** en XAMPP y **www** en Laragon).

---

## Estructura del Proyecto

```md
ProviSys-backend/
├── controllers/
│ ├── [AuthController.php]
│ ├── [HomeController.php]
│ └── [ProductsController.php]
├── middlewares/
│ ├── [AuthMiddleware.php]
│ ├── [GuestMiddleware.php]
│ ├── [JsonMiddleware.php]
│ └── [PermissionMiddleware.php]
├── models/
│ ├── [CredentialsModel.php]
│ └── [UsersModel.php]
├── utils/
│ ├── [DBConnection.php]
│ ├── [JWT.php]
│ ├── [Responses.php]
│ └── [Validator.php]
├── [.htaccess]
├── [env.php]
├── [index.php]
├── [README.md]
└── [routes.php]
```

---

## Rutas

Las rutas están definidas en el archivo routes.php y se dividen en métodos GET y POST.

### Rutas GET

- `/`: Ruta principal que muestra la página de inicio. Se encuentra en desarrollo experimental para futura implementación con el frontend.

### Rutas POST

- `/login`

  - **Acción**: AuthController::login
  - **Middlewares**:
    - JsonMiddleware
    - GuestMiddleware
  - **Descripción**: Maneja el inicio de sesión.

- `/register`

  - **Acción**: AuthController::register
  - **Middlewares**:
    - JsonMiddleware
    - GuestMiddleware
  - **Descripción**: Registra un nuevo usuario.

- `/auth/user`

  - **Acción**: AuthController::user
  - **Middlewares**:
    - JsonMiddleware
    - AuthMiddleware
  - **Descripción**: Responde con la información del usuario autenticado.

---

## Middlewares

Los middlewares procesan las solicitudes antes de que lleguen a los controladores. Están definidos en la carpeta middlewares.

- `AuthMiddleware()`: Verifica si el usuario está autenticado mediante un token JWT, para proteger rutas.
- `GuestMiddleware()`: Restringe el acceso a rutas para que solo usuarios no autenticados puedan acceder.
- `JsonMiddleware()`: Valida que el cuerpo de la solicitud sea JSON válido.
  PermissionMiddleware: Verifica los permisos del usuario para acceder a una ruta.
- `PermissionMiddleware($args)`: Verifica si el usuario tiene los permisos necesarios para acceder a una ruta.

---

## Utilidades

### JWT (JSON Web Token)

Una clase para generar y validar tokens JWT.

- `generateToken($payload, $secret)`: Genera un token JWT.
- `validateToken($token, $secret)`: Valida un token JWT.

### Responses

Una clase para manejar distintos tipos de respuestas. Todos los métodos corresponden a un tipo de respuesta, mismas que finalizan la ejecución y responden con un código de estado HTTP.

- `json($data, $status)`: Envía una respuesta JSON.
- `redirect($url, $status)`: Redirige a una URL.

### Validator

Una clase para validar datos de entrada. Proporciona métodos para validar campos específicos, como email, contraseña, etc.

---

## Controladores

### AuthController

- `login($request)`: Maneja el inicio de sesión. <code style="color: darkorange;">(Solamente genera tokens)</code>
- `register($request)`: Registra un nuevo usuario. <code style="color: red;">(Sin implementar)</code>

### HomeController

- `index($request)`: Muestra la página de inicio.

### ProductsController

- `create($request)`: Crea un nuevo producto. <code style="color: red;">(Sin implementar)</code>
- `update($request)`: Actualiza un producto existente. <code style="color: red;">(Sin implementar)</code>
- `delete($request)`: Elimina un producto.

---

## Variables de Entorno

Las variables de entorno se definen en el archivo `env.php`. Asegúrate de configurar las variables necesarias para la conexión a la base de datos y otros ajustes.

```php
<?php
$ENV = [
    'APP_NAME' => 'MyApp',
    'APP_ENV' => 'local',
    'APP_DEBUG' => true,
    'JWT_SECRET' => 'clave-secreta-para-jwt',
];
```

---
