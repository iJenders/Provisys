<?php
include_once 'models/UsersModel.php';
include_once 'responses/Responses.php';
include_once 'utils/Validator.php';

class EmployeeController
{
    public static function createEmployee()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        Validator::ensureFields($data, ['username', 'password', 'names', 'lastNames', 'email', 'phone', 'address', 'roleId']);

        $usernameValidator = new Validator($data['username'], 'nombre de usuario');
        $usernameValidator->required()->minLength(3)->maxLength(24)->alphanumeric();

        $passwordValidator = new Validator($data['password'], 'contraseña');
        $passwordValidator->required()->password();

        $namesValidator = new Validator($data['names'], 'nombres');
        $namesValidator->required()->minLength(3)->maxLength(46)->alphaNumericWithSpaces()->maxSpaces(1);

        $lastNamesValidator = new Validator($data['lastNames'], 'apellidos');
        $lastNamesValidator->required()->minLength(3)->maxLength(46)->alphaNumericWithSpaces()->maxSpaces(1);

        $emailValidator = new Validator($data['email'], 'correo electrónico');
        $emailValidator->required()->email();

        $phoneValidator = new Validator($data['phone'], 'teléfono');
        $phoneValidator->required()->phone();

        $addressValidator = new Validator($data['address'], 'dirección');
        $addressValidator->required()->minLength(3)->maxLength(255)->alphaNumericWithSecureSpecialChars();

        $roleIdValidator = new Validator($data['roleId'], 'rol');
        $roleIdValidator->required()->numeric();

        $errors = array_merge(
            $usernameValidator->getErrors(),
            $passwordValidator->getErrors(),
            $namesValidator->getErrors(),
            $lastNamesValidator->getErrors(),
            $emailValidator->getErrors(),
            $phoneValidator->getErrors(),
            $addressValidator->getErrors(),
            $roleIdValidator->getErrors()
        );

        if (UsersModel::userExists($data['username'])) {
            $errors[] = 'El nombre de usuario ya está en uso.';
        }
        if (UsersModel::emailExists($data['email'])) {
            $errors[] = 'El correo electrónico ya está en uso.';
        }

        if (!empty($errors)) {
            Responses::json(['errors' => $errors], 400);
            return;
        }

        $result = UsersModel::createUser(
            $data['username'],
            $data['password'],
            $data['names'],
            $data['lastNames'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $data['roleId'],
            $data['secondaryPhone'] ?? null,
            1
        );

        if ($result) {
            Responses::json(['message' => 'Empleado creado con éxito'], 201);
        } else {
            Responses::json(['message' => 'Error al crear el empleado'], 500);
        }
    }

    public static function updateEmployee()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        Validator::ensureFields($data, ['username', 'names', 'lastNames', 'email', 'phone', 'address', 'roleId']);

        $user = UsersModel::getUser($data['username']);
        if (!$user) {
            Responses::json(['message' => 'Empleado no encontrado'], 404);
            return;
        }

        $namesValidator = new Validator($data['names'], 'nombres');
        $namesValidator->required()->minLength(3)->maxLength(46)->alphaNumericWithSpaces()->maxSpaces(1);

        $lastNamesValidator = new Validator($data['lastNames'], 'apellidos');
        $lastNamesValidator->required()->minLength(3)->maxLength(46)->alphaNumericWithSpaces()->maxSpaces(1);

        $emailValidator = new Validator($data['email'], 'correo electrónico');
        $emailValidator->required()->email();

        $phoneValidator = new Validator($data['phone'], 'teléfono');
        $phoneValidator->required()->phone();

        $addressValidator = new Validator($data['address'], 'dirección');
        $addressValidator->required()->minLength(3)->maxLength(255)->alphaNumericWithSecureSpecialChars();

        $roleIdValidator = new Validator($data['roleId'], 'rol');
        $roleIdValidator->required()->numeric();

        $errors = array_merge(
            $namesValidator->getErrors(),
            $lastNamesValidator->getErrors(),
            $emailValidator->getErrors(),
            $phoneValidator->getErrors(),
            $addressValidator->getErrors(),
            $roleIdValidator->getErrors()
        );

        if ($user->getEmail() !== $data['email'] && UsersModel::emailExists($data['email'])) {
            $errors[] = 'El correo electrónico ya está en uso.';
        }

        if (!empty($errors)) {
            Responses::json(['errors' => $errors], 400);
            return;
        }

        $user->setNames($data['names']);
        $user->setLastNames($data['lastNames']);
        $user->setEmail($data['email']);
        $user->setPhone($data['phone']);
        $user->setSecondaryPhone($data['secondaryPhone'] ?? null);
        $user->setAddress($data['address']);
        $user->setRoleId($data['roleId']);
        $user->setVerified($data['status'] ?? $user->getVerified());

        if ($user->getUsername() == 'admin' && $user->getRoleId() != 1) {
            Responses::json(['errors' => ['No se puede modificar el rol del usuario admin']], 400);
            return;
        }

        try {
            $user->update();
            Responses::json(['message' => 'Empleado actualizado con éxito']);
        } catch (Exception $e) {
            Responses::json(['message' => 'Error al actualizar el empleado: ' . $e->getMessage()], 500);
        }
    }

    public static function deleteEmployee()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'];

        try {
            $result = UsersModel::deleteUser($username);
            if ($result) {
                Responses::json(['message' => 'Empleado eliminado con éxito']);
            } else {
                Responses::json(['message' => 'Empleado no encontrado'], 404);
            }
        } catch (Exception $e) {
            Responses::json(['message' => 'Error al eliminar el empleado: ' . $e->getMessage()], 500);
        }
    }

    public static function getAllEmployees()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $search = $data['search'] ?? '';
        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        $result = UsersModel::getAllEmployees($offset, $search, $limit);

        Responses::json([
            'employees' => $result['users'],
            'count' => $result['count']
        ]);
    }
}
