<?php
include_once 'models/UsersModel.php';

class PermissionMiddleware
{
    public static function handle($request, $permissions)
    {
        // La verificación de permisos se hace mediante la tabla "permisos" y "roles" en la base de datos.
        // El rol y permiso del usuario actual se obtienen a través del JWT.

        global $USER;
        $userPermissions = UsersModel::getUserPermissions($USER);

        foreach ($permissions as $permission) {
            if (in_array($permission, $userPermissions)) {
                return true;
            }
        }

        Responses::json(['errors' => ['No tiene los permisos para acceder a esta ruta']], 403);
    }
}