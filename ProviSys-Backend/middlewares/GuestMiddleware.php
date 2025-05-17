<?php


class GuestMiddleware
{
    public static function handle($request, $params)
    {
        // Aquí va la lógica de autenticación
        // Por ejemplo, verificar si el usuario tiene sesión iniciada o si el token JWT es válido.

        require_once 'utils/JWT.php';
        global $ENV;
        $jwt_secret = $ENV['JWT_SECRET'];

        $headers = getallheaders();

        // Verificar si el token JWT está presente en la cabecera de autorización
        if (isset($headers['Authorization'])) {
            Responses::json(['errors' => ['Clientes con sesión iniciada no pueden acceder a esta ruta']], 401);
        }
    }
}