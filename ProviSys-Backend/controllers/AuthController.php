<?php
include_once 'models/UsersModel.php';
class AuthController
{
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

        $usernameValidator->required()->minLength(3)->maxLength(20)->alphaNumeric();
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
    public static function register()
    {
        require_once 'env.php';
        global $ENV;

        $password = 'password123456789'; // Contraseña de prueba

        $test_password = password_hash($password, PASSWORD_DEFAULT);

        Responses::json(['message' => 'Registro exitoso', 'test_password' => $test_password], 200);
    }

    public static function user()
    {
        global $USER;

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