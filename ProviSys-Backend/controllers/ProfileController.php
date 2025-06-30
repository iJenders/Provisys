<?php

include_once 'models/UsersModel.php';
include_once 'models/CredentialsModel.php';

class ProfileController
{
    public static function update()
    {
        // Get user from global variable
        global $USER;
        if (!$USER) {
            Responses::json(['errors' => ['Usuario no autenticado']], 401);
        }
        $user = UsersModel::getUser($USER);

        // Get request data
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate input data
        Validator::ensureFields($data, [
            'names',
            'lastNames',
            'email',
            'phone',
            'address',
        ]);

        $names = $data['names'];
        $lastNames = $data['lastNames'];
        $email = $data['email'];
        $phone = $data['phone'];
        $secondaryPhone = $data['secondaryPhone'] ?? null;
        $address = $data['address'];

        $namesValidator = new Validator($names, 'nombres');
        $lastNamesValidator = new Validator($lastNames, 'apellidos');
        $emailValidator = new Validator($email, 'correo electrónico');
        $phoneValidator = new Validator($phone, 'teléfono');
        if ($secondaryPhone !== null) {
            $secondaryPhoneValidator = new Validator($secondaryPhone, 'teléfono secundario');
        }
        $addressValidator = new Validator($address, 'dirección');

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

        $errors = array_merge($namesValidator->getErrors(), $lastNamesValidator->getErrors(), $emailValidator->getErrors(), $phoneValidator->getErrors(), $addressValidator->getErrors());

        if (!empty($errors)) {
            Responses::json(['errors' => $errors], 400);
        }

        // Check if email is already in use
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

        // Update password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $password = $data['password'];
            $passwordValidator = new Validator($password, 'contraseña');
            $passwordValidator->required()->minLength(8)->maxLength(255);
            if (!empty($passwordValidator->getErrors())) {
                Responses::json(['errors' => $passwordValidator->getErrors()], 400);
            }
            $credentials = CredentialsModel::getCredential($USER);
            $credentials->updatePassword($password);
        }

        try {
            $user->update();
            Responses::json(['message' => 'Perfil actualizado correctamente']);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }
}
