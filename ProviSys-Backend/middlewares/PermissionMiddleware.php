<?php

class PermissionMiddleware
{
    public static function handle($request, $params)
    {
        // Aquí va la lógica de autorización
        // Por ejemplo, verificar si el usuario tiene permisos para acceder a la ruta.

        // La verificación de permisos se hace mediante la tabla "permisos" y "roles" en la base de datos.
        // El rol y permiso del usuario actual se obtienen a través del JWT.

        echo Responses::json([
            'message' => [
                'permisos requeridos' => $params,
            ]
        ], 200);

        exit;
    }
}