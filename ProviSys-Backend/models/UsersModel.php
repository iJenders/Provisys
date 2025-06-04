<?php
include_once 'utils/DBConnection.php';

class UsersModel
{
    private $db;
    private $username;
    private $registerDate;
    private $names;
    private $lastNames;
    private $email;
    private $phone;
    private $secondaryPhone;
    private $address;
    private $roleId;

    public function __construct($username, $registerDate, $names, $lastNames, $email, $phone, $secondaryPhone, $address, $roleId)
    {
        $this->username = $username;
        $this->registerDate = $registerDate;
        $this->names = $names;
        $this->lastNames = $lastNames;
        $this->email = $email;
        $this->phone = $phone;
        $this->secondaryPhone = $secondaryPhone;
        $this->address = $address;
        $this->roleId = $roleId;

        // Obtener la conexión a la base de datos
        $this->db = DBConnection::getInstance()->getConnection();
    }

    // Getters
    public function getUsername()
    {
        return $this->username;
    }
    public function getRegisterDate()
    {
        return $this->registerDate;
    }
    public function getNames()
    {
        return $this->names;
    }
    public function getLastNames()
    {
        return $this->lastNames;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getPhone()
    {
        return $this->phone;
    }
    public function getSecondaryPhone()
    {
        return $this->secondaryPhone;
    }
    public function getAddress()
    {
        return $this->address;
    }
    public function getRoleId()
    {
        return $this->roleId;
    }

    public function getCredentials()
    {
        // Obtener las credenciales del usuario
        include_once 'models/CredentialsModel.php';
        return CredentialsModel::getCredential($this->username);
    }

    // Métodos estáticos
    public static function getUser($username)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query = "SELECT * FROM usuario WHERE nombre_usuario = '$username'";

        $result = $db->query($query);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            return new UsersModel(
                $user['nombre_usuario'],
                $user['fecha_registro'],
                $user['nombres'],
                $user['apellidos'],
                $user['correo_electrónico'],
                $user['telefono'],
                $user['telefono_secundario'],
                $user['direccion'],
                $user['id_rol']
            );
        } else {
            return null; // No se encontró el usuario
        }
    }

    public static function userExists($username)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query = "SELECT * FROM usuario WHERE nombre_usuario = '$username'";

        $result = $db->query($query);

        return $result->num_rows > 0; // Devuelve true si el usuario existe, false en caso contrario
    }

    public static function emailExists($email)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query = "SELECT * FROM usuario WHERE correo_electrónico = '$email'";

        $result = $db->query($query);

        return $result->num_rows > 0; // Devuelve true si el correo electrónico ya está en uso, false en caso contrario
    }

    public static function createUser($username, $password, $names, $lastNames, $email, $phone, $secondaryPhone = null, $address, $roleId = 2)
    {
        $db = DBConnection::getInstance()->getConnection();

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $registerDate = date('Y-m-d H:i:s');

        $startTransactionQuery = "START TRANSACTION";
        $commitTransactionQuery = "COMMIT";
        $rollbackTransactionQuery = "ROLLBACK";
        $insertCredentialQuery = "INSERT INTO credencial (nombre_usuario, password) VALUES ('$username', '$hashedPassword')";
        $insertUserQuery = "INSERT INTO usuario (nombre_usuario, fecha_registro, nombres, apellidos, correo_electrónico, telefono, telefono_secundario, direccion, id_rol) VALUES ('$username', '$registerDate', '$names', '$lastNames', '$email', '$phone', '$secondaryPhone', '$address', '$roleId')";

        $db->query($startTransactionQuery);
        $db->query($insertCredentialQuery);
        if ($db->query($insertUserQuery)) {
            $db->query($commitTransactionQuery);
            return new UsersModel($username, $registerDate, $names, $lastNames, $email, $phone, $secondaryPhone, $address, $roleId);
        } else {
            $db->query($rollbackTransactionQuery);
            throw new Exception("Error al crear el usuario: " . $db->error);
        }
    }
}