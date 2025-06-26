<?php
include_once 'models/RolesModel.php';

class RolesController
{
    public static function createRole()
    {
        // Obtener los datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validación de los datos de entrada
        Validator::ensureFields($data, [
            'name',
            'description'
        ]);

        $name = $data['name'];
        $description = $data['description'];

        $nameValidator = new Validator($name, 'name');
        $descriptionValidator = new Validator($description, 'description');

        $nameValidator->required()->minLength(3)->maxLength(32)->alphaNumericWithSpaces();
        $descriptionValidator->required()->minLength(3)->maxLength(255)->alphaNumericWithSecureSpecialChars();

        $errors = array_merge($nameValidator->getErrors(), $descriptionValidator->getErrors());

        if (!empty($errors)) {
            Responses::json(['errors' => $errors], 400);
        }

        // Crear el rol en la base de datos
        try {
            RolesModel::create($name, $description);
        } catch (Exception $e) {
            Responses::json(['errors' => 'Error al crear el rol.'], 500);
        }

        // Mensaje de éxito
        Responses::json(['message' => 'Rol creado exitosamente'], 201);
    }

    public static function getRoles()
    {
        // Validación de los datos de entrada
        $data = json_decode(file_get_contents('php://input'), true);

        $search = null;
        $page = 1;
        $onlyDisabled = false;

        if (isset($data['search'])) {
            $search = $data['search'];
            $searchValidator = new Validator($search, 'search');
            $searchValidator->maxLength(255);

            $errors = $searchValidator->getErrors();
            if (!empty($errors)) {
                Responses::json(['errors' => $errors], 400);
            }
        }

        if (isset($data['page'])) {
            $page = $data['page'];
        }

        if (isset($data['onlyDisabled'])) {
            $onlyDisabled = $data['onlyDisabled'] == true;
        }

        $pageValidator = new Validator($page, 'page');
        $pageValidator->required()->numeric()->minValue(1);

        $errors = $pageValidator->getErrors();
        if (!empty($errors)) {
            Responses::json(['errors' => $errors], 400);
        }

        // Obtener todos los roles de la base de datos
        $roles = null;
        $count = null;
        try {
            if ($search) {
                $roles = RolesModel::getAll($page, $onlyDisabled, $search);
            } else {
                $roles = RolesModel::getAll($page, $onlyDisabled);
            }

            $count = RolesModel::getCount($search, $onlyDisabled);
        } catch (Exception $e) {
            Responses::json([
                'errors' => 'Error al obtener los roles.' . $e->getMessage()
            ], 500);
        }

        $rolesArray = [];
        foreach ($roles as $role) {
            $rolesArray[] = [
                'id' => $role->getId(),
                'name' => $role->getName(),
                'description' => $role->getDescription(),
                'disabled' => $role->getDisabled()
            ];
        }

        // Devolver los roles en formato JSON
        Responses::json([
            'roles' => $rolesArray,
            'count' => $count
        ], 200);
    }

    public static function getRole()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        Validator::ensureFields($data, ['id']);

        $id = $data['id'];

        $idValidator = new Validator($id, 'id');
        $idValidator->required()->numeric();

        $errors = $idValidator->getErrors();

        if (!empty($errors)) {
            Responses::json(['errors' => $errors], 400);
        }

        try {
            if (!RolesModel::roleExists($id)) {
                Responses::json(['errors' => ['El rol no existe.']], 404);
            }
        } catch (Exception $e) {
            Responses::json(['errors' => ['Error al verificar la existencia del rol.']], 500);
        }

        try {
            $role = RolesModel::getById($id);
        } catch (Exception $e) {
            Responses::json(['errors' => ['Error al obtener el rol.']], 500);
        }

