<?php
include_once 'utils/DBConnection.php';

class UsersModel
{
    private $db;

    private $userId;
    private $registerDate;
    private $names;
    private $lastNames;
    private $email;
    private $phone;
    private $secondaryPhone;
    private $address;
    private $role;

    public function __construct($userId = null, $registerDate = null, $names = null, $lastNames = null, $email = null, $phone = null, $secondaryPhone = null, $address = null, $role = null)
    {
        $this->userId = $userId;
        $this->registerDate = $registerDate;
        $this->names = $names;
        $this->lastNames = $lastNames;
        $this->email = $email;
        $this->phone = $phone;
        $this->secondaryPhone = $secondaryPhone;
        $this->address = $address;
        $this->role = $role;

        $this->db = DBConnection::getInstance()->getConnection();
    }

    // Getters y Setters

    public function getUserId()
    {
        return $this->userId;
    }
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
    public function getRegisterDate()
    {
        return $this->registerDate;
    }
    public function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;
    }
    public function getNames()
    {
        return $this->names;
    }
    public function setNames($names)
    {
        $this->names = $names;
    }
    public function getLastNames()
    {
        return $this->lastNames;
    }
    public function setLastNames($lastNames)
    {
        $this->lastNames = $lastNames;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function getPhone()
    {
        return $this->phone;
    }
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }
    public function getSecondaryPhone()
    {
        return $this->secondaryPhone;
    }
    public function setSecondaryPhone($secondaryPhone)
    {
        $this->secondaryPhone = $secondaryPhone;
    }
    public function getAddress()
    {
        return $this->address;
    }
    public function setAddress($address)
    {
        $this->address = $address;
    }
    public function getRole()
    {
        return $this->role;
    }
    public function setRole($role)
    {
        $this->role = $role;
    }

    // Métodos para interactuar con la base de datos
    public static function getUser($userId)
    {
        $db = DBConnection::getInstance()->getConnection();
        $query = "SELECT * FROM usuarios WHERE id_usuario = '$userId' OR correo_electronico = '$userId'";

        $result = $db->query($query);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user = new UsersModel(
                $user['id_usuario'],
                $user['fecha_registro'],
                $user['nombres'],
                $user['apellidos'],
                $user['correo_electronico'],
                $user['numero_celular'],
                $user['numero_celular_secundario'],
                $user['direccion'],
                $user['id_rol']
            );
            return $user;
        } else {
            return null; // No se encontró el usuario
        }
    }

    public static function userExists($userId)
    {
        $db = DBConnection::getInstance()->getConnection();
        $query = "SELECT * FROM usuarios WHERE correo_electronico = '$userId' OR id_usuario = '$userId'";

        $result = $db->query($query);

        if ($result->num_rows > 0) {
            return true; // El usuario existe
        } else {
            return false; // El usuario no existe
        }
    }

    public static function passwordMatch($userId, $password)
    {
        $db = DBConnection::getInstance()->getConnection();
        $query = "SELECT * FROM usuarios WHERE correo_electronico = '$userId' OR id_usuario = '$userId'";

        $result = $db->query($query);
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            $credentialsQuery = "SELECT * FROM credenciales WHERE id_usuario = '" . $user["id_usuario"] . "'";
            $credentialsResult = $db->query($credentialsQuery);

            if ($credentialsResult->num_rows > 0) {
                $credentials = $credentialsResult->fetch_assoc();
                $hashedPassword = $credentials["password"];

                // Verificar si la contraseña proporcionada coincide con la contraseña almacenada
                include_once 'env.php';
                global $ENV;

                $PASSWORD_HASH_SECRET = $ENV['PASSWORD_HASH_SECRET'];
                $PASSWORD_HASH_ALGO = $ENV['PASSWORD_HASH_ALGO'];

                $expectedHash = hash_hmac($PASSWORD_HASH_ALGO, $password, $PASSWORD_HASH_SECRET);
                if ($expectedHash !== $hashedPassword) {
                    return false; // Contraseña incorrecta
                }

                return true; // Contraseña correcta
            } else {
                return false; // No se encontraron credenciales para el usuario
            }
        } else {
            return false; // No se encontró el usuario
        }
    }
}