<?php

include_once 'models/UsersModel.php';

class UsersController
{
    public static function getAllClients()
    {
        // Obtener datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        $search = $data['search'] ?? '';
        
        $users = UsersModel::getAllClients(search: $search);

        Responses::json($users);
    }

    public static function editClient()
    {
        // Obtener los datos del usuario del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validación de los datos de entrada
        Validator::ensureFields($data, [
            'username',
            'names',
            'lastNames',
            'email',
            'phone',
            'address',
            'verified'
        ]);

        $username = $data['username'];
        $names = $data['names'];
        $lastNames = $data['lastNames'];
        $email = $data['email'];
        $phone = $data['phone'];
        $secondaryPhone = null;
        if (isset($data['secondaryPhone'])) {
            $secondaryPhone = $data['secondaryPhone'];
        }
        $address = $data['address'];
        $verified = $data['verified'];

        $usernameValidator = new Validator($username, 'nombre de usuario');
        $namesValidator = new Validator($names, 'nombres');
        $lastNamesValidator = new Validator($lastNames, 'apellidos');
        $emailValidator = new Validator($email, 'correo electrónico');
        $phoneValidator = new Validator($phone, 'teléfono');
        if ($secondaryPhone !== null) {
            $secondaryPhoneValidator = new Validator($secondaryPhone, 'teléfono secundario');
        }
        $addressValidator = new Validator($address, 'dirección');
        $verifiedValidator = new Validator($verified, 'estado');

        $namesValidator->required()->minLength(3)->maxLength(46)->alphaNumericWithSpaces()->maxSpaces(1);
        $lastNamesValidator->required()->minLength(3)->maxLength(46)->alphaNumericWithSpaces()->maxSpaces(1);
        $emailValidator->required()->email();
        $phoneValidator->phone();
        if ($secondaryPhone !== null) {
            $secondaryPhoneValidator->phone();
            if (!empty($secondaryPhoneValidator->getErrors())) {
                Responses::json(['errors' => $secondaryPhoneValidator->getErrors()], 400);
            }
        }
        $addressValidator->required()->minLength(3)->maxLength(255)->alphaNumericWithSecureSpecialChars();
        $verifiedValidator->required()->numeric()->minValue(0)->maxValue(1);

        $errors = array_merge($usernameValidator->getErrors(), $namesValidator->getErrors(), $lastNamesValidator->getErrors(), $emailValidator->getErrors(), $phoneValidator->getErrors(), $addressValidator->getErrors(), $verifiedValidator->getErrors());

        if (!empty($errors)) {
            Responses::json(['errors' => $errors], 400);
        }

        // Verificar si el usuario ya existe en la base de datos
        $existingUser = UsersModel::userExists($username);
        if (!$existingUser) {
            Responses::json(['errors' => ['El usuario "' . $username . '" no existe']], 400);
        }

        // Verificar si el correo electrónico ya está en uso
        $user = UsersModel::getUser($username);
        if ($user->getEmail() !== $email) {
            $existingEmail = UsersModel::emailExists($email);
            if ($existingEmail) {
                Responses::json(['errors' => ["El correo electrónico $email ya está en uso"]], 400);
            }
        }

        $user->setNames($names);
        $user->setLastNames($lastNames);
        $user->setEmail($email);
        $user->setPhone($phone);
        if ($secondaryPhone !== null) {
            $user->setSecondaryPhone($secondaryPhone);
        }
        $user->setAddress($address);
        $user->setVerified($verified);

        try {
            $user->update();
            Responses::json(['message' => 'Cliente actualizado correctamente']);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }
}