        Responses::json(['role' => $role], 200);
    }

    public static function editRole()
    {
        // Obtener los datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validación de los datos de entrada
        Validator::ensureFields($data, [
            'id',
            'name',
            'description'
        ]);

        $id = $data['id'];
        $name = $data['name'];
        $description = $data['description'];

        $idValidator = new Validator($id, 'id');
        $nameValidator = new Validator($name, 'name');
        $descriptionValidator = new Validator($description, 'description');

        $idValidator->required()->numeric();
        $nameValidator->required()->minLength(3)->maxLength(32)->alphaNumericWithSpaces();
        $descriptionValidator->required()->minLength(3)->maxLength(255)->alphaNumericWithSecureSpecialChars();

        if (isset($data['disabled'])) {
            $disabled = $data['disabled'];
            $disabledValidator = new Validator($disabled, 'disabled');
            $disabledValidator->required()->isTinyInt();
            $errors = array_merge($idValidator->getErrors(), $nameValidator->getErrors(), $descriptionValidator->getErrors(), $disabledValidator->getErrors());
        } else {
            $errors = array_merge($idValidator->getErrors(), $nameValidator->getErrors(), $descriptionValidator->getErrors());
        }

        if (!empty($errors)) {
            Responses::json(['errors' => $errors], 400);
        }

        try {
            if (!RolesModel::roleExists($id)) {
                Responses::json(['errors' => ['El rol no existe.']], 404);
            }
        } catch (Exception $e) {
            Responses::json(['errors' => ['Error al verificar la existencia del rol.']], 500);
        }

        // Actualizar el rol en la base de datos
        try {
            if (isset($data['disabled'])) {
                RolesModel::edit($id, $name, $description, $disabled);
            } else {
                RolesModel::edit($id, $name, $description);
            }
        } catch (Exception $e) {
            Responses::json(['errors' => ['Error al editar el rol.']], 500);
        }

        // Mensaje de éxito
        Responses::json(['message' => 'Rol editado exitosamente'], 200);
    }

    public static function deleteRole()
    {
        // Obtener los datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validación de los datos de entrada
        Validator::ensureFields($data, [
            'id'
        ]);

        $id = $data['id'];
        $idValidator = new Validator($id, 'id');
        $idValidator->required()->numeric();

        $errors = $idValidator->getErrors();

        if (!empty($errors)) {
            Responses::json(['errors' => $errors], 400);
        }

        try {
            if (!RolesModel::roleExists($id)) {
                Responses::json(['errors' => ['El rol no existe.']], 404);
            }
        } catch (Exception $e) {
            Responses::json(['errors' => ['Error al verificar la existencia del rol.']], 500);
        }

        // Eliminar el rol de la base de datos
        try {
            RolesModel::delete($id);
        } catch (Exception $e) {
            Responses::json(['errors' => ['Error al eliminar el rol.']], 500);
        }

        // Mensaje de éxito
        Responses::json(['message' => 'Rol eliminado exitosamente'], 200);
    }

    public static function getAllActive()
    {
        try {
            $roles = RolesModel::getAllActive();
        } catch (Exception $e) {
            Responses::json(['errors' => ['Error al obtener los roles.']], 500);
        }

        $rolesArray = [];
        foreach ($roles as $role) {
            $rolesArray[] = [
                'id' => $role->getId(),
                'name' => $role->getName(),
                'description' => $role->getDescription(),
                'disabled' => $role->getDisabled()
            ];
        }

        Responses::json(['roles' => $rolesArray], 200);
    }

    public static function getRolePermissions()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        Validator::ensureFields($data, ['id']);

        $id = $data['id'];
        $idValidator = new Validator($id, 'id');
        $idValidator->required()->numeric();

        if (!empty($idValidator->getErrors())) {
            Responses::json(['errors' => $idValidator->getErrors()], 400);
            return;
        }

        try {
            if (!RolesModel::roleExists($id)) {
                Responses::json(['errors' => ['El rol no existe.']], 404);
                return;
            }
            $permissions = RolesModel::getPermissions($id);
            Responses::json(['permissions' => $permissions], 200);
        } catch (Exception $e) {
            Responses::json(['errors' => $e->getMessage()], 500);
        }
    }

    public static function updateRolePermissions()
    {
        // Obtener los datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);
        Validator::ensureFields($data, ['id', 'permissions']);

        $id = $data['id'];
        $permissions = $data['permissions'];
$permissionsValidator = new Validator($id, 'id');
        // Validar los datos de entrada
        $idValidator = new Validator($id, 'id');
        $idValidator->required()->numeric();

        if (!empty($idValidator->getErrors())) {
            Responses::json(['errors' => $idValidator->getErrors()], 400);
            return;
        }

        $permissionsValidator = new Validator($permissions, 'permissions');
        $permissionsValidator->isArray();

        if (!empty($permissionsValidator->getErrors())) {
            Responses::json(['errors' => $permissionsValidator->getErrors()], 400);
            return;
        }

        // Validar que el rol no sea administrador ni cliente
        if ($id == 1 || $id == 2) {
            Responses::json(['errors' => ['No se puede actualizar los permisos de este rol.']], 400);
            return;
        }

        // Actualizar los permisos del rol
        try {
            if (!RolesModel::roleExists($id)) {
                Responses::json(['errors' => ['El rol no existe.']], 404);
                return;
            }
            RolesModel::updatePermissions($id, $permissions);
            Responses::json(['response' => 'Permisos actualizados correctamente'], 200);
        } catch (Exception $e) {
            Responses::json(['errors' => $e->getMessage()], 500);
        }
    }
}
