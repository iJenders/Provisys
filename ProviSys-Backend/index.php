<?php
// FRONT CONTROLLER

// Siempre es bueno incluir esto al principio de tu script PHP que maneja solicitudes API
// para evitar cualquier salida accidental antes de las cabeceras.
ob_start(); 

// 1. Permite cualquier origen (*): Esto significa que cualquier dominio puede hacer solicitudes.
header('Access-Control-Allow-Origin: *');

// 2. Permite cualquier método HTTP (GET, POST, PUT, DELETE, OPTIONS, etc.):
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

// 3. Permite cualquier cabecera HTTP personalizada que el cliente pueda enviar:
// Esto es importante para cabeceras como Content-Type, Authorization, X-Requested-With, etc.
header('Access-Control-Allow-Headers: *');

// 4. (Opcional) Permite que el navegador cachee la respuesta de pre-vuelo (OPTIONS) por un tiempo.
// Esto puede mejorar un poco el rendimiento en desarrollo, ya que el navegador no necesitará
// hacer una solicitud OPTIONS para cada petición.
header('Access-Control-Max-Age: 86400'); // 86400 segundos = 24 horas

// 5. Manejo de solicitudes OPTIONS (pre-vuelo):
// Los navegadores modernos envían una solicitud OPTIONS antes de la solicitud real (PUT, DELETE,
// o POST con cabeceras/Content-Type no estándar) para preguntar al servidor si la solicitud
// real está permitida. Si el método de la solicitud es OPTIONS, solo necesitamos enviar las cabeceras CORS y salir.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Si estás usando PHP-FPM o Apache con mod_php, es crucial terminar el script aquí
    // para que no se procese más lógica y no se envíe ningún cuerpo de respuesta innecesario.
    exit(0); 
}

// Cargar los archivos necesarios para la aplicación
include_once 'routes.php';
include_once 'env.php';
include_once 'utils/Responses.php';
include_once 'utils/Validator.php';

// Automáticamente incluir todos los controladores de la carpeta "controllers"
// Esto es útil para evitar tener que incluir cada controlador manualmente
$controllers = new DirectoryIterator('controllers');
foreach ($controllers as $controller) {
    if ($controller->isFile() && $controller->getExtension() === 'php') {
        include_once 'controllers/' . $controller->getFilename();
    }
}

// Automáticamente incluir todos los middlewares de la carpeta "middlewares"
// Esto es útil para evitar tener que incluir cada middleware manualmente
$middlewares = new DirectoryIterator('middlewares');
foreach ($middlewares as $middleware) {
    if ($middleware->isFile() && $middleware->getExtension() === 'php') {
        include_once 'middlewares/' . $middleware->getFilename();
    }
}



// Configurar Rutas.

global $GET_ROUTES, $POST_ROUTES; // Obtener las rutas GET y POST definidas en routes.php

// Obtener la ruta y método de la solicitud entrante
$REQUEST_ROUTE = $_SERVER['REQUEST_URI'];
$REQUEST_ROUTE = explode('?', $REQUEST_ROUTE)[0]; // Elimina los parámetros de la URL
if ($REQUEST_ROUTE !== '/' && substr($REQUEST_ROUTE, -1) === '/') { // Elimina la barra al final de la ruta si la ruta no es la raíz
    $REQUEST_ROUTE = substr($REQUEST_ROUTE, 0, -1);
}

$REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];




// Ejecutar la acción correspondiente según el método de la solicitud
$ROUTES;

if ($REQUEST_METHOD === 'GET') {
    $ROUTES = $GET_ROUTES;
} else if ($REQUEST_METHOD === 'POST') {
    $ROUTES = $POST_ROUTES;
} else {
    $ROUTES = [];
}

if (isset($ROUTES[$REQUEST_ROUTE])) {
    $ROUTE = $ROUTES[$REQUEST_ROUTE];

    // Aquí se lleva a cabo la ejecución de los middlewares. Para más información,
    // ver el archivo routes.php
    if (!isset($ROUTE['middlewares'])) {
        $ROUTE['middlewares'] = [];
    }
    foreach ($ROUTE['middlewares'] as $middleware) {
        $middlewareName = $middleware[0]; // El nombre del middleware
        $params = isset($middleware[1]) ? $middleware[1] : []; // Los parámetros del middleware (opcional)

        if (class_exists($middlewareName)) {
            $middlewareName::handle($_REQUEST, $params); // Ejecuta el middleware, pasándole la solicitud y los parámetros
        } else {
            Responses::json(['errors' => ['Middleware ' . $middlewareName . ' no encontrado']], 500);
        }
    }

    // Luego de pasar por los middlewares, se ejecuta la acción asociada a la ruta
    if (!class_exists($ROUTE['action'][0])) {
        Responses::json(['errors' => ['Controlador ' . $ROUTE['action'][0] . ' no encontrado']], 500);
    }
    if (!method_exists($ROUTE['action'][0], $ROUTE['action'][1])) {
        Responses::json(['errors' => ['Método ' . $ROUTE['action'][1] . ' no encontrado en el controlador ' . $ROUTE['action'][0]]], 500);
    }

    call_user_func($ROUTE['action']);
} else {
    Responses::json(['error' => 'Ruta no encontrada: ' . $REQUEST_ROUTE], 404);
    exit;
}
?>