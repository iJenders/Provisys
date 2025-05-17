<?php
include_once 'models/UsersModel.php';
class AuthController
{
    public static function login()
    {
        // Lógica de inicio de sesión

        // Obtener los datos de inicio de sesión del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['userOrEmail']) || !isset($data['password'])) {
            Responses::json(['errors' => ['Faltan datos de inicio de sesión']], 400);
        }

        $userOrEmail = $data['userOrEmail'];
        $password = $data['password'];

        $userExists = UsersModel::userExists($userOrEmail);
        if ($userExists == null) {
            Responses::json(['errors' => ['Usuario o correo electrónico no encontrado']], 401);
        }

        $loginSuccess = UsersModel::passwordMatch($userOrEmail, $password);
        if ($loginSuccess === false) {
            Responses::json(['errors' => ['Usuario o contraseña incorrectos']], 401);
        }

        $user = UsersModel::getUser($userOrEmail);

        // Generar el token JWT
        require_once 'utils/JWT.php';

        global $ENV;
        $jwt_secret = $ENV['JWT_SECRET'];


        $payload = [
            'sub' => $user->getUserId(), // ID del usuario
            'iat' => time(), // Tiempo de emisión
            'exp' => time() + 3600, // Tiempo de expiración (1 hora)
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

        $PASSWORD_HASH_SECRET = $ENV['PASSWORD_HASH_SECRET'];
        $PASSWORD_HASH_ALGO = $ENV['PASSWORD_HASH_ALGO'];

        $password = '12345678'; // Contraseña de prueba

        $test_password = hash_hmac($PASSWORD_HASH_ALGO, $password, $PASSWORD_HASH_SECRET);

        Responses::json(['message' => 'Registro exitoso', 'test_password' => $test_password], 200);
    }
}

?>