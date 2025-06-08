<?php

class AuthMiddleware
{
    public static function handle($request, $params)
    {
        // Aquí va la lógica de autenticación
        // Es decir, verificar si el usuario tiene sesión iniciada o si el token JWT es válido.

        require_once 'utils/JWT.php';
        global $ENV;
        $jwt_secret = $ENV['JWT_SECRET'];

        $headers = getallheaders();

        // Verificar si el token JWT está presente en la cabecera de autorización
        if (!isset($headers['Authorization'])) {
            Responses::json(['errors' => ['Header de autenticación inválido']], 401);
        }

        // Obtener el token JWT de la cabecera de autorización
        $token = $headers['Authorization'];
        $token = str_replace('Bearer ', '', $token); // Eliminar el prefijo "Bearer "

        // Validar el token JWT
        $decoded = JWT::validateToken($token, $jwt_secret);
        if (!$decoded) {
            Responses::json(['errors' => ['Token inválido o expirado']], 401);
        }

        // Si el token es válido, se guarda el nombre de usuario en una variable global
        global $USER;
        $USER = $decoded['sub']; // El 'sub' del payload es el username del usuario
    }
}

