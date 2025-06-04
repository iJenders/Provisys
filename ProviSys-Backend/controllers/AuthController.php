<?php
include_once 'models/UsersModel.php';
class AuthController
{
    // Inicia Sesión y retorna un token JWT
    public static function login()
    {
        // Obtener los datos de inicio de sesión del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);


        // Validación de los datos de entrada

        if (!isset($data['username']) || !isset($data['password'])) {
            Responses::json(['errors' => ['Falta alguno de los campos: username o password']], 400);
        }

        $username = $data['username'];
        $password = $data['password'];

        $usernameValidator = new Validator($username, 'usuario');
        $passwordValidator = new Validator($password, 'contraseña');

        $usernameValidator->required()->minLength(3)->maxLength(255)->alphaNumeric();
        $passwordValidator->required()->minLength(8)->maxLength(64);

        $errors = array_merge($usernameValidator->getErrors(), $passwordValidator->getErrors());

        if (!empty($errors)) {
            Responses::json(['errors' => $errors], 400);
        }

        // Verificar si el usuario existe en la base de datos
        $user = UsersModel::getUser($username);

        if (!$user) {
            Responses::json(['errors' => ['El usuario "' . $username . '" no existe']], 401);
        }

        // Verificar si la contraseña es correcta
        if (!$user->getCredentials()->verifyPassword($password)) {
            Responses::json(['errors' => ['Usuario o contraseña incorrectos']], 401);
        }

        // Generar el token JWT
        require_once 'utils/JWT.php';

        global $ENV;
        $jwt_secret = $ENV['JWT_SECRET'];


        $payload = [
            'sub' => $user->getUsername(), // Username del usuario
            'iat' => time(), // Tiempo de emisión
            'exp' => time() + 3600, // Tiempo de expiración (2 horas)
        ];
        $token = JWT::generateToken($payload, $jwt_secret);

        $token = 'Bearer ' . $token; // Agregar el prefijo "Bearer " al token

        // Devolver el token en la respuesta
        Responses::json([
            'message' => "Inicio de sesión exitoso",
            'token' => $token
        ]);
    }

    // Registra un usuario y retorna un mensaje de éxito
    public static function register()
    {
        // Obtener los datos del usuario del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validación de los datos de entrada
        Validator::ensureFields($data, [
            'names',
            'lastNames',
            'email',
            'phone',
            'address',
            'username',
            'password',
            'passwordConfirmation',
        ]);

        $names = $data['names'];
        $lastNames = $data['lastNames'];
        $email = $data['email'];
        $phone = $data['phone'];
        $secondaryPhone = null;
        if (isset($data['secondaryPhone'])) {
            $secondaryPhone = $data['secondaryPhone'];
        }
        $address = $data['address'];
        $username = $data['username'];
        $password = $data['password'];
        $passwordConfirmation = $data['passwordConfirmation'];

        $namesValidator = new Validator($names, 'nombres');
        $lastNamesValidator = new Validator($lastNames, 'apellidos');
        $emailValidator = new Validator($email, 'correo electrónico');
        $phoneValidator = new Validator($phone, 'teléfono');
        if ($secondaryPhone !== null) {
            $secondaryPhoneValidator = new Validator($secondaryPhone, 'teléfono secundario');
        }
        $addressValidator = new Validator($address, 'dirección');
        $usernameValidator = new Validator($username, 'usuario');
        $passwordValidator = new Validator($password, 'contraseña');

        $namesValidator->required()->minLength(3)->maxLength(46)->alphaNumericWithSpaces()->maxSpaces(1);
        $lastNamesValidator->required()->minLength(3)->maxLength(46)->alphaNumericWithSpaces()->maxSpaces(1);
        $emailValidator->required()->email();
        $phoneValidator->phone();
        if ($secondaryPhone !== null) {
            $secondaryPhoneValidator->phone();
        }
        $addressValidator->required()->minLength(3)->maxLength(255)->alphaNumericWithSecureSpecialChars();
        $usernameValidator->required()->minLength(3)->maxLength(255)->alphaNumeric();
        $passwordValidator->password();

        $errors = array_merge($namesValidator->getErrors(), $lastNamesValidator->getErrors(), $emailValidator->getErrors(), $phoneValidator->getErrors(), $addressValidator->getErrors(), $usernameValidator->getErrors(), $passwordValidator->getErrors());
        if ($password !== $passwordConfirmation) {
            $errors[] = 'La contraseña y la confirmación de contraseña no coinciden.';
        }

        if (!empty($errors)) {
            Responses::json(['errors' => $errors], 400);
        }

        // Verificar si el usuario ya existe en la base de datos
        $existingUser = UsersModel::userExists($username);
        if ($existingUser) {
            Responses::json(['errors' => ['El usuario "' . $username . '" ya existe']], 400);
        }

        // Verificar si el correo electrónico ya está en uso
        $existingEmail = UsersModel::emailExists($email);
        if ($existingEmail) {
            Responses::json(['errors' => ['El correo electrónico "' . $email . '" ya está en uso']], 400);
        }

        // Crear un nuevo usuario
        try {
            $createdUser = UsersModel::createUser(
                $username,
                $password,
                $names,
                $lastNames,
                $email,
                $phone,
                $secondaryPhone,
                $address
            );

            Responses::json([
                'message' => 'Usuario creado exitosamente',
                'user' => [
                    'username' => $createdUser->getUsername(),
                    'registerDate' => $createdUser->getRegisterDate(),
                    'names' => $createdUser->getNames(),
                    'lastNames' => $createdUser->getLastNames(),
                    'email' => $createdUser->getEmail(),
                    'phone' => $createdUser->getPhone(),
                    'secondaryPhone' => $createdUser->getSecondaryPhone(),
                    'address' => $createdUser->getAddress(),
                    'roleId' => $createdUser->getRoleId(),
                ]
            ], 201);
        } catch (Exception $e) {
            Responses::json(['errors' => ['Error al crear el usuario: ' . $e->getMessage()]], 500);
        }
    }

    // Devuelve los datos del usuario autenticado en base a su token JWT
    // ESTA RUTA REQUIERE AUTENTICACIÓN MEDIANTE @/middlewares/AuthMiddleware.php
    public static function user()
    {
        global $USER;
        // $USER es una variable global que contiene el nombre de usuario del usuario autenticado
        // esta variables se establece en el middleware de autenticación.

        if (!$USER) {
            Responses::json(['errors' => ['No tiene los permisos para acceder a esta ruta: Usuario no autenticado']], 401);
        }

        // Obtener los datos del usuario
        $user = UsersModel::getUser($USER);

        if (!$user) {
            Responses::json(['errors' => ['Usuario no encontrado']], 404);
        }

        // Devolver los datos del usuario en la respuesta
        $userData = [
            'username' => $user->getUsername(),
            'registerDate' => $user->getRegisterDate(),
            'names' => $user->getNames(),
            'lastNames' => $user->getLastNames(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'secondaryPhone' => $user->getSecondaryPhone(),
            'address' => $user->getAddress(),
            'roleId' => $user->getRoleId(),
        ];

        Responses::json(['user' => $userData], 200);
    }
}

?>